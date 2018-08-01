<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Accomodation;
use kartik\select2\Select2;
use backend\models\Domains;
use yii\helpers\ArrayHelper;

$accomodationData = [ArrayHelper::map(Accomodation::find()->andWhere('parent_id IS NULL')->asArray()->all(), 'accomodation_id', 'name')][0];
$domainData = [ArrayHelper::map(Domains::getAvaliableDomains($model->isNewRecord, $model->domain_id), 'domain_id', 'domain_url')][0];
/* @var $this yii\web\View */
/* @var $model backend\models\AccomodationDomain */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="accomodation-domain-form">

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
        $form->field($model, 'domain_id')->widget(Select2::classname(), [
          'data' => $domainData,
          'options' => ['placeholder' => 'Domain'],
          'pluginOptions' => [
          ],
        ]);

    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
