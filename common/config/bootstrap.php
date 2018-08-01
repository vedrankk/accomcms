<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@frontend_url', YII_ENV != 'dev' ? 'http://www.accomcms.com' : 'http://www.accomcms.dev');
Yii::setAlias('@backend_url', YII_ENV != 'dev' ? 'http://admin.accomcms.com' : 'http://admin.accomcms.dev');
