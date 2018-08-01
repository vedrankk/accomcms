<?php

namespace backend\models;

use Yii;
use backend\models\Emails;

/**
 * EmailsSearch represents the model behind the search form about `backend\models\Emails`.
 */
class EmailsSearch extends Emails
{
    use langdb\LangDbSearch;
    
    public static function gridColumnsCustom() : array
    {
        return [['attribute' => 'accomodation_id', 'value' => 'accomodation.name'], 'title', 'email'];
    }
}
