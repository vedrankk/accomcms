<?php

namespace backend\components\template\xml;

use Yii;

class OptionsSaver
{
    protected $fields;
    protected $table_fields;
    
    /*
     * Sets the fields from the post request
     */
    public function getFields($post)
    {
        foreach($post as $key => $val)
        {
            if($key == '_csrf-backend')
            {
                continue;
            }
            $this->fields[$key] = $val; 
        }
        return $this;
    }
    
    /*
     * Inserts into the table
     */
    public function insert(string $table_name, int $accomodationId)
    {
        $this->fields['accomodation_id'] = $accomodationId;
        return Yii::$app->db->createCommand()->insert($table_name, $this->fields)->execute();
    }
    
    /*
     * Updates existing row
     */
    public function update(string $table_name, int $accomodationId)
    {
        return Yii::$app->db->createCommand()->update($table_name, $this->fields, 'accomodation_id = :accomodation_id', [':accomodation_id' => $accomodationId])->execute();
    }
}

