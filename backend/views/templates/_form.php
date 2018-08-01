<?php

use yii\helpers\Html;
use backend\components\LangActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Templates */
/* @var $form yii\widgets\ActiveForm */
$this->registerCssFile('/css/snackbar.css');
$this->registerJsFile('js/templates.js', ['depends' => [yii\web\JqueryAsset::className()]]);
?>

<div class="templates-form">

    <?php $form = LangActiveForm::begin(); ?>

    <?= $form->field($model, 'lang_id')->hiddenInput(['value' => $model->dblang_id])->label(false) ?>

    <?= $form->field($model, 'parent_id')->hiddenInput(['value' => $parent_id])->label(false) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'path')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php LangActiveForm::end(); ?>
    <div id="snackbar"></div>
</div>
