<?php
namespace backend\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use common\components\WebsiteLang;
use Yii;

class BackendLanguagePicker extends Widget
{
    private $url;

    public function init()
    {
        parent::init();
        $temp_url = explode('/', Yii::$app->request->url);
        unset($temp_url[1]);
        unset($temp_url[0]);
        $this->url = implode('/', $temp_url);
    }

    public function run()
    {
        $button = Html::a(WebsiteLang::getCurrentLangName() . ' ' . Html::tag('span', '', ['class' => ' fa fa-angle-down']), 'javascript:;', 
                        ['class' => 'user-profile dropdown-toggle',
                         'data-toggle' => 'dropdown',
                         'aria-expanded' => 'false',
                        ]);
        $li = [];
        foreach(WebsiteLang::getLangs() as $key => $val){
            $url = sprintf('/%s/%s', $val['code'], $this->url);
            $li[] = Html::a($val['name'], $url);
        }
        $ul = Html::ul($li, ['encode'=>false, 'class' => 'dropdown-menu dropdown-usermenu pull-right']);
        $html = $button .$ul;
        return $html;
        $html = Html::tag('span', $button . $ul, ['class'=> 'dropdown']);
        $html = Html::tag('div',  $html, ['class' => 'input-group']);
        return $html;
    }
}