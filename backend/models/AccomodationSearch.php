<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Accomodation;

/**
 * AccomodationSearch represents the model behind the search form about `backend\models\Accomodation`.
 */
class AccomodationSearch extends Accomodation
{
    use langdb\LangDbSearch;
    
    public static function gridColumnsCustom() : array
    {
        return ['name', 'address', 'facebook'];
    }
}
