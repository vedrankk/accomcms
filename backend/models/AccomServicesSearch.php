<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AccomServices;

/**
 * AccomServicesSearch represents the model behind the search form about `backend\models\AccomServices`.
 */
class AccomServicesSearch extends AccomServices
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['accom_services_id'], 'integer'],
            [['accomodation_id', 'services_id'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AccomServices::find()->select('accom_services.accomodation_id')->distinct();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->joinWith('accomodation');
        $query->joinWith('services');
        // grid filtering conditions
        $query->andFilterWhere([
            'accom_services_id' => $this->accom_services_id
        ]);
        
        $query->andFilterWhere(['like', 'accomodation.name', $this->accomodation_id]);
        $query->andFilterWhere(['like', 'services.name', $this->services_id]);

        return $dataProvider;
    }
}
