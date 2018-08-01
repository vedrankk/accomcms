<?php

namespace backend\components;

class ActiveField extends \yii\widgets\ActiveField
{
    public function render($content = null)
    {
        if ($this->model->isTranslate() &&
                !in_array($this->attribute, $this->model::translateFields()) &&
                 !in_array($this->attribute, $this->model->defaultTranslateFields())
            ) {
            return '';
        }
        
        return parent::render($content);
    }
}
