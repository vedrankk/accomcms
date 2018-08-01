<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
         'i18n' => [
        'translations' => [
            'app*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@app/messages',
                'sourceLanguage' => 'en-US',
                'fileMap' => [
                    'app' => 'app.php',
                    'app/error' => 'error.php',
                ],
            ],
            'model*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@app/messages',
                'sourceLanguage' => 'en-US',
                'fileMap' => [
                    'model/languageswebsite' =>  'languageswebsite.php',
                    'model/languagesdb' => 'languagesdb.php',
                    'model/services' => 'services.php',
                    'model/accomodation' => 'accomodation.php',
                    'model/emails' => 'emails.php',
                    'model/accomservices' => 'accomservices.php',
                    'model/accomlanguages' => 'accomlanguages.php',
                    'model/overview' => 'overview.php',
                    'model/galleries' => 'galleries.php',
                    'model/accomnews' => 'accomnews.php',
                    'model/templates' => 'templates.php',
                    'model/accomtemplates' => 'accomtemplates.php',
                    'model/accomdomain' => 'accomdomain.php',
                    'model/domains' => 'domains.php',
                    'model/user' => 'user.php',
                    'model/index' => 'start.php',
                ],
              'on missingTranslation' => ['backend\components\TranslationEventHandler', 'handleMissingTranslation']
            ],
        ],
    ],
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

         'urlManager' => [
                'class' => 'backend\components\UrlManagerBackend',
                'enablePrettyUrl' => true,
                'showScriptName' => false,
                'enableStrictParsing' => false,
                'rules' => [
                    'users' => 'user/index',
                    'users/profile' => 'user/profile',
                    'users/<id:\d+>' => 'user/view',
                    'users/settings' => 'user/settings',
                    'users/update/<id:\d+>' => 'user/update',
            ],
        ],
        'view' => [
            'renderers' => [
                'tpl' => [
                    'class' => 'yii\smarty\ViewRenderer',
                    //'cachePath' => '@runtime/Smarty/cache',
                ],
            ],
        ],
        
 
    ],
    'params' => $params,
];
