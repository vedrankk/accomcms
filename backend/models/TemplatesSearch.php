<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Templates;

/**
 * TemplatesSearch represents the model behind the search form about `backend\models\Templates`.
 */
class TemplatesSearch extends Templates
{
    use langdb\LangDbSearch;
    
    public static function gridColumnsCustom() : array
    {
        return ['name', 'path'];
    }
}
