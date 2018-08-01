<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "accomodation_news".
 *
 * @property integer $news_id
 * @property integer $accomodation_id
 * @property integer $lang_id
 * @property string $news_headline
 * @property string $news_text
 *
 * @property Accomodation $accomodation
 * @property LanguagesDb $lang
 */
class AccomodationNews extends \yii\db\ActiveRecord
{
    const CRYPT_TEMP_STRING = "@jYo&$6hYa7p*2";
    const CRYPT_NEWS_STRING = "kU*2+qY81H3LfP";
    const CRYPT_USER_STRING = "]Q9Ne3,@96H+|2";
    const CRYPT_IMAGE_STRING = 'a@1nf}|9^2Bl^l';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'accom_news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['accomodation_id', 'news_headline', 'news_text'], 'required'],
            [['accomodation_id', 'lang_id'], 'integer'],
            [['news_text'], 'string'],
            [['news_headline'], 'string', 'max' => 100],
            [['accomodation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Accomodation::className(), 'targetAttribute' => ['accomodation_id' => 'accomodation_id']],
            [['lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => LanguagesDb::className(), 'targetAttribute' => ['lang_id' => 'lg_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'news_id' => Yii::t('model/accomnews', 'news_id'),
            'accomodation_id' => Yii::t('model/accomnews', 'accomodation_id'),
            'lang_id' => Yii::t('model/accomnews', 'lang_id'),
            'news_headline' => Yii::t('model/accomnews', 'news_headline'),
            'news_text' => Yii::t('model/accomnews', 'news_text')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccomodation()
    {
        return $this->hasOne(Accomodation::className(), ['accomodation_id' => 'accomodation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLang()
    {
        return $this->hasOne(LanguagesDb::className(), ['lg_id' => 'lang_id']);
    }
    
    /*
     * Creates the temp directory for this user if it does not exist
     */
    public function createTempDirectoryIfNotExists() : array
    {
        //Gets the temp folder name
        $temp_folder_name = self::getNewsTempFolderName();
        //Image upload path
        $path = sprintf('%s/web/uploads/%s/news_temp/%s/', Yii::getAlias('@backend'), self::getUserFolderName(), $temp_folder_name);
        //Image src path
        $src_path = sprintf('%s/uploads/%s/news_temp/%s/', Yii::getAlias('@web'), self::getUserFolderName(), $temp_folder_name);
        if(!file_exists($path))
        {
            \yii\helpers\FileHelper::createDirectory($path, $mode = 0775, $recursive = true);
        }
        return [$path, $src_path];
    }
    
    public function getUserFolderName() : string
    {
        return md5(self::CRYPT_USER_STRING.Yii::$app->user->identity->user_id.self::CRYPT_USER_STRING);
    }
    
    /*
     * Crypts the user id with the crypt string
     */
    public function getNewsTempFolderName() : string
    {
        return md5(self::CRYPT_TEMP_STRING.Yii::$app->user->identity->user_id.self::CRYPT_TEMP_STRING);
    }
    
    public function getNewsFolderName($id) : string
    {
        return md5(self::CRYPT_NEWS_STRING.$id.self::CRYPT_NEWS_STRING);
    }
    
    public function createImageName(string $name) : string
    {
        $data = explode('.', $name);
        $data[0] = md5(self::CRYPT_IMAGE_STRING.$name.self::CRYPT_IMAGE_STRING);
        return implode('.', $data);
    }
    
    public function getTempDirectory() : string
    {
        $temp_folder_name = self::getNewsTempFolderName();
        return sprintf('%s/web/uploads/%s/news_temp/%s/', Yii::getAlias('@backend'), self::getUserFolderName(), $temp_folder_name);
    }
    
    /*
     * Creates the directory in the news folder of the current user
     * @param int $news_id -> News ID
     */
    public function createNewsDirectory(int $news_id) : string
    {
        $folder_name = self::getNewsFolderName($news_id);
        $path = sprintf('%s/web/uploads/%s/news/%s/', Yii::getAlias('@backend'), self::getUserFolderName(), $folder_name);
        if(!file_exists($path))
        {
            \yii\helpers\FileHelper::createDirectory($path, $mode = 0775, $recursive = true);
        }
        return $path;
    }
    
    public function getNewsDirectory($news_id)
    {
        return sprintf('news/%s/', self::getNewsFolderName($news_id));
    }
        
    
    public function validateImage(string $file, string $imageType, $tmp_name) : array
    {
        if($imageType != "jpg" && $imageType != "png" && $imageType != "jpeg" && $imageType != "gif" ) {
            return ['status'=> false, 'msg' => Yii::t('model/accomnews', 'invalid_extension')];
        }
        
        $imageinfo = getimagesize($tmp_name);
        if($imageinfo['mime'] != 'image/gif' && $imageinfo['mime'] != 'image/jpeg' && $imageinfo['mime'] != 'image/png' && $imageinfo['mime'] != 'image/jpg')
        {
            return ['status'=> false, 'msg' => self::t('invalid_extension')];
        }
        
        if(file_exists($file)){
            return ['status'=> false, 'msg' => Yii::t('model/accomnews', 'file_exists')];
        }
        
        return ['status' => true];
    }
    
    /*
     * Moves images to news dir
     * @param $news_id -> News ID
     */
    public function moveImagesToNewsDir(int $news_id) : bool
    {
        //Gets the temp dir name
        $temp_dir = self::getTempDirectory();
        if(file_exists($temp_dir)){
            //Create the news dir
            $news_dir = self::createNewsDirectory($news_id);
            $dir = new \DirectoryIterator($temp_dir);
            $success = true;
            //Moves all the images 
            foreach ($dir as $fileinfo) {
                if ($fileinfo->isFile()) {
                    if(!rename($temp_dir.$fileinfo->getBasename(), $news_dir.$fileinfo->getBasename()))
                    {
                        $success = false;
                    }
                }
            }
            //removes the temp dir
            \yii\helpers\FileHelper::removeDirectory($temp_dir);
            return $success;
        }
        else{
            return true;
        }
    }
    
    /*
     * Removes the news folder
     * @param $news_id -> News ID
     */
    public function deleteFolder(int $news_id) : bool
    {
        $folder_name = self::getNewsFolderName($news_id);
        $path = sprintf('%s/web/uploads/%s/news/%s/', Yii::getAlias('@backend'), self::getUserFolderName(), $folder_name);
        \yii\helpers\FileHelper::removeDirectory($path);
        return true;
    }
    
    /*
     * Replaces the url of the images with the correct value from news_temp/*string* to news/*string*
     * @param int $news_id -> News ID
     * @param string $news_text -> Text String
     * @return string
     */
    public function formatContent(int $news_id, string $news_text) : string
    {
        $path = sprintf('news_temp/%s/', self::getNewsTempFolderName());
        self::createNewsDirectory($news_id);
        $new_path = AccomodationNews::getNewsDirectory($news_id);
        return str_replace($path, $new_path, $news_text);
    }
    
    /*
     * Tries to move the images from the news_temp to the news dir
     * @param int $news_id -> News ID
     * @param object $model -> AccomNews model
     * @return boolean
     */
    public function moveImagesAndSave(int $news_id, $model) : bool
    {
        if(!self::moveImagesToNewsDir($news_id))
        {
            $model->delete();
            \common\components\Msg::warning(Yii::t('model/accomnews', 'internal_error'));
            return false;
        }
        else
        {
            return true;
        }
    }
}
