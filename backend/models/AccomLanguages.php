<?php

namespace backend\models;

use Yii;
use common\components\Msg;

/**
 * This is the model class for table "accom_languages".
 *
 * @property integer $accom_languages_id
 * @property integer $accomodation_id
 * @property integer $lang_id
 * @property integer $default_lang_id
 *
 * @property Accomodation $accomodation
 * @property LanguagesDb $lang
 */
class AccomLanguages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'accom_languages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['accomodation_id', 'lang_id'], 'required'],
            [['accomodation_id', 'lang_id', 'default_lang_id'], 'integer'],
            [['accomodation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Accomodation::className(), 'targetAttribute' => ['accomodation_id' => 'accomodation_id']],
            [['lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => LanguagesDb::className(), 'targetAttribute' => ['lang_id' => 'lg_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'accom_languages_id' => Yii::t('model/accomlanguages', 'accom_languages_id'),
            'accomodation_id' => Yii::t('model/accomlanguages', 'accomodation_id'),
            'lang_id' => Yii::t('model/accomlanguages', 'lang_id'),
            'default_lang_id' => Yii::t('model/accomlanguages', 'default_lang_id'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccomodation()
    {
        return $this->hasOne(Accomodation::className(), ['accomodation_id' => 'accomodation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLang()
    {
        return $this->hasOne(LanguagesDb::className(), ['lg_id' => 'lang_id']);
    }
    
    public function rowExists($model) : bool
    {
        return !empty(self::find()->where(['accomodation_id' => $model->accomodation_id, 'lang_id' => $model->lang_id])->asArray()->one()) ? true : false;
    }
    
    public function defaultLangIdExists($model) : bool
    {
        return $model->default_lang_id != 0 && !empty(self::find()->where(['accomodation_id' => $model->accomodation_id, 'default_lang_id' => 1])->asArray()->one()) ? true : false;
    }
    
    public function removeExistingLangs(int $id) : array
    {
         $langs = \common\components\DbLang::getLangs();
         $existingAccomLangs = AccomLanguages::find()->select('lang_id')->where(['accomodation_id' => $id])->asArray()->all();
         foreach ($existingAccomLangs as $key => $val)
         {
             foreach($langs as $key_l => $val_l){
                 if($val['lang_id'] == $val_l['lang_id']){
                     unset($langs[$key_l]);
                 }
             }
         }
         return $langs;
    }
    
    /*
     * Checks if the language already exists for this accomodation
     * @param $accomId int - Current Accomodation ID
     * @param $langId int - Current Language ID
     */
    public function langExists(int $accomId, int $langId) : bool
    {
        return !empty(self::find()->where(['accomodation_id' => $accomId, 'lang_id' => $langId])->one()) ? true : false;
    }
    
    /*
     * Checks if the accomodation already has a default language set
     * @param $accomId int - Accomodation ID
     */
    public function hasDefault(int $accomId) : bool
    {
        return !empty(self::find()->where(['accomodation_id' => $accomId, 'default_lang_id' => 1])->one()) ? true : false;
    }
    
    /*
     * Adds a new language
     * @param $model - AccomLanguages Model
     * @param $accomId int - Accomodation ID
     * @param $langId int - Language ID
     * @param $default int (0/1) - True if the language is default
     * @return bool
     */
    public function addNewLang(AccomLanguages $model, int $accomId, int $langId, int $default) : bool
    {
        $model->accomodation_id = $accomId;
        $model->lang_id = $langId;
        $model->default_lang_id = $default;
        if($model->save())
        {
            Msg::success(Yii::t('model/accomlanguages', 'new_success'));
            return true;
        }
        else
        {
            Msg::error(Yii::t('model/accomlanguages', 'insert_error'));
            return false;
        }
    }
}
