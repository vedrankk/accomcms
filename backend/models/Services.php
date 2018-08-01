<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "services".
 *
 * @property integer $services_id
 * @property string $name
 * @property string $alt
 * @property string $description
 * @property integer $lang_id
 * @property integer $parent_id
 * @property string $ico
 * @property string $image
 *
 * @property LanguagesDb $lang
 */
class Services extends LangDb
{

    /**
     * @inheritdoc
     */
    public function rulesCustom() : array
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['name', 'ico', 'image'], 'string', 'max' => 100],
            [['name'], 'unique'],
            [['alt'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => static::t('Name'),
            'alt' => static::t('Alt'),
            'description' => static::t('Description'),
            'lang_id' => static::t('Lang ID'),
            'parent_id' => static::t('Parent ID'),
            'ico' => static::t('Ico'),
            'image' => static::t('Image'),
            'services_id' => static::t('Services ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    /*
    public function getLang()
    {
        return $this->hasOne(LanguagesDb::className(), ['lang_id' => 'lang_id']);
    }*/

    public static function viewAttributes() : array
    {
        return ['name', 'description', 'alt', 'ico', 'image'];
    }

    public static function translateFields() : array
    {
        return ['alt', 'description'];
    }
    
    public static function titleAttribute() : string
    {
        return 'name';
    }
}
