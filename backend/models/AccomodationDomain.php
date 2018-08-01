<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "accomodation_domain".
 *
 * @property integer $accomdomain_id
 * @property integer $accomodation_id
 * @property integer $domain_id
 *
 * @property Accomodation $accomodation
 * @property Domains $domain
 */
class AccomodationDomain extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'accomodation_domain';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['accomodation_id', 'domain_id'], 'required'],
            [['accomodation_id', 'domain_id'], 'integer'],
            [['accomodation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Accomodation::className(), 'targetAttribute' => ['accomodation_id' => 'accomodation_id']],
            [['domain_id'], 'exist', 'skipOnError' => true, 'targetClass' => Domains::className(), 'targetAttribute' => ['domain_id' => 'domain_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'accomdomain_id' => Yii::t('model/accomdomain', 'accomdomain_id'),
            'accomodation_id' => Yii::t('model/accomdomain', 'accomodation_id'),
            'domain_id' => Yii::t('model/accomdomain', 'domain_id'),
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
    public function getDomain()
    {
        return $this->hasOne(Domains::className(), ['domain_id' => 'domain_id']);
    }
    
    public function validateTableEntry(int $accomodation_id, int $domain_id) : bool
    {
        return self::validateAccomodation($accomodation_id) && self::validateDomain($domain_id) ? true : false;
    }
    
    private function validateAccomodation(int $id) : bool
    {
        return self::find()->where(['accomodation_id' => $id])->asArray()->one() ? self::returnError('accomodation_has_domain') : true;
    }
    
    public function validateDomain(int $id) : bool
    {
        return self::find()->where(['domain_id' => $id])->asArray()->one() ? self::returnError('domain_taken') : true;
    }
    
    private function returnError($msg)
    {
        \common\components\Msg::error(Yii::t('model/accomdomain', $msg));
        return false;
    }
}
