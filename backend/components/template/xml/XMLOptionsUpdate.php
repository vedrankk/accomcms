<?php

namespace backend\components\template\xml;


class XMLOptionsUpdate extends XMLOptions
{
    protected $table_field_data;
    
    /*
     * Updates all the fields that are neccesarry
     */
    public function updateTable()
    {
        $change = $this->checkForNewFieldsAndTypes();
        $this->queryNewFields($change['insertion']);
        $this->queryChangeType($change['types']);
        $this->queryDeleteFields($change['forDelete']);
    }
    
    /*
     * Adds new fields to the table
     */
    private function queryNewFields($fields)
    {
        foreach($fields as $key => $val)
        {
            try{
                $this->db->createCommand()->addColumn($this->table_name, $this->formatOptionName($val->option_name), $this->createTypes($val))->execute();
            } catch (Exception $ex) {
                throw new \yii\base\UserException(405, 'Error adding field. Field name: '.$val->option_name); 
            }
        }
    }
    
    /*
     * Changes the type of the field in the table
     */
    private function queryChangeType($fields)
    {
        foreach($fields as $key => $val)
        {
            try {
                $this->db->createCommand()->alterColumn($this->table_name, $this->formatOptionName($val->option_name), $this->createTypes($val))->execute();
            } catch (Exception $ex) {
                throw new \yii\base\UserException(405, 'Error altering field. Field Name: '.$val->option_name); 
            }
        }
    }
    
    /*
     * Deletes the fields from the table
     */
    private function queryDeleteFields($fields)
    {
        foreach($fields as $key => $val)
        {
            if($val == 'id' || $val == 'accomodation_id')
            {
                continue;
            }
            try {
                $this->db->createCommand()->dropColumn($this->table_name, $val)->execute();
            } catch (Exception $ex) {
                throw new \yii\base\UserException(405, 'Error removing field. Field Name: '.$val); 
            }
        }
    }
    
    /*
     * Gets the field data from table(column data)
     */
    private function getFieldDataFromTable()
    {
        $this->table_field_data =  $this->db->createCommand('SHOW COLUMNS FROM '. $this->table_name)->queryAll();
    }
    
    /*
     * Checks if there are any new fields in the XML or if any type of the existing field is changed.
     */
    private function checkForNewFieldsAndTypes()
    {
        $this->getFieldDataFromTable();
        $fieldsForInsertion = [];
        $changedType = [];
        foreach($this->xml as $key => $val)
        {
            $exists = false;
            foreach($this->table_field_data as $t_key => $t_val)
            {
               if($t_val['Field'] === 'id' || $val['Field'] == 'accomodation_id')
               {
                   continue;
               }
               //Checks if the name exists in the table
               if($this->formatOptionName($val->option_name) == $t_val['Field'])
               {
                   //Checks if the type is changed in the xml
                   if($this->formatTypeForTable($val) !== $t_val['Type'])
                   {
                       $changedType[] = $val;
                   }
                   $exists = true;
                   break;
               }
            }
            //Adds to the insertion array in the case there is a new field
            if(!$exists)
            {
                $fieldsForInsertion[] = $val;
            }
        }
        return ['insertion' => $fieldsForInsertion, 'types' => $changedType, 'forDelete' => $this->checkForDeletedFields()];
    }
    
    /*
     * Checks if there are any fields that were deleted from the XML
     */
    private function checkForDeletedFields() : array
    {
        $forDelete = [];
        foreach($this->table_field_data as $key => $val)
        {
            $exists = false;
            foreach($this->xml as $x_key => $x_val)
            {
                if($val['Field'] == 'id' || $val['Field'] == 'accomodation_id')
                {
                    $exists = true;
                    break;
                }
                if($val['Field'] == $this->formatOptionName($x_val->option_name))
                {
                    $exists = true;
                    break;
                }
            }
            if(!$exists)
            {
                $forDelete[] = $val['Field'];
            }
        }
        return $forDelete;
    }
    
    /*
     * Formats the type to match the type we get from the table data
     */
    private function formatTypeForTable($xml)
    {
        switch($xml->option_type)
        {
            case self::CASE_STRING:
                return sprintf('varchar(%s)', $xml->option_length);
            break;
        
            case self::CASE_TEXT:
                return 'longtext';
            break;
        
            case self::CASE_INT:
                return 'int(11)';
            break;
        
            case self::CASE_BOOLEAN:
                return "enum('0','1')";
            break;
        
            case self::CASE_DATE:
                return 'date';
            break;
        }
    }
}
