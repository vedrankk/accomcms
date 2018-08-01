<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "domains".
 *
 * @property integer $domain_id
 * @property string $domain_url
 *
 * @property AccomodationDomain[] $accomodationDomains
 */
class Domains extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'domains';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['domain_url'], 'required'],
            [['domain_url'], 'string', 'max' => 70],
            [['domain_url'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'domain_id' => Yii::t('model/domains', 'domain_id'),
            'domain_url' => Yii::t('model/domains', 'domain_url'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccomodationDomains()
    {
        return $this->hasMany(AccomodationDomain::className(), ['domain_id' => 'domain_id']);
    }
    
    public function getAvaliableDomains($newRecord = true, $domain_id = null) : array
    {
        $accom_domains = AccomodationDomain::find()->asArray()->all();
        $allDomains = self::find()->asArray()->all();
        $avaliableDomains = [];
        
        if(!$newRecord)
        {
            $avaliableDomains[] = self::getCurrentDomain($domain_id);
        }
        foreach($allDomains as $key => $val)
        {
            $exists = false;
            foreach($accom_domains as $a_key => $a_val)
            {
                if($val['domain_id'] === $a_val['domain_id'])
                {
                    $exists = true;
                    break;
                }
            }
            if(!$exists)
            {
                $avaliableDomains[] = $val;
            }
        }
        return $avaliableDomains;
    }
    
    private function getCurrentDomain(int $id) : array
    {
        return self::find()->where(['domain_id' => $id])->asArray()->one();
    }
    
    public function validateDelete(int $id) : bool
    {
        return empty(AccomodationDomain::find()->where(['domain_id' => $id])->asArray()->one()) ? true : false;
    }
}
