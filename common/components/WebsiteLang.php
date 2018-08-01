<?php

namespace common\components;

use Yii;
use yii\base\Component;

class WebsiteLang extends DbLang
{
    protected static $default_lang_id = 1;
    protected static $current_lang_id = 1;
    
    public static function tableName(): string
    {
        return 'languages_website';
    }
}
