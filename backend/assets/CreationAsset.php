<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class CreationAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/materialize/materialize.min.css',
        'https://fonts.googleapis.com/icon?family=Material+Icons'
    ];
    public $js = [
        '/js/materialize/materialize.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
