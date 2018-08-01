<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\components\LangActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\Services */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="services-form">

    <?php $form = LangActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'alt')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'lang_id')->hiddenInput(['value' => $model->dblang_id])->label(false) ?>

    <?= $form->field($model, 'parent_id')->hiddenInput(['value' => $parent_id])->label(false) ?>

    <?= $form->field($model, 'ico')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'image')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php LangActiveForm::end(); ?>

</div>
