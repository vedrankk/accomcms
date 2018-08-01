<?php

namespace backend\components\template;

use Yii;
use yii\helpers\FileHelper;
use common\components\Msg;

class TemplateCreator extends FileHelper
{
    private $temp_dir;
    private $temp_zip_name;
    private $model;
    private $allowed_extensions = ['html', 'php', 'js', 'css', 'ini', 'jpg', 'jpeg', 'png', 'gif', 'json', 'txt', 'scss', 'xml'];
    
    /*
     * @param string $temp_dir -> The dir containing the files
     * @param string $temp_zip_name -> The name of the zip file
     */
    public function __construct($model, $temp_dir, $temp_zip_name)
    {
        $this->model = $model;
        $this->temp_dir = $temp_dir;
        $this->temp_zip_name = $temp_zip_name;
    }
    
    /*
     * For creating the new template
     */
    public function newTemplate()
    {
        $allowed = $this->validateDirContent();
        
        if(!$allowed)
        {
            self::removeDirectory($this->temp_dir);
            return false;
        }
        else{
            libxml_use_internal_errors(true);
            $xmlValidator = new \backend\components\template\xml\XMLValidator($this->temp_dir);
            $xml = [];
            if($xmlValidator->xmlFileExists() && $xmlValidator->validateOptionsContent())
            {
                $xml = $xmlValidator->getXmlData();
            }
            
            //Detects the .ini file
            return $this->iniExists() && $this->validIni() ? \backend\models\Templates::newTempFromIni($this->readIni(), $this->model, $xml) : false;
        }
    }
    
    /*
     * For updating the existing template
     */
    public function updateTemplate()
    {
        $allowed = $this->validateDirContent();
        if(!$allowed)
        {
            self::removeDirectory($this->temp_dir);
            return false;
        }
        else{
            if($this->iniExists() && $this->validIni())
            {
                $ini = $this->readIni();
                libxml_use_internal_errors(true);
                $xmlValidator = new \backend\components\template\xml\XMLValidator($this->temp_dir);
                $xml = [];
                if($xmlValidator->xmlFileExists() && $xmlValidator->validateOptionsContent())
                {
                    $xml = $xmlValidator->getXmlData();
                }
                return $this->validateIniData($ini) && $this->validateNameFromIni($ini['temp_name']) ? \backend\models\Templates::updateTemp($ini, $this->model, $xml) : false;
            }
            else{
                return false;
            }
        }
    }
    
    /*
     * Validate if all the data that is neccesary exists in the .ini file
     */
    private function validateIniData($ini)
    {
        if(isset($ini['temp_name']) && !empty($ini['temp_name']) && isset($ini['temp_desc']) && !empty($ini['temp_desc']) && isset($ini['temp_version']) && !empty($ini['temp_version']))
        {
            return true;
        }
        else{
            self::removeDirectory($this->temp_dir);
            Msg::error($this->model->t('ini_params_missing', ['error_code' => '402']));
            return false;
        }
    }
    
    /*
     * Only the .ini of the same name could be uploaded
     */
    private function validateNameFromIni($name)
    {
        if($name != $this->model->name)
        {
            Msg::error($this->model->t('name_mismatch', ['error_code' => '403']));
            return false;
        }
        return true;
    }
    
    /*
     * Checks the files in the dir to see if everything is valid
     */
    private function validateDirContent()
    {
        unlink($this->temp_dir.$this->temp_zip_name);
        //Loops all the files to check the extensions
        $allowed = $this->loopFiles();
        return $allowed;
    }
    
    /*
     * Loops the files from the temp folder and check if they are valid
     */
    private function loopFiles()
    {
        $files = self::findFiles($this->temp_dir);
        //Loops all the files to check the extensions
        foreach($files as $file)
        {
            $file_ext =  pathinfo($file, PATHINFO_EXTENSION);
            $allowed = false;
            foreach($this->allowed_extensions as $ext)
            {
                //If the extensions exists, sets the allowed to true and breaks the loop
                if($file_ext == $ext)
                {
                    $allowed = true;
                    break;
                }
            }
            //If there is an unallowed extension, like .exe, removes the whole dir and breaks the loop
            if(!$allowed)
            {
                Msg::warning($this->model->t('invalid_files'));
                self::removeDirectory($this->temp_dir);
                break;
            }
        }
        return $allowed;
    }
        
    /*
     * Error message for the ini missing
     */
    private function iniMissing()
    {
         Msg::error($this->model->t('ini_missing'));
         self::removeDirectory($this->temp_dir);
         return false;
    }
    
    private function returnError($msg)
    {
        Msg::error($this->model->t($msg));
        self::removeDirectory($this->temp_dir);
        return false;
    }
    
    /*
     * Checks if the ini exists
     */
    private function iniExists()
    {
        return file_exists($this->temp_dir.'settings.ini') ? true : $this->returnError('ini_missing');
    }
    
    private function validIni()
    {
        return @parse_ini_file($this->temp_dir.'settings.ini') !== false ? true : $this->returnError('invalid_ini');
    }
    
    /*
     * Reads the ini file
     */
    private function readIni()
    {
        return parse_ini_file($this->temp_dir.'settings.ini');
    }
    
    private function xmlOptionsExist()
    {
        return file_exists($this->temp_dir.'options.xml') ? true : false;
    }
    
    private function readXml()
    {
        return simplexml_load_file($this->temp_dir.'options.xml');
    }
    
    private function invalidXml()
    {
        Msg::error('Invalid XML format!');
        self::removeDirectory($this->temp_dir);
        return false;
    }
}
