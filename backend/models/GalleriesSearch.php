<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Galleries;

/**
 * GalleriesSearch represents the model behind the search form about `backend\models\Galleries`.
 */
class GalleriesSearch extends Galleries
{
    use langdb\LangDbSearch;
    
    public static function gridColumnsCustom() : array
    {
        //Za sad stoji attribute parent_id nisam nasao kako drugacije da ubacim
        $actions = [['format' => 'raw','attribute' => 'parent_id', 'value' => function($model){return \yii\helpers\Html::a($model::t('all_galleries'), ['view-galleries', 'id' => $model->accomodation_id, 'db_lang' => $model->dblang_id], ['class' => 'btn btn-primary']);}]];
        
        $columns = [['attribute' => 'accomodation_id', 'value' => 'accomodation.name'], 'gallery_name', 'gallery_description'];
        $final = array_merge($columns, $actions);
        return $final;
    }
}
