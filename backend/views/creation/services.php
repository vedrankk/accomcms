<?php
$this->context->layout = 'create';
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use backend\models\Services;
use yii\widgets\ActiveForm;
$this->registerJsFile('https://use.fontawesome.com/a322008483.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
//$this->registerJsFile('js/materialize/materialize.min.css');
$this->registerJsFile('/js/creation/services.js', ['depends' => [backend\components\MaterializeAsset::className(), yii\web\JqueryAsset::className()]]);
?>

<div class="container">
    <h1>Choose services that your accomodation has</h1>
    <div class="input-field col s12">
        <form action="choose-services" method="POST">
            <?php $form = ActiveForm::begin() ?>
            <?= $form->field($model, 'services_id')->dropDownList($services, ['prompt' => empty($services) ? 'There are no more avaliable services!' : 'Select services...', 'multiple' => 'multiple']) ?>
            <?= Html::submitButton('Save', ['class' => 'btn col s6']) ?>
            <a href="choose-languages" class="btn right col s6">Skip</a>
            <?php ActiveForm::end() ?>
            
        </form>
  </div>
</div>

