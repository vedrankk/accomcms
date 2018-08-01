<?php

use yii\helpers\Html;
use backend\components\LangActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Accomodation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="accomodation-form">

    <?php $form = LangActiveForm::begin(); ?>

    <?= $form->field($model, 'lang_id')->hiddenInput(['value' => $model->dblang_id])->label(false) ?>

    <?= $form->field($model, 'parent_id')->hiddenInput(['value' => $parent_id])->label(false) ?>
    
    <?= $form->field($model, 'user_id')->hiddenInput(['value' => Yii::$app->user->identity->user_id])->label(false) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'facebook')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'twitter')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'youtube')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'published')->dropdownList(['1' => Yii::t('app', 'True'), '0' => Yii::t('app', 'False')], ['prompt' => Yii::t('app', 'Choose')]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php LangActiveForm::end(); ?>

</div>
