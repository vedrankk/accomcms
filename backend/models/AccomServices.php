<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "accom_services".
 *
 * @property integer $accom_services_id
 * @property integer $accomodation_id
 * @property integer $services_id
 *
 * @property Accomodation $accomodation
 * @property Services $services
 */
class AccomServices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'accom_services';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['accomodation_id', 'services_id'], 'required'],
            [['accomodation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Accomodation::className(), 'targetAttribute' => ['accomodation_id' => 'accomodation_id']],
            [['services_id'], 'exist', 'skipOnError' => true, 'targetClass' => Services::className(), 'targetAttribute' => ['services_id' => 'services_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'accom_services_id' => Yii::t('model/accomservices','accom_services_id'),
            'accomodation_id' => Yii::t('model/accomservices','accomodation_id'),
            'services_id' => Yii::t('model/accomservices','services_id'),
        ];
    }
    
    public function createWarningMessage(int $accomodation_id, int $service_id) : string
    {
         $accomodation = \backend\models\Accomodation::findOne(['accomodation_id' => $accomodation_id])->name;
         $service = \backend\models\Services::findOne(['services_id' => $service_id])->name;
         return sprintf('%s <br>', Yii::t('model/accomservices', 'service_exists',['accomodation' => $accomodation, 'service' => $service]));
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
    public function getServices()
    {
        return $this->hasOne(Services::className(), ['services_id' => 'services_id']);
    }
}
