<?php


namespace backend\components;

use Yii;

class MaterializeAsset extends \yii\web\AssetBundle
{
    /**
     * @var string the directory that contains the source asset files for this asset bundle.
     */
    public $sourcePath = 'js/materialize/';
    
    /**
     * @var array list of JS files that this bundle contains.
     */
    public $js = [
        'materialize.min.js'
    ];
    /**
     * @var array list of bundle class names that this bundle depends on.
     */
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
