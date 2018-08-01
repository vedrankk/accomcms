<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "accomodation_template".
 *
 * @property integer $accom_template_id
 * @property integer $accomodation_id
 * @property integer $template_id
 *
 * @property Accomodation $accomodation
 * @property Templates $template
 */
class AccomodationTemplate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'accomodation_template';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['accomodation_id', 'template_id'], 'required'],
            [['accomodation_id', 'template_id'], 'integer'],
            [['accomodation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Accomodation::className(), 'targetAttribute' => ['accomodation_id' => 'accomodation_id']],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Templates::className(), 'targetAttribute' => ['template_id' => 'template_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'accom_template_id' => Yii::t('app', 'Accom Template ID'),
            'accomodation_id' => Yii::t('app', 'Accomodation ID'),
            'template_id' => Yii::t('app', 'Template ID'),
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
    public function getTemplate()
    {
        return $this->hasOne(Templates::className(), ['template_id' => 'template_id']);
    }
    
    private function accomTemplateExists($accomodation_id, $template_id)
    {
        return empty(self::find()->where(['accomodation_id' => $accomodation_id])->asArray()->one()) ? true : false;
    }
    
    private function errorSaving()
    {
        \common\components\Msg::error(Yii::t('model/accomtemplates', 'error_saving', ['error_code' => '101']));
        return false;
    }
    
    public function trySaveModel($model) : bool
    {
        if(self::accomTemplateExists($model->accomodation_id, $model->template_id) && $model->save())
        {
            return true;
        }
        else{
            return self::errorSaving();
        }
    }
}
