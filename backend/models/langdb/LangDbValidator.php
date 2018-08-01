<?php

namespace backend\models\langdb;

use yii;
use common\components\WebsiteLang;
use yii\db\IntegrityException;
use yii\base\InvalidCallException;

trait LangDbValidator
{
    abstract public function rulesCustom() : array;
    
    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return array_merge($this->rulesCustom(), $this->rulesDefault());
    }
    
    /**
     * Default ruler for lang_id and parent_id
     *
     * @return array
     */
    public function rulesDefault() : array
    {
        return [
            [['lang_id', 'parent_id'], 'integer'],
            ['lang_id', 'validateLangId'],
            ['parent_id', 'validateTranslateParentId', 'on' => static::SCENARIO_ADD_TRANSLATION],
            [['lang_id', 'parent_id'], 'unique', 'comboNotUnique' => 'Error unique add translation.',
                 'targetAttribute' => ['lang_id', 'parent_id'], 'on' => static::SCENARIO_ADD_TRANSLATION],
            ['parent_id', 'compare', 'compareValue' => 0, 'operator' => '==', 'on' => static::SCENARIO_CREATE]
        ];
    }
     
    /**
     * Validate if lang id exist
     *
     * @return bool
     */
    public function validateLangId() : bool
    {
        if (!WebsiteLang::idExist($this->lang_id)) {
            $this->addError('lang_id', 'Invalid lang id');
            return false;
        }

        return true;
    }
    
    /**
     * Check is parent id valid and if exist on default db lang
     *
     * @return bool
     */
    public function validateTranslateParentId() : bool
    {
        if ($this->parent_id < 1 || !$this->findOne([static::primaryKey()[0] => $this->parent_id, 'lang_id' => $this->default_dblang_id])) {
            $this->addError('parent_id', 'Invalid parent id');
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate is lang id in url the same with lang id in the model item
     *
     * @param type $model
     * @return boolean
     */
    public function validateLangInUrl($model)
    {
        if (!isset($model->lang_id)) {
            return true;
        }
         
        $lang_id = (int) Yii::$app->request->get('db_lang');

        return $model->lang_id === $lang_id;
    }
    /**
     * Delete translation
     *
     * @param type $model
     * @return bool
     * @throws InvalidCallException
     */
    public function validateDeleteTranslation($model) : bool
    {
        if ($model->parent_id == 0 || !$model->isTranslate()) {
            throw new InvalidCallException(Yii::t('app', "You can't delete an original value this way."));
        }
        return true;
    }
    
    /**
     *
     * @todo ovde moze kada nadje jedan podataka barem da kaze jezik na kome ima translate
     *
     */
    public function validateDelete(int $id) : bool
    {
        if (!empty($this->findOne(['parent_id' => $id]))) {
            throw new IntegrityException(Yii::t('app', "has_translation"));
        }
        return true;
    }
}
