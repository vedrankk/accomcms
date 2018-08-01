<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
Hello <?= sprintf('%s %s', $user->first_name, $user->last_name) ?>,

Follow the link below to reset your password:

<?= $resetLink ?>
