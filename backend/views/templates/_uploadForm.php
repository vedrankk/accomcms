<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Templates */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="templates-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'lang_id')->hiddenInput(['value' => $model->dblang_id])->label(false) ?>

    <?= $form->field($model, 'parent_id')->hiddenInput(['value' => null])->label(false) ?>

    <?= $form->field($model, 'name')->hiddenInput(['maxlength' => true, 'value' => 'a'])->label(false) ?>
    
    <?= $form->field($model, 'user_id')->hiddenInput(['value' => Yii::$app->user->identity->user_id])->label(false) ?>

    <?= $form->field($model, 'description')->hiddenInput(['rows' => 6, 'value' => 'a'])->label(false) ?>

    <?= $form->field($model, 'path')->fileInput(['multiple' => false]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php yii\widgets\ActiveForm::end(); ?>

</div>
