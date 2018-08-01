<?php

namespace backend\models;

class LanguagesDbSearch extends LanguagesDb
{
    use langdb\LangDbSearch;
    
    public static function gridColumnsCustom() : array
    {
        return ['name','code','t_name', ];
    }
}
