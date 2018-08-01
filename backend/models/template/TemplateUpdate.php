<?php

namespace backend\models\template;

use Yii;
use yii\helpers\FileHelper;
use common\components\Msg;

trait TemplateUpdate
{
    /*
     * Regulates the update of the template
     */
    public function updateTemp(array $ini, $model, $xml = []) : bool
    {
        //Checks if all the neccesary data is in the .ini file
        if(isset($ini['temp_name']) && isset($ini['temp_desc']) && isset($ini['temp_version']))
        {
            if(self::validateNameFromIni($ini['temp_name'], $model->name)){
                return self::updateStatusValid($ini, $model, $xml);
            }
            else{
                return self::returnError(['name_mismatch', ['error_code' => '404']], [self::tempPath()]);
            }
        }
        else{
            return self::returnError(['ini_settings_missing'], [self::tempPath()]);
        }
    }
    
    private static function updateStatusValid($ini, $model, $xml) : bool
    {
        $status = self::validateVersion($ini['temp_version'], $model->version);
        if($status)
        {
            $same_version = isset($status['same']) ? true : false;
            //If everything is valid
            if($status['status'])
            {
                
               return self::switchUpdateAction($status, $ini, $model, $xml, $same_version);
            }
            else{
                return self::returnError(['version_invalid', ['error_code' => '201']], [self::tempPath()]);
            }
        }
        else{
            return self::returnError(['version_invalid', ['error_code' => '202']], [self::tempPath()]);
        }
    }
    
    private static function switchUpdateAction($status, $ini, $model, $xml, $same_version) : bool
    {
        //Regulates the action, if the current version is 1.4.6 and we are uploading 1.5.7 that is is UPDATE
        //If the new version is 2.0.0 than the action is UPGRADE
        switch($status['action'])
        {
            case self::ACTION_TEMP_UPGRADE: 
                return self::upgradeTemplate($ini, $model, $xml, $status['version']);
            break;

            case self::ACTION_TEMP_UPDATE:
                return self::updateTemplate($ini, $model, $status['version'], $xml, $same_version);
            break;
        }
    }
    
     /*
     * Finished the upgrading of the template
     * @param $crypt_version -> The first digit of the version from the .ini
     */
    private function upgradeTemplate(array $ini, $model, $xml, string $crypt_version) : bool
    {
        //Saves old data in case of failure
        $old_model_data = self::setOldModelData($model);
        //Sets the new data fro the model
        $model = self::setUpdateModel($ini, $model, $crypt_version);
        //Tries to move new files to main dir and save model
        if($model !== false)
        {
            if(self::moveToMain($model, $model->path, 2) && $model->save())
            {
                if(!isset($ini['version_desc']))
                {
                    $ini['version_desc'] = '';
                }
                //Adds a row in the template_versions table with the data from the newest version
                self::versionUpdate($model->template_id, $model->version, $ini['version_desc']);
                //Removes the temp dir
                FileHelper::removeDirectory(self::tempPath());
                Msg::success($model->t('temp_upgraded', ['version' => $model->version]));
                self::updateXMLTable($xml, $model->name);
                return true;
            }
            else{
                //Sets the model with the old data
                $model = self::getOldModelData($model, $old_model_data);
                $model->save();
                return self::returnError(['internal_error', ['error_code' => '103']]);
            }
        }
        else{
            return false;
        }
    }
    
    private static function updateXMLTable($xml, $name)
    {
        if(!empty($xml))
        {
            $val = new \backend\components\template\xml\XMLOptionsUpdate($xml, $name);
            $val->updateTable();
        }
    }
    
    /*
     * Finishes the updating of the template
     * @param $crypt_version -> The first digit of the version from the .ini
     * @param $same_version -> If the new version is the same as the current one
     */
    private function updateTemplate(array $ini, $model, string $crypt_version, $xml, bool $same_version = false) : bool
    {
        //Saves old data in case of failure
        $old_model_data = self::setOldModelData($model);
        //Sets the new data fro the model
        $model = self::setUpdateModel($ini, $model, $crypt_version);
        
        //Creates backup of the current main folder in case of failure
        if($model !== false)
        {
            if(self::createBackup($model->name, $crypt_version, $model->path) && $model->save())
            {
                //Deletes all from current main folder
                self::deleteAllFromDir($model->path);
                //Tries to move files into the main dir
                if(self::moveToMain($model, $model->path, 2) && $model->save())
                {
                    if(!isset($ini['version_desc']))
                    {
                        $ini['version_desc'] = '';
                    }
                    //Adds the row in the template_versions table. If the versions are the same, than only overwrites the description if there is a difference
                    if(self::versionUpdate($model->template_id, $model->version, $ini['version_desc'], $same_version))
                    {
                        FileHelper::removeDirectory(self::tempPath());
                        FileHelper::removeDirectory(self::getBackupDirRoot($model->name));
                        Msg::success($model->t('update_success'));
                        self::updateXMLTable($xml, $model->name);
                        return true;
                    }
                    else{
                        //If the row in the template_versions table was not updated or added, reverts back all the changes
                        self::deleteAllFromDir($model->path);
                        FileHelper::copyDirectory(self::getBackupDir($model->name, $crypt_version), $model->path);
                        $model = self::getOldModelData($model, $old_model_data);
                        $model->save();
                        return self::returnError(['internal_error', ['error_code' => '104']], [self::getBackupDirRoot($model->name), self::tempPath()]);
                    }
                }
                else{
                    //Copies the backed up dir back to main folder
                    FileHelper::copyDirectory(self::getBackupDir($model->name, $crypt_version), $old_model_data['path']);
                    //Returns the old data to the model and saves
                    $model = self::getOldModelData($model, $old_model_data);
                    $model->save();
                    return self::returnError(['internal_error', ['error_code' => '104']], [self::getBackupDirRoot($model->name), self::tempPath()]);
                }

            }
            else{
                return self::returnError(['internal_error', ['error_code' => '105']], [self::getBackupDirRoot($model->name), self::tempPath()]);
            }
        }
        else{
            return false;
        }
        
    }
    
    /*
     * Regulates the template_versions table
     * @param $same_version -> If the new version is the same as the current
     */
    private static function versionUpdate(int $template_id, string $template_version, string $version_desc = '', bool $same_version = false) : bool
    {
        //Finds the data for the current version and updates the descrption if it was changed
        if($same_version)
        {
            $version = \backend\models\TemplateVersions::find()->where(['version' => $template_version, 'template_id' => $template_id])->one();
            if(!empty($version_desc))
            {
                $version->version_description = $version_desc;
                return $version->save() ? true : false;
            }
            return true;
        }
        else{
            //Creates new model and saves it
            $version = self::createVersionModel($template_id, $template_version, $version_desc);
            return $version->save() ? true : false;
        }
    }
    
    private function getOldModelData(Templates $model, array $old_model) : object
    {
        foreach($old_model as $key => $val)
        {
            $model->{$key} = $val;
        }
        return $model;
    }
    
    private function setOldModelData($model) : array
    {
        $path = self::find()->where(['template_id' => $model->template_id])->asArray()->one()['path'];
        return ['name' => $model->name, 'description' => $model->description, 'version' => $model->version, 'path' => $path];
    }
    
    /*
     * Sets the data in the model when updaing
     * @param array $ini -> Array of data from the .ini file
     * @param object $model -> Model
     */
    private function setUpdateModel(array $ini, $model, int $crypt_version)
    {
        $version = self::explodeVersion($ini['temp_version']);
        $model->name = $model->name == $ini['temp_name'] ? $model->name : $ini['temp_name'];
        $model->description = $model->description == $ini['temp_desc'] ? $model->description : $ini['temp_desc'];
        if($version !== false)
        {
            $model->version = implode(".", $version);
        }
        else{
            return self::returnError("Invalid version format! It has to be A.B.C or A,B,C", [self::tempPath()]);
        }
        $model->path = self::createTemplateDir($model->name, $crypt_version);
        return $model;
    }
}

