<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "accomodation".
 *
 * @property integer $accomodation_id
 * @property integer $lang_id
 * @property integer $parent_id
 * @property string $name
 * @property string $description
 * @property string $address
 * @property string $facebook
 * @property string $twitter
 * @property string $youtube
 * @property integer $published
 *
 * @property LanguagesDb $lang
 */
class Accomodation extends LangDb
{
  
    /**
     * @inheritdoc
     */
    public function rulesCustom() : array
    {
        return [
            [['published', 'user_id'], 'integer'],
            [['name', 'published'], 'required'],
            [['description', 'address'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['facebook', 'twitter', 'youtube'], 'url', 'defaultScheme' => 'http'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => static::t('Name'),
            'accomodation_id' => static::t('Accomodation ID'),
            'lang_id' => static::t('Lang ID'),
            'parent_id' => static::t('Parent ID'),
            'description' => static::t('Description'),
            'address' => static::t('Address'),
            'facebook' => static::t('Facebook'),
            'twitter' => static::t('Twitter'),
            'youtube' => static::t('Youtube'),
            'published' => static::t('Published'),
        ];
    }
    
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['overviewTranslateUpdate'] = ['name', 'address', 'description'];
        
        return $scenarios;
    }
    
    public static function viewAttributes() : array
    {
        return ['name', 'facebook', 'twitter', 'youtube', 'description', 'address',
            [
                'attribute' => 'published', 
                'value' => function($model){return $model->published == 1 ? static::t('Published') : static::t('Unpublished');}
            ]
        ]; 
    }

    public static function translateFields() : array
    {
        return ['name', 'description', 'address'];
    }
    
    public static function titleAttribute() : string
    {
        return 'name';
    }
    
    public function getUser()
    {
        return $this->hasOne(\common\components\User::className(), ['user_id' => 'user_id']);
    }
}
