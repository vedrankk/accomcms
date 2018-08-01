<?php

namespace backend\components\grid;

class LangGridView extends \yii\grid\GridView
{
    public function init()
    {
        parent::init();
        if (!$this->filterModel->isTranslate()) {
            foreach ($this->columns as $key => $val) {
                if (isset($val->attribute) && 0 === strpos($val->attribute, 't_')) {
                    unset($this->columns[$key]);
                }
            }
        }
    }
}
