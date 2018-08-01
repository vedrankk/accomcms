<?php

namespace common\components;

use yii\filters\AccessControl as Control;
use yii\web\NotFoundHttpException;
use Yii;

class AccessControl extends Control
{
    /**
    * Inherit
    */
    protected function denyAccess($user)
    {
        if ($user !== false && $user->getIsGuest()) {
            $user->loginRequired();
        } else {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found'));
        }
    }
}
