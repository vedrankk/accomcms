<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\AccomLanguagesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="accom-languages-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'accom_languages_id') ?>

    <?= $form->field($model, 'accomodation_id') ?>

    <?= $form->field($model, 'lang_id') ?>

    <?= $form->field($model, 'default_lang_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
