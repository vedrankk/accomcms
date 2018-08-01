<?php
$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'class' => 'common\components\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'lang' => [
            'class' => 'common\components\WebsiteLang',
        ],

        'request' => [
            'cookieValidationKey' => 'aWdAwIJdAaAohDS',
        ],
        
        'user' => [
            'identityClass' => 'common\components\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity', 'httpOnly' => true, 'domain' => $params['cookieDomain']],
        ],
        
        'session' => [
            'name' => 'advanced-session',
            'cookieParams' => ['httponly' => true, 'domain' => $params['cookieDomain']],
        ],
        
    ],
];
