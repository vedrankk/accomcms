<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AccomodationDomain;

/**
 * AccomodationDomainSearch represents the model behind the search form about `backend\models\AccomodationDomain`.
 */
class AccomodationDomainSearch extends AccomodationDomain
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['accomdomain_id'], 'integer'],
            [['accomodation_id', 'domain_id'], 'string']
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
        $query = AccomodationDomain::find();

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
        $query->joinWith('domain');
        
        // grid filtering conditions
        $query->andFilterWhere([
            'accomdomain_id' => $this->accomdomain_id
        ]);
        
        $query->andFilterWhere(['like', 'accomodation.name', $this->accomodation_id]);
        $query->andFilterWhere(['like', 'domains.domain_url', $this->domain_id]);

        return $dataProvider;
    }
}
