<?php

namespace backend\models;

use Yii;
use \common\components\Msg;
use yii\helpers\FileHelper;
/**
 * This is the model class for table "templates".
 *
 * @property integer $template_id
 * @property integer $lang_id
 * @property integer $parent_id
 * @property string $name
 * @property string $description
 * @property string $path
 * @property int $user_id
 * @property int $version
 *
 * @property LanguagesDb $lang
 */
class Templates extends LangDb
{
    use template\TemplateFileHelper;
    use template\TemplateValidator;
    use template\TemplateCreate;
    use template\TemplateUpdate;
    
    
    const ZIP_TEMP_CRYPT      = 'k(2H@1Lun*';
    const ZIP_NAME_CRYPT      = '&2(1j3Ob[>,';
    const TEMPLATE_DIR_CRYPT  = '@b*3Kw~b}p^%';
    const VERSION_DIR_CRYPT   = "nK7d;@n*ng02J(";
    const ACTION_TEMP_UPGRADE = 'UPGRADE';
    const ACTION_TEMP_UPDATE  = 'UPDATE';

    /**
     * @inheritdoc
     */
    public function rulesCustom() : array
    {
        return [
            [['lang_id', 'user_id'], 'integer'],
            [['user_id', 'name', 'description', 'path'], 'required'],
            [['description', 'path'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['name'], 'unique'],
            [['version'], 'string', 'max' => 10],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'template_id' => self::t('template_id'),
            'lang_id' => self::t('lang_id'),
            'parent_id' => self::t('parent_id'),
            'name' => self::t('name'),
            'description' => self::t('description'),
            'path' => self::t('path'),
            'user_id' => self::t('user_id'),
            'version' => self::t('version'),
        ];
    }
    
    public static function viewAttributes() : array
    {
        return ['name', 'description', 'path', 'version'];
    }

    public static function translateFields() : array
    {
        return ['name', 'description'];
    }
    
    public static function titleAttribute() : string
    {
        return 'name';
    }
    
    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['templateUpdate'] = ['path'];
        return $scenarios;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }
    
    /*
     * Creates the model for the new TemplateVersions
     */
    private function createVersionModel(int $template_id, string $template_version, string $version_desc = '') : TemplateVersions
    {
         $versions = new TemplateVersions();
         $versions->template_id = $template_id;
         $versions->version = $template_version;
         $versions->version_description = $version_desc !== '' ? $version_desc : '--No description for this version--';
         return $versions;
    }
    
    /*
     * Sets the flash error message
     */
    private function returnError(array $msg, array $dirs_for_delete = []) : bool
    {
        if(!empty($dirs_for_delete))
        {
            foreach($dirs_for_delete as $dir)
            {
                FileHelper::removeDirectory($dir);
            }
        }
        isset($msg[1]) ? Msg::error(self::t($msg[0], $msg[1])) : Msg::error(self::t($msg[0]));
        return false;
    }
    
    /*
     * @parent moveToMain
     * Sets the path of the model to the main template dir and attempts to save the model
     * @param object $model -> Model
     * @param string $dir -> Main directory
     */
    private function finalizeInsert(Templates $model, string $dir) : bool
    {
        Msg::success('Template created!');
        $model->path = $dir;
        if($model->save())
        {
            FileHelper::removeDirectory(self::tempPath());
            Msg::success("Template creation finalized.");
            return true;
        }
        else{
            FileHelper::removeDirectory(self::tempPath());
            FileHelper::removeDirectory(self::createTemplateDir($model->name, $model->version));
            $model->delete();
            return self::returnError(["Error finalizing template creation. Rolling back changes. Please try again."]);
        }
    }
    
    public static function removeOptionsTable($table_name)
    {
        if(Yii::$app->db->schema->getTableSchema($table_name) !== null){
            Yii::$app->db->createCommand(sprintf('DROP TABLE `%s`',$table_name))->execute();
        }
    }
    
    /*
     * ----------------------MODELS END-----------------------------------------
     */
}
