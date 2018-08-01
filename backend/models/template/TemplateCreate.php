<?php

namespace backend\models\template;

use Yii;
use yii\helpers\FileHelper;
use common\components\Msg;

trait TemplateCreate
{
    /*
     * Reads the template data from the .ini file
     * @param array $ini -> The data from the file
     */
    public static function newTempFromIni(array $ini, $model, $xml = []) : bool 
    {
        if(isset($ini['temp_name']) && isset($ini['temp_desc']) && isset($ini['temp_version']))
        {
            //Creates the new model
            $model = self::setNewModel($ini, $model);
            //Checks if the name already exists
            if($model !== false)
            {
                $ini['version_desc'] = !isset($ini['version_desc']) ? '' : $ini['version_desc'];
                return self::templateNameUniqueAndModelSaveSuccess($model, $xml,  $ini['version_desc']);
            }
            else{
                return false;
            }
        }
        else{
            return self::returnError(['ini_settings_missing'], [self::tempPath()]);
        }
    }
    
    private static function templateNameUniqueAndModelSaveSuccess($model, $xml, $ini_version_desc) : bool
    {
        if(self::nameUnique($model->name))
        {
            if($model->save()){
                //$ini_version_desc = !isset($ini_version_desc) ? '' : $ini_version_desc;
                //Creates the model for the template_versions table
                $versions = self::createVersionModel($model->template_id, $model->version, $ini_version_desc);
                if(!$versions->save())
                {
                    $model->delete();
                    return self::returnError(['internal_error', ['error_code' => '101']], [self::tempPath()]);
                }
                //explodes the version to get the first digit ex(from 1.4.6 it gets the 1)
                $version_for_crypt = self::explodeVersion($model->version)[0];
                //Creates the dir for the template /template_id/version(1)
                return self::moveToMainAndSaveXML($model, $xml, $version_for_crypt);
            }
            else{
                return self::returnError(['internal_error', ['error_code' => '102']], [self::tempPath()]);
            }
        }
        else{
             return self::returnError(['name_exists', ['error_code', '106']], [static::tempPath()]);
        }
    }
    
    private static function moveToMainAndSaveXML($model, $xml, $version_for_crypt) : bool
    {
        $templateDir = self::createTemplateDir($model->name, $version_for_crypt);
        if($templateDir)
        {
            //Moves the files from the temp directory to the main dir
            if(self::moveToMain($model, $templateDir, 1))
            {
                //$xml_options_creator = new \backend\components\template\XMLOptionsCreator($xml, $model->name);
                return !empty($xml) ? self::saveFromXML($xml, $templateDir, $model) : true;
            }
        }
        else{
            return self::returnError(['internal_error', ['error_code' => '102']], [self::tempPath()]);
        }
    }
    
    private static function saveFromXML($xml, $templateDir, $model) : bool
    {
        if(!(new \backend\components\template\xml\XMLOptionsCreate($xml, $model->name))->createTable())
        {
            $model->delete();
            return self::returnError(['xml_table_failed', ['error_code' => '101']], [self::tempPath(), $templateDir]);
        }
        return true;
    }
    
    /*
     * @parent newTempFromIni
     * Sets the data for the new model
     * @param string $name -> Template name
     * @param string $desc -> Template description
     * @param object $model -> Model
     * @param string $version -> Version
     */
    private function setNewModel(array $ini, $model)
    {
        $version = self::explodeVersion($ini['temp_version']);
        $model->name = $ini['temp_name'];
        $model->description = $ini['temp_desc'];
        $model->user_id = Yii::$app->user->identity->user_id;
        if($version !== false)
        {
            $model->version = implode(".", $version);
        }
        else{
            return self::returnError(["version_invalid", ['error_code' => '102']], [self::tempPath()]);
        }
        $model->path = 'a';
        return $model;
    }
}

