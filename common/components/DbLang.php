<?php

namespace common\components;

use Yii;
use yii\base\Component;

class DbLang extends Component
{
    protected static $default_lang_id = 1;
    protected static $current_lang_id = 1;

    public static function primaryKey(): string
    {
        return 'lang_id';
    }
    
    public static function parentKey(): string
    {
        return 'parent_id';
    }
    
    public static function tableName(): string
    {
        return 'languages_db';
    }
        
    /**
     * Get lang codes, short names
     */
    public static function getLangCodes(): array
    {
        $langs = static::getLangsOnLangId(static::$default_lang_id);
        return array_column($langs, 'code', 'lang_id');
    }

    /**
     * Try to set current lang with lang code,
     * if not exist then set default lang and return his code
     */
    public static function setCurrentLang(string $code): string
    {
        $lang_codes = static::getLangCodes();
        if (!$key = array_search($code, $lang_codes)) {
            $key = static::$default_lang_id;
        }

        static::$current_lang_id = $key;

        return $lang_codes[static::$current_lang_id];
    }
    
    /**
     * Try to set current lang with lang id,
     * if not exist then set default lang and return his id
     */
    public static function setCurrentLangById(int $lang_id): int
    {
        $lang_codes = static::getLangCodes();
        
        if (!isset($lang_codes[$lang_id])) {
            $lang_id = static::$default_lang_id;
        }

        static::$current_lang_id = $lang_id;

        return static::$current_lang_id;
    }
    
    /**
     * Try to set default lang with lang id,
     * if not exist then set keep old default lang id and
     * return his id
     */
    public static function setDefaultLangById(int $lang_id): int
    {
        $lang_codes = static::getLangCodes();
        
        if (!isset($lang_codes[$lang_id])) {
            $lang_id = static::$default_lang_id;
        }

        static::$default_lang_id = $lang_id;

        return static::$default_lang_id;
    }

    /**
     * Get current lang id
     */
    public static function getCurrentLangId(): int
    {
        return static::$current_lang_id;
    }

    /**
     * Get default lang id
     */
    public static function getDefaultLangId(): int
    {
        return static::$default_lang_id;
    }
    
    /**
     * Get translated current lang name
     */
    public static function getCurrentLangName(): string
    {
        return static::getLangNameByLangId(static::$current_lang_id);
    }
    
    /**
     * Get translated default lang name
     */
    public static function getDefaultLangName(): string
    {
        return static::getLangNameByLangId(static::$default_lang_id);
    }
    
    /**
     * Get  lang name by lang id
     */
    public static function getLangNameByLangId(int $lang_id, $translated = true)
    {
        $langs = static::getLangs($translated);
        return (isset($langs[$lang_id])) ? $langs[$lang_id]['name'] : '';
    }
    
    /**
     * Get current lang code
     */
    public static function getCurrentLangCode(): string
    {
        return static::getLangCodeByLangId(static::$current_lang_id);
    }
    
    /**
     * Get default lang code
     */
    public static function getDefaultLangCode(): string
    {
        return static::getLangCodeByLangId(static::$default_lang_id);
    }

    
    /**
     * Get current lang code
     */
    public static function getLangCodeByLangId(int $lang_id): string
    {
        $langs = static::getLangs();
        return (isset($langs[$lang_id])) ? $langs[$lang_id]['code'] : '';
    }

    /**
     * Get langs list translated on current lang
     */
    public static function getLangs(bool $merge = true): array
    {
        $default_langs = static::getLangsOnLangId(static::$default_lang_id);
        $current_langs = [];
        if (static::$default_lang_id !== static::$current_lang_id && $merge == true) {
            $current_langs = static::getLangsOnLangId(static::$current_lang_id);
        }

        $langs = static::merge($default_langs, $current_langs, 'lang_id', ['name', 'order']);
        $langs = \yii\helpers\ArrayHelper::index($langs, 'lang_id');

        return $langs;
    }

    /**
     * Get langs for lang_id
     */
    private static function getLangsOnLangId(int $lang_id):array
    {
        $res = Yii::$app->db->createCommand("SELECT lg_id AS lang_id, code, name, parent_id 
                FROM " . static::tableName() . "
                WHERE lang_id=:lang_id 
                ORDER BY `order`, code")
                ->bindValues([':lang_id' => $lang_id])
                ->cache(0, new \yii\caching\TagDependency(['tags' => 'langs']))
                ->queryAll();
        return (array) $res;
    }
    
    public static function idExist($lang_id)
    {
        $lang_codes = static::getLangCodes();
        return isset($lang_codes[$lang_id]);
    }

    /**
     *
     */
    public static function invalidateCache()
    {
        return (new \yii\caching\TagDependency(['tags' => 'langs']))->invalidate(Yii::$app->cache, ['tags' => 'langs']);
    }

    /**
     * Merge array two arrays with diff langs
     * $translate_fields are fields that need to be overwritten (id can be overwritten to)
     * if $translate_fields array is empty then all columns will be overwritten except parent_id
     */
    public static function merge(array $default, array $translate, string $key, array $translate_fields): array
    {
        if (empty($default) || empty($translate)) {
            return $default;
        }

        //move key column to array key
        $keys = array_column($default, $key);
        $default = array_combine($keys, $default);

        foreach ($translate as $row) {
            $parent_id = $row['parent_id'];
            if (isset($default[$parent_id])) {
                foreach ($row as $col_key => $col_val) {
                    if ((empty($translate_fields) || in_array($col_key, $translate_fields)) &&
                            $col_key !== 'parent_id') {
                        $default[$parent_id][$col_key] = $col_val;
                    }
                }
            }
        }
        return $default;
    }
}
