<?php
$this->context->layout = 'create';
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
$this->registerJsFile('https://use.fontawesome.com/a322008483.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('/js/creation/languages.js', ['depends' => [backend\components\MaterializeAsset::className(), yii\web\JqueryAsset::className()]]);
$langData = [ArrayHelper::map(\backend\models\LanguagesDb::find()->andWhere('parent_id IS NULL')->asArray()->all(), 'lg_id', 'name')][0];
?>

<div class="container">
    <h1>Choose languages for your accomodation</h1>
    
            <?php $form = ActiveForm::begin() ?>
            <?= $form->field($model, 'lang_id')->dropDownList($langData, ['multiple' => 'multiple', 'prompt' => 'Select languages...']) ?>
            <?= Html::submitButton('Save', ['class' => 'btn col s6']) ?>
            <?php ActiveForm::end() ?>
    
    <div id="selectedLangs"></div>
</div>

