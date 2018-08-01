<?php

namespace backend\components\template\xml;

/*
 * When a new template is created, this is called
 */
class XMLOptionsCreate extends XMLOptions
{
    private $fieldArray = [];
    
    public function createTable()
    {
        $this->createFields();
        $this->db->createCommand()->createTable($this->table_name, $this->fieldArray)->execute();
        if($this->db->schema->getTableSchema($this->table_name) === null)
        {
            \common\components\Msg::error('There was an error while creating options table. Rolling back changes. Please try again.');
            return false;
        }
        return true;
    }
    
    /*
     * Creates the fields for the new table. id and accomodation_id are the same everywhere.
     */
    private function createFields()
    {
        $this->fieldArray = ['id' => 'pk', 'accomodation_id' => 'integer'];
        foreach($this->xml as $key => $val)
        {
            $this->fieldArray[$this->formatOptionName($val->option_name)] = $this->createTypes($val);
        }
        $this->fieldArray[] = 'FOREIGN KEY (accomodation_id) references accomodation(accomodation_id)';
    }
}

