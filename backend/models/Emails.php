<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "emails".
 *
 * @property integer $emails_id
 * @property integer $lang_id
 * @property integer $parent_id
 * @property string $title
 * @property integer $accomodation_id
 *
 * @property Accomodation $accomodation
 * @property LanguagesDb $lang
 */
class Emails extends \backend\models\LangDb
{

    /**
     * @inheritdoc
     */
    public function rulesCustom() : array
    {
        return [
            [['accomodation_id'], 'integer'],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['title', 'accomodation_id'], 'required'],
            [['title', 'email'], 'string', 'max' => 255],
            [['accomodation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Accomodation::className(), 'targetAttribute' => ['accomodation_id' => 'accomodation_id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => static::t('ETitle'),
            'emails_id' => static::t('Emails ID'),
            'lang_id' => static::t('Lang ID'),
            'parent_id' => static::t('Parent ID'),
            'email' => static::t('Email'),
            'accomodation_id' => static::t('Accomodation ID'),
        ];
    }
    
    public static function viewAttributes() : array
    {
        return ['lang_id','title', 'email', ['attribute' => 'accomodation_id', 'value' => function($model){return Accomodation::findOne(['accomodation_id' => $model->accomodation_id])['name'];}]]; 
    }

    public static function translateFields() : array
    {
        return ['title'];
    }
    
    public static function titleAttribute() : string
    {
        return 'title';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccomodation()
    {
        return $this->hasOne(Accomodation::className(), ['accomodation_id' => 'accomodation_id']);
    }
}
