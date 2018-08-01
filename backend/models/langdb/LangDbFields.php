<?php

namespace backend\models\langdb;

trait LangDbFields
{
    /**
     * Get default translate columns/fields for all translatable db tables
     *
     * @return array
     */
         
    public static function defaultTranslateFields() : array
    {
        return [static::primaryKey()[0], 'parent_id', 'lang_id'];
    }
    
    /**
     * Get default and custom translate db table columns
     *
     * @return array
     */
    public static function mergedTranslateFields() : array
    {
        return array_merge(static::defaultTranslateFields(), static::translateFields());
    }
    
    /**
     * Get default sql columns, original and translated
     *
     * @param string $alias [='o']
     * @return array
     */
    public function getDefaultColumns(string $alias = "o") : array
    {
        $attr = $this->attributes();
        if (empty($alias)) {
            return $attr;
        } else {
            $alias =  "{$alias}.";
        }
        
        $attr = array_map(function ($value) use ($alias) {
            return "{$alias}{$value}";
        }, $attr);
        foreach (static::translateFields() as $val) {
            $attr[] = "('') AS t_{$val}";
        }
        return $attr;
    }
    
    /**
     * Get translate columns and parent columns
     *
     * @param string $alias_translate [='t']
     * @param string $alias_parent [='p']
     * @return array
     */
    public function getTranslateColumns(string $alias_translate = "t", string $alias_parent = "p"): array
    {
        $attr = $this->attributes();
        $translate = $this->defaultTranslateFields();
        $ret = [];
        foreach ($attr as $key => $val) {
            $alias = in_array($val, $this->defaultTranslateFields()) ? $alias_translate : $alias_parent;
            $ret[] = "{$alias}.{$val}" ;
        }
        
        foreach (static::translateFields() as $val) {
            $ret[] = "{$alias_translate}.{$val} AS t_{$val}";
        }
        return $ret;
    }
}
