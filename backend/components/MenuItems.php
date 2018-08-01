<?php

namespace backend\components;

use Yii;
use common\components\User;
use yii\helpers\Html;

class MenuItems
{
    public function getItems()
    {
        $items = [
                [
            'url' => ['/site/index'],
            'label' => Yii::t('app', 'Home'),
            'icon' => 'home'
        ],
                [
            'url' => '@frontend_url',
            'label' => Yii::t('app', 'Go to Frontend')
        ],
                [
            'url' => '#',
            'label' => Yii::t('app', 'Settings'),
            'icon' => 'cog',
            'items' => self::getSettingsItems(),
        ],
                [
            'url' => ['/site/logout'],
            'label' => Yii::t('app', 'Logout'),
                        'template' => '<a href="{url}" data-method="post">{icon}{label}</a>'
        ],
            ];
        return $items;
    }
        
    private function getSettingsItems()
    {
        return User::isUser() ? MenuItems::userSettingsItems() : MenuItems::adminSettingsItems();
    }
    private function adminSettingsItems()
    {
        $items = [];
        if (User::isSuperAdmin()) {
            $items[] = ['label' => Yii::t('app', 'Users'), 'icon'=>'user', 'url'=>['/users']];
        }

        if (User::isAdmin() || User::isSuperAdmin()) {
            $items[] = ['label' => Yii::t('app', 'DB languages'), 'icon'=>'globe', 'url'=>['/languages-db']];
            $items[] = ['label' => Yii::t('app', 'Website languages'), 'icon'=>'language', 'url'=>['/languages-website']];
            $items[] = ['label' => Yii::t('app', 'Services'), 'icon'=>'info', 'url'=>['/services']];
            $items[] = ['label' => Yii::t('app', 'Accomodation'), 'icon'=>'hotel', 'url'=>['/accomodation']];
            $items[] = ['label' => Yii::t('app', 'Emails'), 'icon'=>'envelope', 'url'=>['/emails']];
            $items[] = ['label' => Yii::t('app', 'AccomServices'), 'icon'=>'info-circle', 'url'=>['/accom-services']];
            $items[] = ['label' => Yii::t('app', 'AccomLangs'), 'icon'=>'language', 'url'=>['/accom-languages']];
            $items[] = ['label' => Yii::t('app', 'Overview'), 'icon'=>'cogs', 'url'=>['/overview']];
            $items[] = ['label' => Yii::t('app', 'Galleries'), 'icon'=>'picture-o', 'url'=>['/galleries']];
            $items[] = ['label' => Yii::t('app', 'AccomNews'), 'icon'=>'newspaper-o', 'url'=>['/accomodation-news']];
            $items[] = ['label' => Yii::t('app', 'Templates'), 'icon'=>'sticky-note', 'url'=>['/templates']];
            $items[] = ['label' => Yii::t('app', 'Accomodation Templates'), 'icon'=>'sticky-note', 'url'=>['/accomodation-template']];
            $items[] = ['label' => Yii::t('app', 'Accomodation Domains'), 'icon'=>'sticky-note', 'url'=>['/accomodation-domain']];
            $items[] = ['label' => Yii::t('app', 'Domains'), 'icon'=>'sticky-note', 'url'=>['/domains']];
            $items[] = ['label' => Yii::t('app', 'Template Options'), 'icon'=>'sticky-note', 'url'=>['/template-options']];
        }
                
        return $items;
    }

    private function userSettingsItems()
    {
        $items = [];
        return $items;
    }
        
    private function defaultSettingItems()
    {
    }
}
