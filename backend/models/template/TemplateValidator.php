<?php

namespace backend\models\template;

trait TemplateValidator
{
    /*
     * Validates the type and name of the zip
     */
    public static function validateZip(string $type, string $name) : bool 
    {
        
        $acceptedTypes = ['application/octet-stream', 'application/x-zip-compressed'];
        $validType = false;
        
        foreach($acceptedTypes as $types)
        {
            if($types == $type)
            {
                $validType = true;
                break;
            }
        }
        if(!$validType)
        {
            return static::returnError(['zip_type_invalid', ['error_code' => '301']]);
        }
        
        if(pathinfo(static::tempPath().$name, PATHINFO_EXTENSION) != 'zip')
        {
            return static::returnError(['zip_type_invalid', ['error_code' => '302']]);
        }
       
        $zip_path = static::fileExists(static::tempPath(), $name);
        
        return $zip_path;
    }
    
    
    
    /*
     * @param string $filePath -> The path to the temp dir
     * @param string $fileName -> The name of the zip
     */
    private function fileExists(string $filePath, string $fileName) : bool
    {
        $newFileName = static::zipCryptName($fileName);
        if(file_exists($filePath.$newFileName))
        {
             return static::returnError(['zip_exists', ['error_code' => '303']]);
        }
        return true;
    }
    
    /*
     * If the name already exists in the DB removes the temp dir
     */
    private function nameExists()
    {
        return static::returnError(['name_exists', ['error_code', '106']], [static::tempPath()]);
    }
    
    /*
     * Check if the template name is unique
     * @param string $name -> Template Name
     */
    private function nameUnique(string $name) : bool
    {
        if(!empty(static::find()->where(['name' => $name])->asArray()->one()))
        {
            return false;
        }
        return true;
    }
    
    private function validateVersion(string $ini_version, string $model_version) : array
    {
        $ini = static::explodeVersion($ini_version);
        $model = static::explodeVersion($model_version);
        if($ini && $model)
        {
            if(intval($ini[0]) > intval($model[0]))
            {
                return ['status' => true, 'action' => static::ACTION_TEMP_UPGRADE, 'version' => $ini[0]];
            }
            elseif(intval($ini[0]) === intval($model[0]))
            {
                if(intval($ini[1]) > intval($model[1]))
                {
                    return ['status' => true, 'action' => static::ACTION_TEMP_UPDATE, 'version' => $ini[0]];
                }
                elseif(intval($ini[1]) === intval($model[1]))
                {
                    if(intval($ini[2]) > intval($model[2]))
                    {
                        return ['status' => true, 'action' => static::ACTION_TEMP_UPDATE, 'version' => $ini[0]];
                    }
                    elseif(intval($ini[2]) === intval($model[2])){
                        return ['status' => true, 'action' => static::ACTION_TEMP_UPDATE, 'version' => $ini[0], 'same' => 1];
                    }
                        
                }
                else{
                    return ['status' => false];
                }
            }
            else{
                return ['status' => false];
            }
        }
        else{
            return ['status' => false];
        }
    }
    
    public function explodeVersion(string $version)
    {
        if(preg_match("(^[0-9]{1,2}+\.[0-9]{1,2}+\.[0-9]{1,2}$)", $version))
        {
            return explode(".", $version);
        }
        elseif(preg_match("(^[0-9]{1,2}+\,[0-9]{1,2}+\,[0-9]{1,2}$)", $version))
        {
            return explode(",", $version);
        }
        else{
            return false;
        }
    }
    
    private function validateNameFromIni(string $ini_name, string $model_name) : bool
    {
        return $ini_name == $model_name ? true : false;
    }
}

