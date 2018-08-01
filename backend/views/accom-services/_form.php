<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\Accomodation;
use backend\models\Services;
use kartik\select2\Select2;

$accomodationData = [ArrayHelper::map(Accomodation::find()->andWhere('parent_id IS NULL')->asArray()->all(), 'accomodation_id', 'name')][0];
$servicesData = [ArrayHelper::map(Services::find()->andWhere('parent_id IS NULL')->asArray()->all(), 'services_id', 'name')][0];
/* @var $this yii\web\View */
/* @var $model backend\models\AccomServices */
/* @var $form yii\widgets\ActiveForm */
$id != '' ? $model->accomodation_id = $id : '';
?>

<div class="accom-services-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?=   
        $form->field($model, 'accomodation_id')->widget(Select2::classname(), [
          'data' => $accomodationData,
          'options' => ['placeholder' => 'Accomodation'],
          'pluginOptions' => [
          ],
        ]);

    ?>

    <?=   
        $form->field($model, 'services_id')->widget(Select2::classname(), [
          'data' => $servicesData,
          'options' => ['placeholder' => 'Services'],
          'pluginOptions' => [
              'multiple' => true,
          ],
        ]);

    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
