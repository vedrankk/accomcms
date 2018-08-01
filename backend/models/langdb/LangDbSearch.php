<?php

namespace backend\models\langdb;

use common\components\WebsiteLang;
use yii\data\ActiveDataProvider;

trait LangDbSearch
{
    protected static $default_order = null;
    abstract public static function gridColumnsCustom() : array;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [static::gridColumnsNames(), 'safe'],
        ];
    }
    
    /**
     * Get grid columns, merged default with custom columns
     *
     * @return array
     */
    public static function gridColumns(): array
    {
        $cols_before = [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'lang_id',
                'value' => function ($model) {
                    return WebsiteLang::getLangCodeByLangId($model->lang_id);
                },
            ],
            ];
        $cols_after  = [['class' => 'backend\components\grid\TranslateActionColumn']];

        return \yii\helpers\ArrayHelper::merge($cols_before, static::gridColumnsCustom(), $cols_after);
    }
    
    /**
     * Get grid column names
     *
     * @return array
     */
    public static function gridColumnsNames() : array
    {
        $names = [];
        foreach (static::gridColumns() as $key => $val) {
            if (!is_array($val)) {
                $names[] = $val;
            } elseif (isset($val['attribute'])) {
                $names[] = $val['attribute'];
            }
        }
        $names = array_diff($names, ['lang_id']);
        
        return array_values($names);
    }
    
    /**
     * Get sort array
     *
     * @return array
     */
    protected static function sortColumns() : array
    {
        return static::gridColumnsNames();
    }
    
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query_default = $this->find();
        $query_default->select($this->getDefaultColumns())
            ->from(['o' => $this->tableName()])
            ->leftJoin(['t' => $this->tableName()], 't.parent_id=o.'.$this->primaryKey()[0].' AND t.lang_id=:lang_id',
                    [':lang_id' => $this->dblang_id])
             ->where('o.lang_id=:default_lang AND o.parent_id IS NULL and t.'.$this->primaryKey()[0].' IS NULL',
                    [':default_lang' => $this->default_dblang_id]);
       
        $query_translate = $this->find();
        $query_translate->select($this->getTranslateColumns())
            ->from(['t' => $this->tableName()])
            ->leftJoin(['p' => $this->tableName()], 't.parent_id=p.'.$this->primaryKey()[0])
            ->where('t.lang_id=:db_lang AND t.parent_id IS NOT NULL',
                    [':db_lang' => $this->dblang_id]);
        
        $query_union = $this->find();
        $query_union->from(['union' => $query_default->union($query_translate)]);
        
        $this->load($params);
        
        if (!$this->validate()) {
            return $dataProvider;
        }
        
        foreach (static::gridColumnsNames() as $val) {
            $query_union->andFilterWhere(['like', "union.{$val}", $this->{$val}]);
        }
        
        $dataProvider = new ActiveDataProvider(['query' => $query_union,]);
        $dataProvider->setSort([
            'defaultOrder' => [static::defaultOrder() => SORT_ASC],
            'attributes' => static::sortColumns()
                ]);

        return $dataProvider;
    }
    
    /**
     * Get default order column
     *
     * @return string
     */
    public static function defaultOrder() : string
    {
        if (!is_null(static::$default_order)) {
            return static::$default_order;
        }
        $cols = static::gridColumnsNames();
 
        if (isset($cols[0])) {
            return $cols[0];
        }
        
        return static::primaryKey()[0];
    }
}
