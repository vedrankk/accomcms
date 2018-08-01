<?php
namespace common\components;

use Yii;

class User extends \common\models\User
{
    public static function isUser() : bool
    {
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role == self::ROLE_USER) {
            return true;
        }
        return false;
    }

    public static function isAdmin() : bool
    {
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role == self::ROLE_ADMIN) {
            return true;
        }
        return false;
    }

    public static function isSuperAdmin() : bool
    {
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role == self::ROLE_SUPERADMIN) {
            return true;
        }
        return false;
    }
}
