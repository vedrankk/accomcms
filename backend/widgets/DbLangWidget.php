<?php
namespace backend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\components\DbLang;

class DbLangWidget extends Widget
{

    public function run()
    {
        $url = Yii::$app->request->url;
        $base_url = explode('?', $url);
        $base_url = $base_url[0];
        $parsed = parse_url($url);
        if(isset($parsed['query'])){
            $query = $parsed['query'];
            parse_str($query, $params);
            if (isset($params['db_lang'])) { unset($params['db_lang']); }
        }
        $db_lang = DbLang::getCurrentLangId();
  
        $button = Html::button(DbLang::getCurrentLangName() . ' ' . Html::tag('span', '', ['class' => 'caret']), 
                        ['class' => 'btn btn-default  dropdown-toggle',
                         'data-toggle' => 'dropdown'
                        ]);
        $li = [];
        foreach(DbLang::getLangs() as $key => $val)
        {
            $params['db_lang'] = $val['lang_id'];
            $lang_url = sprintf("%s?%s", $base_url, http_build_query($params));
            $li[] =  Html::a($val['name'], $lang_url);
        }
        
        $title_class = DbLang::getCurrentLangId() != DbLang::getDefaultLangId() ? ' alert-danger' : '';
        $title = Html::tag('span', Yii::t('app', 'Module Lang'), ['class' => 'input-group-addon'.$title_class]);
        $ul = Html::ul($li, ['encode'=>false, 'class' => 'dropdown-menu']);
        $html = Html::tag('span', $button . $ul, ['class'=> 'dropdown']);
        $html = Html::tag('div', $title . $html, ['class' => 'input-group']);
        return  $html;
    }
}