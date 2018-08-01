<?php

namespace backend\components\template\xml;

use Yii;

class XMLOptions
{
    protected $xml;
    protected $db;
    protected $sql;
    protected $table_name;
    protected $fields;
    
    
    const CASE_STRING  = 'string';
    const CASE_TEXT    = 'text';
    const CASE_INT     = 'integer';
    const CASE_DATE    = 'date';
    const CASE_BOOLEAN = 'boolean';
    
    public function __construct($xml, $table_name)
    {
        $this->xml = $xml;
        $this->db = Yii::$app->db;
        $this->table_name = $this->createTableName($table_name);
    }
    
    /*
     * Returns the name of the table depending on the name of the template
     */
    public function createTableName($table_name) : string
    {
        return 'options_' .preg_replace('/([^a-zA-Z0-9])/', '_', strtolower($table_name));
    }
    
    /*
     * Replaces all the special characters with an underscore
     */
    protected function formatOptionName(string $name) : string
    {
        return preg_replace('/([^a-zA-Z])/', '_', strtolower($name));
    }
    
    /*
     * Sets the appropriate type for the option
     */
    protected function createTypes($val)
    {
        switch($val->option_type)
        {
            case self::CASE_BOOLEAN : 
                    return "ENUM('0','1')";
            break;
            
            case self::CASE_STRING :
                return isset($val->option_length) ? 'string('.$val->option_length.')' : 'string';
            break;
        
            case self::CASE_DATE:
                return 'date';
            break;
        
            case self::CASE_INT:
                return 'integer';
            break;
        
            case self::CASE_TEXT:
                return 'text';
            break;
        }
    }
    
    /*
     * Saves the options from the post request
     */
    public static function saveFromPost($post, $template_name, $accomodation_id) : bool
    {
        $saver = new \backend\components\template\xml\OptionsSaver();
        $rows = !empty((new \yii\db\Query())->select('id')->from(\backend\components\template\xml\XMLOptions::createTableName($template_name))->where(['accomodation_id' => $accomodation_id])->one());
        if(!$rows)
        {
             $saver->getFields(Yii::$app->request->post())->insert(\backend\components\template\xml\XMLOptions::createTableName($template_name), $accomodation_id);
        }
        else{
             $saver->getFields(Yii::$app->request->post())->update(\backend\components\template\xml\XMLOptions::createTableName($template_name), $accomodation_id);
        }
        return true;
    }
}

