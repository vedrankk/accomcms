<?php

namespace backend\components\grid;

use yii\helpers\Html;
use yii\helpers\Url;
use yii;

class TranslateActionColumn extends yii\grid\ActionColumn
{
    public function init()
    {
        $this->template = ' {add_translation} {view} {update} {delete} {update_translation} {delete_translation}';
        $this->initVisibleButtons();
        $this->buttons = [
            'add_translation' => function ($url, $model, $key) {
                $params[0] = $this->controller ? $this->controller . '/add-translation' : 'add-translation';
                $params['db_lang'] = $model->dblang_id;
                $params['parent_id'] = $key;
                $url = Url::toRoute($params);
                return Html::a('<span class="glyphicon glyphicon-plus-sign"></span>', $url, [
                            'title' => Yii::t('app', 'Add translation'),
                ]);
            },
            'update_translation' => function ($url, $model, $key) {
                $params[0] = $this->controller ? $this->controller . '/update-translation' : 'update-translation';
                $params['db_lang'] = $model->dblang_id;
                $params['id'] = $key;
                $url = Url::toRoute($params);
                return Html::a('<span class="glyphicon glyphicon-edit"></span>', $url, [
                            'title' => Yii::t('app', 'Update translation'),
                ]);
            },
             'view' => function ($url, $model, $key) {
                 $params[0] = $this->controller ? $this->controller . '/view' : 'view';
                 $params['id'] = $key;
                 $params['db_lang'] = $model->dblang_id;
                 $url = Url::toRoute($params);
                 return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => Yii::t('app', 'View item'),
                ]);
             },
              'update' => function ($url, $model, $key) {
                  $params[0] = $this->controller ? $this->controller . '/update' : 'update';
                  $params['id'] = $key;
                  $params['db_lang'] = $model->dblang_id;
                  $url = Url::toRoute($params);
                  return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => Yii::t('app', 'Update'),
                ]);
              },
              'delete_translation' => function ($url, $model, $key) {
                  $params[0] = $this->controller ? $this->controller . '/delete-translation' : 'delete-translation';
                  $params['id'] = $key;
                  $params['db_lang'] = $model->dblang_id;
                  $url = Url::toRoute($params);
                  return Html::a('<span class="glyphicon glyphicon-minus-sign"></span>', $url, [
                            'title' => Yii::t('app', 'Delete translation'),
                ]);
              }
        ];
        return parent::init();
    }
    
    private function initVisibleButtons()
    {
        $res = [
                'add_translation' => function ($model, $key, $index) {
                    return (bool) $model->isTranslate() && !$this->isTranslatedRow($model);
                },
                'view' => function ($model, $key, $index) {
                    return (bool)
                    ($model->isTranslate() && $this->isTranslatedRow($model)) ||
                    (!$model->isTranslate());
                },
                'update' => function ($model, $key, $index) {
                    return (bool) !$model->isTranslate();
                },
                'delete' => function ($model, $key, $index) {
                    return (bool) !$model->isTranslate();
                },
                'update_translation' => function ($model, $key, $index) {
                    return (bool) $model->isTranslate() && $this->isTranslatedRow($model);
                },
                'delete_translation' => function ($model, $key, $index) {
                    return (bool) $model->isTranslate() && $this->isTranslatedRow($model);
                },
            ];
        $this->visibleButtons = $res;
    }
    
    private function isTranslatedRow($model)
    {
        return (bool) !$model->parent_id == 0;
    }
  
    protected function initDefaultButtons()
    {
        $this->initDefaultButton('add_translation', 'glyphicon glyphicon-plus-sign');
        $this->initDefaultButton('update_translation', 'pencil');
        $this->initDefaultButton('delete_translation', 'glyphicon glyphicon-remove-sign');
        parent::initDefaultButtons();
    }
}
