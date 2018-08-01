<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\components\User;
use common\models\LoggedInUser;
/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

    <?php
    if(User::isSuperAdmin())
    {
     echo $form->field($model, 'role')->dropDownList([ 'user' => 'User', 'admin' => 'Admin', 'superadmin' => 'Superadmin', ], ['prompt' => 'Select user role']);
     echo $form->field($model, 'email')->textInput(['maxlength' => true]); 
    }
     ?>

    <?= $form->field($model, 'country')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lang')->textInput(['maxlength' => true]) ?>

    <?php //$form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    
    <?php 
        // $form->field($model, 'status')->dropdownList([
        // 10 => 'Active', 
        // 0 => 'Inactive'
        // ],
        // ['prompt'=>'Select Category']
        // ); 
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
