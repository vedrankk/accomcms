<?php
namespace backend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\models\Accomodation;
use backend\models\AccomServices;

class AccomServiceWidget extends Widget
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
            if (isset($params['accom'])) { unset($params['accom']); }
        }
        $accomodations = [ArrayHelper::map(Accomodation::find()->andWhere('parent_id IS NULL')->asArray()->all(), 'accomodation_id', 'name')];
  
        $button = Html::button(AccomServices::currentAccomodationDisplay() . ' ' . Html::tag('span', '', ['class' => 'caret']), 
                        ['class' => 'btn btn-default  dropdown-toggle',
                         'data-toggle' => 'dropdown'
                        ]);
        $li = [Html::a(Yii::t('model/accomservices', 'All'), ['index'])];
        foreach($accomodations[0] as $key => $val)
        {
            $params['accom'] = $key;
            $url = sprintf("%s?%s", $base_url, http_build_query($params));
            $li[] =  Html::a($val, $url);
        }
        
        $title_class = AccomServices::currentAccomodationDisplay() != 0 ? ' alert-danger' : '';
        $title = Html::tag('span', Yii::t('model/accomservices', 'accom'), ['class' => 'input-group-addon'.$title_class]);
        $ul = Html::ul($li, ['encode'=>false, 'class' => 'dropdown-menu']);
        $html = Html::tag('span', $button . $ul, ['class'=> 'dropdown']);
        $html = Html::tag('div', $title . $html, ['class' => 'input-group']);
        return  $html;
    }
}