<?php

namespace backend\models;

use Yii;
use yii\helpers\Html;
use backend\models\GalleryImages;

class Galleries extends \backend\models\LangDb
{
    //Gallery crypt string
    const CRYPT_STRING = 'du%9jUt@*^1%]';
    //Accom folder crypt string
    const CRYPT_ACCOM_STRING = '*f5)l".?2A}1';
    
    private $allowedImageTypes = ['jpg', 'gif', 'jpeg', 'png'];
    
    
    public function rulesCustom() : array
    {
        return [
            [['accomodation_id', 'gallery_name'], 'required'],
            [['accomodation_id', 'lang_id', 'parent_id'], 'integer'],
            [['gallery_description'], 'string'],
            [['gallery_name'], 'string', 'max' => 100],
            [['accomodation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Accomodation::className(), 'targetAttribute' => ['accomodation_id' => 'accomodation_id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'gallery_id' => static::t('gallery_id'),
            'accomodation_id' => static::t('accom_id'),
            'lang_id' => static::t('lang_id'),
            'parent_id' => static::t('parent_id'),
            'gallery_name' => static::t('name'),
            'gallery_description' => static::t('desc'),
        ];
    }
    
     public static function viewAttributes() : array
    {
        return [['attribute' => 'accomodation_id', 'value' => function($model){return Accomodation::findOne(['accomodation_id' => $model->accomodation_id])['name'];}] ,'gallery_name', 'gallery_description']; 
    }

    public static function translateFields() : array
    {
        return ['gallery_name', 'gallery_description']; 
    }
    
    public static function titleAttribute() : string
    {
        return 'gallery_name';
    }
    
    /*
     * Creates the gallery directory
     * @param $gallery_name -> Gallery Name
     * @param $accomodation_name -> Accomodation Name
     */
    public function createGalleryDirectory(string $gallery_name, string $accomodation_name) : bool
    {
        $path = self::createDirPath($accomodation_name, $gallery_name);
        return \yii\helpers\FileHelper::createDirectory($path, $mode = 0775, $recursive = true) ? true : false;
    }
    
    /*
     * Returns the gallery directory based on the parent accomodation and gallery name
     * @param $accomodation_name -> Accomodation Name
     * @param $gallery_name -> Gallery Name
     * @return string
     */
    public function getGalleryDirectory(string $accomodation_name, string $gallery_name) : string
    {
        return self::createDirPath($accomodation_name, $gallery_name);
    }
    
    /*
     * Crypts the accomodation and gallery folder with unique strings, and returns the path of the gallery
     * @param $accomodation_name -> Accomodation Name
     * @param $gallery_name -> Gallery Name
     * @return string
     */
    private function createDirPath(string $accomodation_name, string $gallery_name) : string
    {
        $accom_folder_name = md5($accomodation_name.self::CRYPT_ACCOM_STRING);
        $path = Yii::getAlias('@backend')."/web/uploads/gallery/".$accom_folder_name;
        $name = md5($gallery_name.self::CRYPT_STRING);
        $path .= '/'.$name;
        return $path;
    }
    
    /*
     * Creates the url for the images
     * img src uses @web alias
     * @param $accomodation_name -> Accomodation Name
     * @param $gallery_name -> Gallery Name
     * @return string
     */
    public function imageUrl(string $accomodation_name, string $gallery_name) : string
    {
        $accom_folder_name = md5($accomodation_name.self::CRYPT_ACCOM_STRING);
        $path = Yii::getAlias('@web')."/uploads/gallery/".$accom_folder_name;
        $name = md5($gallery_name.self::CRYPT_STRING);
        $path .= '/'.$name;
        return $path;
    }
    
    /*
     * Creates the glyphs for gallery view
     * @param $id -> Galler ID
     * @param $view -> URL(view, delete, update)
     */
    public function generateGlyph(int $id, string $view) : string
    {
        switch($view)
        {
            case 'view':
                $class = 'eye-open';
            break;
            
            case 'delete':
                $class = 'trash';
            break;
        
            case 'update':
                $class = 'pencil';
            break;
            
            default:
                $class = '';
            break;
        }
        
        return $class != '' ? self::glyph($view, $class, $id) : '';
    }
    
    /*
     * @param $id -> Gallery ID
     * @viewParent -> Parent ID
     */
    public function generateViewActions(int $id, int $parent_id = 0) : string
    {
        //If the parent ID is null, the options are set for the delete button so it calls the delete modal and for update
        if($parent_id == 0)
        {
            $delete_p = ['data-url' => 'delete-gallery', 'data-id' => $id, 'class' => 'confirm-delete trigger-btn', 'data-toggle' => 'modal'];
            $delete = Html::a(Html::tag('i', '', ['class' => 'glyphicon glyphicon-trash']), '#myModal', $delete_p);
            $update = 'update';
            $db_lang = 1;
        }
        //If the parent ID is not null, the options are set for delete button so it deletes the translation, also for the update
        else{
            $db_lang = self::getLangParam();
            $delete = self::glyph('delete-translation', 'trash', $id, $db_lang, ['data-method' => 'post']);
            $update = 'update-translation';
        }
        //If the $parent_id(Parent ID) is 0, than the glyph url will contain the original value
        $viewId = $parent_id == 0 ? $id : $parent_id;
        $actions = self::glyph('gallery-details', 'eye-open', $viewId, $db_lang);
        $actions .= self::glyph($update, 'pencil', $id, $db_lang);
        $actions .= $delete;
        return $actions;
    }
    
    /*
     * @param $view -> URL to where it leads
     * @param $class -> Name of the glyph class ex 'pencil', or 'trash'
     * @param $id -> Gallery ID
     * @db_lang -> DB Lang
     * @data_method -> For delete glyph, so it uses POST
     * @return string
     */
    private function glyph(string $view, string $class, int $id, int $db_lang, array $data_method = []) : string
    {
        return sprintf('%s ', Html::a(Html::tag('i', '', ['class' => sprintf('glyphicon glyphicon-%s', $class)]), [$view, 'id' => $id, 'db_lang' => $db_lang], $data_method));
    }
    
    /*
     * Check if the db_lang is default
     */
    public function isDefault() : bool
    {
        //Ovde sad stoji 1, nisam mogao da iskoristim default_dblang_id, probao sam sa self::default_dblang_id ali nije htelo
        return 1 == self::getLangParam() ? true : false;
    }
    
    /*
     * Searches for the translated rows for every gallery which belong to this accomodation ( $id )
     * @param $id -> Accomodation ID
     */
    public function getTranslatedRows(int $id) : array
    {
        //Finds the orinal values
        $original = self::find()->where(['accomodation_id' => $id])->asArray()->all();
        $db_lang = self::getLangParam();
        foreach($original as $key => $val)
        {
            //Loops through those values, searching if the translated row exists based and gallery_id and db_lang from URL
            $translated = self::find()->where(['parent_id' => $val['gallery_id'], 'lang_id' => $db_lang])->asArray()->one();
            //If the translated value exists, replaces the original with the translation
            if(!empty($translated))
            {
                $original[$key] = $translated;
            }
        }
        return $original;
    }
    
    /*
     * Looks for the translated value of the gallery, if it does not exists, returns the original
     * @param $id -> Gallery ID
     */
    public function getTranslatedGallery(int $id) : array
    {
        $translated = self::find()->where(['parent_id' => $id, 'lang_id' => self::getLangParam()])->asArray()->one();
        if(empty($translated))
        {
            $translated = self::find()->where(['gallery_id' => $id])->asArray()->one();
        }
        return $translated;
    }
    
    /*
     * Check if the image name already exists in the folder, or if the image extensions is valid
     * @param $file -> Image path
     * @param $imageType -> Image extension
     * @return array
     */
    public function validateImage(string $file, string $imageType, $tmp_name) : array
    {
        if($imageType != "jpg" && $imageType != "png" && $imageType != "jpeg" && $imageType != "gif" ) {
            return ['status'=> false, 'msg' => self::t('invalid_extension')];
        }
        $imageinfo = getimagesize($tmp_name);
        if($imageinfo['mime'] != 'image/gif' && $imageinfo['mime'] != 'image/jpeg' && $imageinfo['mime'] != 'image/png' && $imageinfo['mime'] != 'image/jpg')
        {
            return ['status'=> false, 'msg' => self::t('invalid_extension')];
        }
        if(file_exists($file)){
            return ['status'=> false, 'msg' => self::t('file_exists')];
        }
        
        return ['status' => true, 'msg' => ''];
    }
    
    /*
     * Removes everything but letters and numbers from the name and replaces with underscore
     * @param string $name -> Image name
     * @return string
     */
    public function cleanImageName(string $name) : string
    {
        $fileNameData = explode('.', $name);
        $fileNameData[0] = preg_replace('/[^\w]/', '_', $fileNameData[0]);
        $fileNameData[0] = str_replace('__', '_', $fileNameData[0]);
        return implode('.', $fileNameData);
    }
    
    /*
     * Crates the return array for the jQuery upload library
     * @param $image_id -> ID of the image in the GalleryImages table
     * @param $gallery_name -> Gallery Name
     * @param $accomodation_name -> Accomodation Name
     * @param $files -> $_FILES
     * @param $file -> Full image path
     * @return array
     */
    public function createReturnArray(int $image_id, string $gallery_name, string $accomodation_name, array $files, string $file) : array
    {
        $return = [
                    'deleteType' => 'POST',
                    //Creates the delete URL 
                    'deleteUrl' => sprintf('delete-image?im_id=%s&gal_name=%s&ac_name=%s', $image_id, $gallery_name, $accomodation_name), 
                    'name' => $files['files']['name'][0],
                    'thumbnailUrl' => self::imageUrl($accomodation_name, $gallery_name).'/'.$files['files']['name'][0],
                    'type' => $files['files']['type'][0],
                    'size' => $files['files']['size'][0],
                    'url' => $file
                ];
        return $return;
    }
    
    /*
     * Deletes all files from the gallery directory
     * @param $dir -> Gallery directory
     */
    public function deleteAllFromDir(string $dir)
    {
        $di = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS);
        $ri = new \RecursiveIteratorIterator($di, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ( $ri as $file ) {
            $file->isDir() ?  rmdir($file) : unlink($file);
        }
    }
    
    public function redirectOnDelete(int $accomodation_id, int $db_lang) : array
    {
         if(!empty(self::findOne(['accomodation_id' => $accomodation_id])))
         {
             return ['view-galleries', 'id' => $accomodation_id, 'db_lang' => $db_lang];
         }
         return ['index', 'db_lang' => $db_lang];
    }
    
    /*
     * @param $id -> Gallery ID
     */
    public function getGalleryName(int $id) : string
    {
        return self::find()->where(['gallery_id' => $id])->asArray()->one()['gallery_name'];
    }
    
    public function getAccomodationName($id) : string
    {
        $accomodation_id = self::find()->where(['gallery_id' => $id])->asArray()->one()['accomodation_id'];
        return Accomodation::find()->where(['accomodation_id' => $accomodation_id])->asArray()->one()['name'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccomodation()
    {
        return $this->hasOne(Accomodation::className(), ['accomodation_id' => 'accomodation_id']);
    }
}
