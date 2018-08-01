<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\User;

?>

<div class="user-form col-lg-6 col-md-6">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($data, 'first_name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($data, 'last_name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($data, 'email')->textInput(['maxlength' => true]) ?>

	<?= $form->field($data, 'country')->textInput(['maxlength' => true]) ?>

	<?= $form->field($data, 'lang')->textInput(['maxlength' => true]) ?>

	 <div class="form-group">
        <?= Html::submitButton($data->isNewRecord ? 'Create' : 'Update', ['class' => $data->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

	<?php ActiveForm::end(); ?>
</div>