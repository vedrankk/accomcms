<?php

namespace backend\models\template;

use common\components\Msg;
use yii\helpers\FileHelper;
use Yii;

trait TemplateFileHelper
{
    
    /*
     * Deletes all files and folders from a dir
     * @param string $dir -> Dir path
     */
    private function deleteAllFromDir(string $dir)
    {
        $di = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS);
        $ri = new \RecursiveIteratorIterator($di, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ( $ri as $file ) {
            $file->isDir() ?  rmdir($file) : unlink($file);
        }
    }
    
    /*
     * Creates backup of the main template dir in the template_backup folder
     */
    private function createBackup(string $template_name, string $version, string $original_path) : bool
    {
        $path = sprintf("%s/web/template_backup/%s/%s/", Yii::getAlias("@backend"), static::templateMainDirName($template_name), $version);
        if(file_exists($original_path))
        {
            FileHelper::copyDirectory($original_path, $path);
            return true;
        }
        return false;
    }
    
    /*
     * Returns the path of the backup root dir
     */
    private function getBackupDirRoot(string $template_name) 
    {
        $path = sprintf("%s/web/template_backup/%s/", Yii::getAlias("@backend"), static::templateMainDirName($template_name));
        return file_exists($path) ? $path : false;
    }
    
    /*
     * Returns the path of the backup dir
     */
    private function getBackupDir($template_name, $version)
    {
        $path = sprintf("%s/web/template_backup/%s/%s/", Yii::getAlias("@backend"), static::templateMainDirName($template_name), $version);
        return file_exists($path) ? $path : false;
    }
    
    /*
     * Creates the temp folder for the current user
     */
    public function tempPath() : string
    {
        $user_temp = md5(static::ZIP_TEMP_CRYPT.Yii::$app->user->identity->user_id.static::ZIP_TEMP_CRYPT);
        $path = sprintf('%s/web/uploads/temp_zip/%s/', Yii::getAlias('@backend'), $user_temp);
        if(!file_exists($path))
        {
            FileHelper::createDirectory($path, $mode = 0775, $recursive = true) ? true : false;
        }
        return $path;
    }
    
    public function returnZipPath() : string
    {
        $user_temp = md5(static::ZIP_TEMP_CRYPT.Yii::$app->user->identity->user_id.static::ZIP_TEMP_CRYPT);
        $path = sprintf('%s/uploads/temp_zip/%s/', Yii::getAlias('@web'), $user_temp);
        return $path;
    }
    
    /*
     * Crypts the name of the zip file
     */
    public function zipCryptName($name) : string
    {
        $parts = explode('.', $name);
        $parts[0] = md5(static::ZIP_NAME_CRYPT.$parts[0].static::ZIP_NAME_CRYPT);
        return implode('.', $parts);
    }
    
    /*
     * Returns the name of the dir in the main template folder
     * @param int $name -> Template Name
     */
    public function templateMainDirName(string $name) : string
    {
        return md5(static::TEMPLATE_DIR_CRYPT.$name.static::TEMPLATE_DIR_CRYPT);
    }
    
    public function getRootTemplateDir(string $name) : string
    {
        return sprintf('%s/web/templates/%s/', Yii::getAlias('@backend'), $name);
    }
    
    /*
     * Creates the main template folder based on the $id and template version
     * @param int $id -> Template ID
     */
    public function createTemplateDir(string $name, int $version) : string
    {
        $path = sprintf('%s/web/templates/%s/%s/', Yii::getAlias('@backend'), static::templateMainDirName($name), $version);
        if(!file_exists($path))
        {
            FileHelper::createDirectory($path, $mode = 0775, $recursive = true) ? true : false;
        }
        return $path;
    }
    
    public function returnTemplateImagePath(string $name, int $version) : string
    {
        $path = sprintf('%s/templates/%s/%s/', Yii::getAlias('@web'), static::templateMainDirName($name), $version);
        if(!file_exists($path))
        {
            FileHelper::createDirectory($path, $mode = 0775, $recursive = true) ? true : false;
        }
        return $path;
    }
    
    /*
     * Validates the zip and uploads to temp folder
     */
    public function zipToTemp(string $fileType, string $fileName, string $fileTempPath)
    {
        if(static::validateZip($fileType, $fileName))
        {
            return static::moveToTemp($fileName, $fileTempPath);
        }
        return false;
    }
    
    /*
     * Crypts the name of the zip and uploads it to the temp folder
     */
    public function moveToTemp(string $name, string $temp)
    {
        $zip_name = static::zipCryptName($name);
        if(move_uploaded_file($temp, static::tempPath().$zip_name))
        {
            Msg::success(static::t('upload_process'));
            return static::tempPath().$zip_name;
        }
        return static::returnError(['zip_upload_error', ['error_code' => '401']]);
    }
    
    /*
     * 
     * Tries to move the files from the temporary dir to the main dir and saves the model
     * @param object $model -> Model
     * @param string $template_dir -> Main directory
     */
    private function moveToMain($model, string $template_dir, int $operation) : bool
    {
        if(static::moveTemplateToMainDir($template_dir))
        {
            switch($operation)
            {
                case 1:
                    return static::finalizeInsert($model, $template_dir);
                break;
                
                case 2:
                    Msg::success("Update success!");
                    return true;
                break;
            }
        }
        else{
            return static::returnError('There was an error proccessing files. Please try again!');
        }
    }
    
    /*
     * Moves all the files from the temp folder to the main folder
     * @param string $template_dir -> Main directory path
     */
    private function moveTemplateToMainDir(string $main_dir) : bool
    {
        $temp_dir = static::tempPath();
        $dir = new \DirectoryIterator($temp_dir);
        $success = true;
        //Loops all the files
        foreach ($dir as $fileinfo) {
            //This is some dummy data, not sure why it is in the loop
            if($fileinfo->getBasename() == '.' || $fileinfo->getBasename() == '..')
            {
                continue;
            }
            if(!rename($temp_dir.$fileinfo->getBasename(), $main_dir.$fileinfo->getBasename()))
            {
                $success = false;
                break;
            }
        }
        //If for some reason the file was not moved, deletes the temp and the main dir
        if(!$success)
        {
            FileHelper::removeDirectory($main_dir);
            FileHelper::removeDirectory($temp_dir);
            return false;
        }
        else{
            return true;
        }
    }
}

