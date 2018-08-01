<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AccomodationNews;

/**
 * AccomodationNewsSearch represents the model behind the search form about `backend\models\AccomodationNews`.
 */
class AccomodationNewsSearch extends AccomodationNews
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['news_id'], 'integer'],
            [['news_headline', 'news_text'], 'safe'],
            [['accomodation_id', 'lang_id'], 'string']
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
        $query = AccomodationNews::find();

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
        $query->joinWith('lang');

        // grid filtering conditions
        $query->andFilterWhere([
            'news_id' => $this->news_id
        ]);

        $query->andFilterWhere(['like', 'news_headline', $this->news_headline]);
        
        $query->andFilterWhere(['like', 'accomodation.name', $this->accomodation_id]);
        $query->andFilterWhere(['like', 'languages_db.name', $this->lang_id]);
        

        return $dataProvider;
    }
}
