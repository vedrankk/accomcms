<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\LanguagesWebsite;

/**
 * LanguagesWebsiteSearch represents the model behind the search form about `backend\models\LanguagesWebsite`.
 */
class LanguagesWebsiteSearch extends LanguagesWebsite
{
    use langdb\LangDbSearch;
    
    public static function gridColumnsCustom() : array
    {
        return ['name','code','t_name', ];
    }
}
