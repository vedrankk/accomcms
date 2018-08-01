<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="languages-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'lg_id') ?>

    <?= $form->field($model, 'lang_id') ?>

    <?= $form->field($model, 'parent_id') ?>

    <?= $form->field($model, 'code') ?>

    <?= $form->field($model, 'name') ?>

    <div class="form-group">
        <?= Html::submitButton($model::t('Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton($model::t('Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
