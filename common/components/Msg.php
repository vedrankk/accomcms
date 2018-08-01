<?php

namespace common\components;

use Yii;
use yii\base\Component;

class Msg extends Component
{
    public static function error($msg)
    {
        self::addMsg('error', $msg);
    }
    public static function danger($msg)
    {
        self::addMsg('danger', $msg);
    }
    public static function success($msg)
    {
        self::addMsg('success', $msg);
    }
    public static function info($msg)
    {
        self::addMsg('info', $msg);
    }
    public static function warning($msg)
    {
        self::addMsg('warning', $msg);
    }
    
    private static function addMsg($type, $msg)
    {
        Yii::$app->session->addFlash($type, $msg);
    }
}
