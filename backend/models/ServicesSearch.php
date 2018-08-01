<?php

namespace backend\models;

/**
 * ServicesSearch represents the model behind the search form about `backend\models\Services`.
 */
class ServicesSearch extends Services
{
    use langdb\LangDbSearch;
    
    public static function gridColumnsCustom() : array
    {
        return ['name', 'alt', 'ico', 'image'];
    }
}
