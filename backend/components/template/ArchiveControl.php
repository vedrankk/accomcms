<?php

namespace backend\components\template;

use \common\components\Msg;
use backend\models\Templates;
/*
 * Detects the zip file and extracts it
 */
class ArchiveControl
{
    private $zip_ext;
    private $zip_dirname;
    private $zip_path;
    
    /*
     * @param string $path -> The path to the zip
     */
    public function __construct($path)
    {
        $this->zip_path = $path;
        $this->zip_dirname = pathinfo(realpath($path), PATHINFO_DIRNAME).'/';
        $this->zip_ext = pathinfo($path, PATHINFO_EXTENSION);
    }
    
    /*
     * Checks if the extension is valid, if yes, extracts it
     */
    public function extract()
    {
        switch ($this->zip_ext){
            case 'zip':
                return $this->extractZip();
            break;

            default:
               return $this->unknownFile();
            break;
        }
    }
    
    private function extractZip()
    {
        $zip = new \ZipArchive;
        $res = $zip->open($this->zip_path);
        if ($res === TRUE) {
            if(is_writeable($this->zip_dirname))
            {
                $zip->extractTo($this->zip_dirname);
                $zip->close();
                Msg::success(Templates::t('unzip_success'));
                return true;
            }
            else
            {
                Msg::error(Templates::t('dir_not_writable'));
                return false;
            }
          
        } else {
            Msg::error(Templates::t('zip_unreadable', ['error_code' => $res]));
           return false;
        }
    }
    
    private function unknownFile()
    {
        \yii\helpers\FileHelper::removeDirectory($this->zip_dirname);
        Msg::error(Templates::t('unknown_format'));
        return false;
    }
}