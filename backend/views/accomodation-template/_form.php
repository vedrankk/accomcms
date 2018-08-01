<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use backend\models\Accomodation;
use backend\models\Templates;
use yii\helpers\ArrayHelper;

$accomodationData = [ArrayHelper::map(Accomodation::find()->andWhere('parent_id IS NULL')->asArray()->all(), 'accomodation_id', 'name')][0];
$templatesData = [ArrayHelper::map(Templates::find()->andWhere('parent_id IS NULL')->asArray()->all(), 'template_id', 'name')][0];

/* @var $this yii\web\View */
/* @var $model backend\models\AccomodationTemplate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="accomodation-template-form">

    <?php $form = ActiveForm::begin(); ?>

    <?=   
        $form->field($model, 'accomodation_id')->widget(Select2::classname(), [
          'data' => $accomodationData,
          'options' => ['placeholder' => Yii::t('model/accomtemplates', 'accomodation_id')],
          'pluginOptions' => [
          ],
        ]);

    ?>

    <?=   
        $form->field($model, 'template_id')->widget(Select2::classname(), [
          'data' => $templatesData,
          'options' => ['placeholder' => Yii::t('model/accomtemplates', 'template_id')],
          'pluginOptions' => [
          ],
        ]);

    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
