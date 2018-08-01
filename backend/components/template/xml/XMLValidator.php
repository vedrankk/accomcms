<?php

namespace backend\components\template\xml;

use common\components\Msg;

class XMLValidator
{
    protected $dir_path;
    protected $xml;
    
    const SUPPORTED_TYPES = ['string', 'integer', 'date', 'boolean', 'text'];
    
    public function __construct($dir_path)
    {
        $this->dir_path = $dir_path;
    }
    
    /*
     * Reads the data from the XML file
     */
    public function readXMLData()
    {
        $this->xml = simplexml_load_file($this->dir_path.'options.xml');
    }
    
    /*
     * Checks if the XML file exists
     */
    public function xmlFileExists()
    {
        return file_exists($this->dir_path.'options.xml') ? true : $this->returnError('There was no XML file detected in your folder. If you forgot to upload it, please update.');
    }
    
    public function getXmlData()
    {
        return $this->xml;
    }
    
    /*
     * Validates the content from the xml
     */
    public function validateOptionsContent()
    {
        $this->readXMLData();
        
        if(!$this->readableXmlData())
        {
            return false;
        }
        $return = true;
        $names = [];
        
        foreach($this->xml as $key => $val)
        {
            //Validates the content of the xml option
            if(!$this->processOptionContent($val))
            {
                $return = false;
                break;
            }
            
            $names[] = (string) $val->option_name;
        }
        
        return $return ? $this->duplicateNames($names) : false;
    }
    
    /*
     * Checks if there are duplicate names in the xml
     */
    private function duplicateNames(array $names) : bool
    {
        return count($names) === count(array_flip($names)) ? true : $this->returnError('Names have to be unique!');
    }
    
    /*
     * Option name, description and type have to be set.
     */
    private function processOptionContent($content) : bool
    {
        if(isset($content->option_name) && isset($content->option_description) && isset($content->option_type))
        {
            return $this->validateOptionNameAndDescription($content->option_name) && $this->validateOptionNameAndDescription($content->option_description) && $this->validateOptionType($content->option_type) ? true : false;
        }
        return $this->returnError('One or more required options do not exists. Please check the file and try again. Required fields are: option_name, option_description, option_type');
    }
    
    /*
     * Can only contain allowed characters
     */
    private function validateOptionNameAndDescription(string $string) : bool
    {
        return is_string($string) && preg_match('/^([a-zA-Z0-9 ])+$/', $string) ? true : $this->returnError('Invalid name or description in: '.$string);
    }
    
    /*
     * Check if the type of the options is valid, has to be a string, containt only letters and has to be in array of supported types
     */
    private function validateOptionType(string $type) : bool
    {
        return is_string($type) && preg_match('/^([a-zA-Z])+$/', $type) && in_array($type, self::SUPPORTED_TYPES) ? true : $this->returnError('Invalid or unsupported type. Check the docs for all supported types. Type Error: '.$type);
    }
    
    private function returnError(string $msg) : bool
    {
        Msg::error($msg);
        return false;
    }
    
    /*
     * Checks if the xml data is readable
     */
    public function readableXmlData()
    {
        return $this->xml === false ? $this->returnError('Unreadable XML data. Check for your file and upload again.') : true;
    }
}
