<?php

namespace backend\components;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class LangDetailView extends \yii\widgets\DetailView
{
    public $template = '<tr><th{captionOptions}>{label}</th><td{contentOptions}>{value}</td></tr>';
    public $template_with_parent =  '<tr><th{captionOptions}>{label}</th><td{contentOptions}>{value}</td><td>{value_parent}</td></tr>';
    public $template_parent_header = '<thead><tr><th></th><th>{translated}</th><th>{default}</th></tr></thead>';
    public function init()
    {
        parent::init();
        if (!empty($this->model->parent)) {
            $this->template = $this->template_with_parent;
        }
        $this->addParentValues();
    }
    
    /**
     * Renders the detail view.
     * This is the main entry of the whole detail view rendering.
     */
    public function run()
    {
        $rows = [];
        
        if (!empty($this->model->parent)) {
            $rows[] = strtr($this->template_parent_header,
                    ['{translated}' => Yii::t('app', 'Translated'),
                     '{default}' => Yii::t('app', 'Default'),
                        ]);
        }
        
        $i = 0;
        foreach ($this->attributes as $attribute) {
            $rows[] = $this->renderAttribute($attribute, $i++);
        }

        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'table');
        echo Html::tag($tag, implode("\n", $rows), $options);
    }
    
    private function addParentValues()
    {
        foreach ($this->attributes as $key => $val) {
            $this->attributes[$key]['value_parent'] = isset($this->model->parent[$val['attribute']]) ? $this->model->parent[$val['attribute']] : '';
            if (!empty($this->model->parent) && !in_array($val['attribute'], $this->model::mergedTranslateFields())) {
                $this->attributes[$key]['value'] = Yii::t('app', "Inherent");
            }
        }
    }
    
    protected function renderAttribute($attribute, $index)
    {
        if (is_string($this->template)) {
            $captionOptions = Html::renderTagAttributes(ArrayHelper::getValue($attribute, 'captionOptions', []));
            $contentOptions = Html::renderTagAttributes(ArrayHelper::getValue($attribute, 'contentOptions', []));
            return strtr($this->template, [
                '{label}' => $attribute['label'],
                '{value}' => $this->formatter->format($attribute['value'], $attribute['format']),
                '{value_parent}' => $this->formatter->format($attribute['value_parent'], $attribute['format']),
                '{captionOptions}' => $captionOptions,
                '{contentOptions}' =>  $contentOptions,
            ]);
        } else {
            return call_user_func($this->template, $attribute, $index, $this);
        }
    }
}
