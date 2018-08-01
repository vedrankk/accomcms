<?php

namespace backend\components\template;

use Yii;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

class TemplateOptionsWidget extends xml\XMLOptions
{
    protected $xml;
    protected $accomodationId;
    protected $accomodation_data;
    protected $form = '';
    
    const INT_SPECIAL_CASE = ['news', 'gallery'];
    const SPECIAL_CASE_NEWS_KEYS = ['id' => 'news_id', 'name' => 'news_headline'];
    const SPECIAL_CASE_GALLERY_KEYS = ['id' => 'gallery_id', 'name' => 'gallery_name'];
    
    public function __construct($xml, $table_name, $accomodationId)
    {
        parent::__construct($xml, $table_name);
        $this->accomodationId = $accomodationId;
        $this->accomodation_data = (new \yii\db\Query())->select('*')->from($this->table_name)->where(['accomodation_id' => $this->accomodationId])->all()[0];
    }
    
    /*
     * Creates the form with all the fields
     */
    public function display()
    {
        $display = '';
        $this->form = Html::beginForm(['index'], 'post');
        foreach($this->xml as $key => $val)
        {
            switch($val->option_type)
            {
                case self::CASE_STRING:
                    $display .= $this->displayTypeString($val);
                break;
            
                case self::CASE_INT:
                    $display .= $this->displayTypeInt($val);
                break;
            
                 case self::CASE_BOOLEAN:
                    $display .= $this->displayTypeBoolean($val);
                 break;
            
                 case self::CASE_TEXT:
                    $display .= $this->displayTypeText($val);
                 break;
             
                 case self::CASE_DATE:
                    $display .= $this->displayTypeDate($val);
                 break;
            }
        }
        return $this->form. $display .Html::submitButton('Submit', ['class' => 'btn btn-default']) .Html::endForm();
    }
    
    /*
     * Creates the input for the string
     */
    private function displayTypeString($val) : string
    {
        $name = $this->formatOptionName($val->option_name);
        $label = Html::label($val->option_name, $name);
        $input =  Html::input('text', $name, isset($this->accomodation_data[$name]) ? $this->accomodation_data[$name] : '' , ['id' => $name, 'class' => 'form-control']);
        return Html::tag('div', $label.$input, ['class' => 'form-group']);
    }
    
    /*
     * Creates the textarea
     */
    private function displayTypeText($val)
    {
        $name = $this->formatOptionName($val->option_name);
        $label = Html::label($val->option_name, $name);
        $input =  Html::textarea($name, isset($this->accomodation_data[$name]) ? $this->accomodation_data[$name] : '' ,['id' => $name, 'class' => 'form-control']);
        return Html::tag('div', $label.$input, ['class' => 'form-group']);
    }
    
    /*
     * Creates the input for the integer field. If the first characters in the name are some of the special keywords(news, gallery) returns the Select2 component.
     */
    private function displayTypeInt($val) : string
    {
        $special = $this->detectIntSpecialCase($val);
        if(!empty($special))
        {
            return $special;
        }
        $name = $this->formatOptionName($val->option_name);
        $label = Html::label($val->option_name, $name);
        $input =  Html::input('number', $name, isset($this->accomodation_data[$name]) ? $this->accomodation_data[$name] : '', ['id' => $name, 'class' => 'form-control']);
        return Html::tag('div', $label.$input, ['class' => 'form-group']);
    }
    
    /*
     * Displys the checkbox
     */
    private function displayTypeBoolean($val)
    {
        $name = $this->formatOptionName($val->option_name);
        $hidden_input = Html::input('hidden', $name, 0);
        $input =  Html::checkbox($name, $this->checkboxChecked($val, $name), ['value' => 1]);
        $label = Html::label($input. ' '.$val->option_name);
        return Html::tag('div', $hidden_input.$label, ['class' => 'checkbox']);
    }
    
    /*
     * Displyas the date picker
     */
    private function displayTypeDate($val)
    {
        $name = $this->formatOptionName($val->option_name);
        $label = Html::label($val->option_name, $name);
        $input =  Html::input('date', $name, isset($this->accomodation_data[$name]) ? $this->accomodation_data[$name] : '', ['id' => $name, 'class' => 'form-control']);
        return Html::tag('div', $label.$input, ['class' => 'form-group']);
    }
    
    
    private function checkboxChecked($val, $name)
    {
        if(isset($this->accomodation_data[$name]) && !empty($this->accomodation_data[$name]) && $this->accomodation_data[$name] != null)
        {
            return $this->accomodation_data[$name] == 1 ? true : false;
        }
        else{
            return false;
        }
    }
    
    /*
     * Checks if the name of the integer field contains the speical keywords
     */
    private function detectIntSpecialCase($val)
    {
        foreach(self::INT_SPECIAL_CASE as $case)
        {
            $pattern = sprintf('/^%s.+$/', $case);
            if(preg_match($pattern, $val->option_name))
            {
                return $this->displayTypeIntSpecial($val, $case);
            }
        }
        return [];
    }
    
    /*
     * Gets the data for the keyword and creates the component.
     */
    private function displayTypeIntSpecial($val, string $param)
    {
        $data = $this->getIntSpecialCaseData($param);
        return $this->createSelect($data, $val);
    }
    
    private function getIntSpecialCaseData(string $param) : array
    {
        switch($param){
            case self::INT_SPECIAL_CASE[0]:
                return ['data' => $this->specialCaseData('AccomodationNews', 'NEWS'), 'placeholder' => 'News'];
            break;
        
            case self::INT_SPECIAL_CASE[1]:
                return ['data' => $this->specialCaseData('Galleries', 'GALLERY'), 'placeholder' => 'Gallery'];
            break;
        }
        return [];
    }
    
    /*
     * Creates the Select2 component
     */
    private function createSelect(array $data, $val)
    {
        $name = $this->formatOptionName($val->option_name);
        $value = isset($this->accomodation_data[$name]) && $this->accomodation_data[$name] != 0 ? $this->accomodation_data[$name] : '';
        $data_value = isset($this->accomodation_data[$name]) && $this->accomodation_data[$name] != 0 ? array_merge([0 => 'Empty'], $data['data']) : $data['data'];
        
        $label = '<label class="control-label">'. $val->option_description .'</label>';
        $select = Select2::widget([
                        'name' => $name,
                        'data' => $data_value,
                        'value' => $value,
                        'options' => [
                            'placeholder' => $data['placeholder'],
                        ],
                    ]);
        return $label.$select;
    }
    
    /*
     * Gets the data from the table depending on the case name
     */
    private function specialCaseData($class_name, $param) : array
    {
        $data = sprintf('\backend\models\%s', $class_name)::find()->where(['accomodation_id' => $this->accomodationId])->asArray()->all();
        $keys = constant(sprintf('self::SPECIAL_CASE_%s_KEYS', $param));
        return !empty($data) ? ArrayHelper::map($data, $keys['id'], $keys['name']) : [];
    }
}
