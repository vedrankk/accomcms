<?php

namespace backend\models;

class LanguagesWebsite extends LangDb
{
    public static function translateFields() : array
    {
        return ['name'];
    }
    
    public function rulesCustom() : array
    {
        return [
            ['name', 'string', 'max' => 32],
            [['name', 'code'], 'required'],
            ['code', 'string', 'length' => [2,7]],
            ['code', 'match',  'pattern' => '/^([a-z]{2,3}(\-[a-z]{1,3})?)(\/|$)/', 'message' => 'Code name format is: xx, xxx, xx(x)-x(xx)'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => static::t('Name'),
            'lang_id' => static::t('Lang'),
            'code' => static::t('Code Name'),
            't_name' => static::t('Translated name'),
            'order' => static::t('Order')
        ];
    }


    public static function viewAttributes() : array
    {
        return [
        'lang_id',
        'code',
        'name'];
    }
    
    public static function titleAttribute() : string
    {
        return 'name';
    }
}
