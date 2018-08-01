<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AccomLanguages;

/**
 * AccomLanguagesSearch represents the model behind the search form about `backend\models\AccomLanguages`.
 */
class AccomLanguagesSearch extends AccomLanguages
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['accom_languages_id'], 'integer'],
            [['accomodation_id', 'lang_id', 'default_lang_id'], 'string']
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
        //Shows only default values
        $query = AccomLanguages::find()->where(['default_lang_id' => 1]);

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
            'accom_languages_id' => $this->accom_languages_id,
        ]);
        
        $query->andFilterWhere(['like', 'accomodation.name', $this->accomodation_id]);
        $query->andFilterWhere(['like', 'languages_db.name', $this->lang_id]);
        $query->andFilterWhere(['like', 'languages_db.name', $this->default_lang_id]);

        return $dataProvider;
    }
}
