<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AccomodationTemplate;

/**
 * AccomodationTemplateSearch represents the model behind the search form about `backend\models\AccomodationTemplate`.
 */
class AccomodationTemplateSearch extends AccomodationTemplate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['accom_template_id', 'accomodation_id', 'template_id'], 'integer'],
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
        $query = AccomodationTemplate::find();

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
        $query->joinWith('template');

        // grid filtering conditions
        $query->andFilterWhere([
            'accom_template_id' => $this->accom_template_id
        ]);
        
        $query->andFilterWhere(['like', 'accomodation.name', $this->accomodation_id]);
        $query->andFilterWhere(['like', 'template.name', $this->accomodation_id]);

        return $dataProvider;
    }
}
