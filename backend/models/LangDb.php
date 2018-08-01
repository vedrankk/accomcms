<?php

namespace backend\models;

use yii;
use common\components\DbLang;
use yii\base\ErrorException;

abstract class LangDb extends \yii\db\ActiveRecord
{
    use langdb\LangDbValidator;
    use langdb\LangDbFields;
    
    const SCENARIO_ADD_TRANSLATION      = 'add-translation';
    const SCENARIO_UPDATE_TRANSLATION   = 'update-translation';
    const SCENARIO_CREATE               = 'create';
    const SCENARIO_UPDATE               = 'update';
    
    protected $default_dblang_id = 1;
    public $dblang_id = 1;
    public $t_name;
    public $parent = [];
    public $title_row_name = '';
    
    /**
     * Get array of db table fields which can be translated (
     * no default lang fields like lang_id, parent_id and primary key)
     */
    abstract public static function translateFields() : array;
    /**
     * Get array of fields for view
     */
    abstract public static function viewAttributes() : array;
    abstract public static function titleAttribute() : string;
    

       
    public function init()
    {
        DbLang::invalidateCache();
        $this->setDefaultDbLang($this->default_dblang_id);
        $this->setDbLang();
        parent::init();
    }
    
    /**
     * Get db table name
     *
     * @return string
     */
    public static function tableName()
    {
        $name = static::class;
        if ($pos = strrchr($name, '\\')) {
            $name = (substr($pos, 1));
        }
        $name = str_replace('Search', '', $name);
        $name_a  = preg_split('#([A-Z][^A-Z]*)#', $name, null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $name = strtolower(implode('_', $name_a));
        return $name;
    }
    
    /**
     * Get model name,
     * if model is Search model then return original, not search model
     * @return string
     */
    private static function modelName() : string
    {
        $name = strtolower(static::class);
        if ($pos = strrchr($name, '\\')) {
            $name = strtolower(substr($pos, 1));
        }
        return str_replace('search', '', $name);
    }
    
    /**
     * Append scenarios for add translation and update translation
     * @array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        
        $scenarios[static::SCENARIO_ADD_TRANSLATION] = array_merge(static::translateFields(), ['lang_id', 'parent_id', 'lg_id']);
        $scenarios[static::SCENARIO_UPDATE_TRANSLATION] = static::translateFields();
        $scenarios[static::SCENARIO_CREATE] = $this->attributes();
        $scenarios[static::SCENARIO_UPDATE] = array_diff($this->attributes(), ['parent_id', 'lang_id']);
        
        return $scenarios;
    }
    
    /**
     * Check if model is in translate mode
     *
     * @return bool
     */
    public function isTranslate() : bool
    {
        return $this->default_dblang_id !== $this->dblang_id;
    }
    
    /**
     * Set default db lang for model
     *
     * @param int $lang_id [=0]
     * @return int
     */
    public function setDefaultDbLang(int $lang_id = 0) : int
    {
        $this->default_dblang_id = DbLang::setDefaultLangById($lang_id);
        return $this->default_dblang_id;
    }
    
    /**
     * Set current db lang for model
     *
     * @param int $lang_id [=0]
     * @return int
     */
    public function setDbLang(int $lang_id = 0) : int
    {
        if (empty($lang_id)) {
            $lang_id = (int)Yii::$app->request->get('db_lang');
        }
        if (empty($lang_id)) {
            $lang_id = $this->default_dblang_id;
        }
        $this->dblang_id = DbLang::setCurrentLangById($lang_id);
        return $this->dblang_id;
    }
    
    /**
     * Get lang request param value and check if it is valid
     * if it is not valid return default
     *
     * @return int
     */
    public static function getLangParam() : int
    {
        $lang_id = (int) Yii::$app->request->get('db_lang');
        $lang_codes = DbLang::getLangCodes();
        
        if (!isset($lang_codes[$lang_id])) {
            $lang_id = DbLang::getDefaultLangId();
        }
        return $lang_id;
    }

    /**
     * Translate with model translation category
     *
     * @param type $message
     * @param type $params
     * @return type
     */
    public static function t($message, $params = [])
    {
        return Yii::t(static::translationCategory(), $message, $params);
    }
     
    /**
     * Get translation category for model
     *
     * @param string $category_name
     * @return string
     */
    public static function translationCategory(string $category_name = null) : string
    {
        if (empty($category_name)) {
            $category_name = static::modelName();
        }
        return "model/{$category_name}";
    }
    
    /**
     * Get value of one row field (name, title, caption...)
     * because we want every row to have name for user view instead row id
     *
     * @param type $model
     * @return string
     */
    public static function rowName($model) : string
    {
        if (!empty($model->{static::titleAttribute()})) {
            return $model->{static::titleAttribute()};
        }
        if ($model->isTranslate() === true) {
            return $model->parentRowName($model);
        }
        
        return '';
    }
    
    public static function parentRowName($model) : string
    {
        $model = $model->getOneWithParent($model);
        
        if (!empty($model->parent->{static::titleAttribute()})) {
            return $model->parent->{static::titleAttribute()};
        }
        return '';
    }
    
    /**
     * Get value of one parent row field (name, title, caption...)
     * because we want every row to have name for user view instead row id
     *
     * @param type $model
     * @return string
     */
    public static function rowParentName(LangDb $model) : string
    {
        $model->parent_id  = $model->getParentIdAddUpdate($model);
        if (empty($model->parent_id)) {
            return '';
        }
        if (empty($model->parent)) {
            $model = $model->getOneWithParent($model);
        }
        
        if (!empty($model::translateFields()[0]) && !empty($model->parent[$model::translateFields()[0]])) {
            return $model->parent[$model::translateFields()[0]];
        }
        
        return '';
    }
   
    /**
     * Fill model with parent data into $this->parent
     *
     * @param type $model
     * @return type
     */
    public function getOneWithParent(LangDb $model) : LangDb
    {
        if ($model->parent_id != 0) {
            $model->parent = self::findOne([static::primaryKey()[0] => $model->parent_id]);
        }
        return $model;
    }

    /**
     * Get parent id from item (if update) or url (if add)
     *
     * @param type $model
     * @return int
     */
    public function getParentIdAddUpdate($model) : int
    {
        return isset($model->parent_id) ? $model->parent_id : Yii::$app->request->get('parent_id', 0);
    }


    public function formatRefferer()
    {
        $ref =  Yii::$app->request->referrer;
        $controller = Yii::$app->controller->id;
        $ref = substr($ref, strpos($ref, $controller)+strlen($controller));
        return strpos($ref, 'index') ? Yii::$app->request->referrer : 'index';
    }
    
    public function detectViewFile($dir, $file) : string
    {
        $ret = realpath($dir . '/../../' . Yii::$app->controller->id . '/' . basename($file));
        if (empty($ret)) {
            $ret = realpath($dir . '/inc/' . basename($file));
        }
        if (empty($ret)) {
            throw new ErrorException('Shared View found', 5789465);
        }
        return $ret;
    }
}
