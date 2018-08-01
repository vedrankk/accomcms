<?php

use yii\helpers\Html;
use backend\components\LangActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\Accomodation;
use kartik\select2\Select2;
$data = [ArrayHelper::map(Accomodation::find()->andWhere('parent_id IS NULL')->asArray()->all(), 'accomodation_id', 'name')];
/* @var $this yii\web\View */
/* @var $model backend\models\Emails */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="emails-form">

    <?php $form = LangActiveForm::begin(); ?>

    <?= $form->field($model, 'lang_id')->hiddenInput(['value' => $model->dblang_id])->label(false) ?>

    <?= $form->field($model, 'parent_id')->hiddenInput(['value' => $parent_id])->label(false) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    
    <?php   
        echo $form->field($model, 'accomodation_id')->widget(Select2::classname(), [
          'data' => $data,
          'options' => ['placeholder' => $model::t('accomodation_choose')],
          'pluginOptions' => [
          ],
        ]);

    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php LangActiveForm::end(); ?>

</div>
