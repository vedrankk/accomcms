<?php
$this->context->layout = 'create';
use yii\widgets\ActiveForm;
$this->registerJsFile('https://use.fontawesome.com/a322008483.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('/js/creation/emails.js', ['depends' => [backend\components\MaterializeAsset::className(), yii\web\JqueryAsset::className()]]);
?>

<div class="container">
    <h1>Input an emails(s) for your accomodation</h1>
     <?php $form = ActiveForm::begin() ?>
    
    <?= $form->field($model, 'email') ?>
    <?= $form->field($model, 'title') ?>
    <button class="btn btn-waves" type="button" id="save-email">Save</button>
    <?php ActiveForm::end() ?>
    
    <div class="active-emails"></div>
</div>

