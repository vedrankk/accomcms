<?php
use yii\helpers\Html;
use backend\components\LangActiveForm;
?>

<div class="<?=Yii::$app->controller->id?>-form">

    <?php $form = LangActiveForm::begin(); ?>

    <?= $form->field($model, 'lang_id')->hiddenInput(['value' => $model->dblang_id])->label(false) ?>

    <?= $form->field($model, 'parent_id')->hiddenInput(['value' => $parent_id])->label(false) ?>
    
    <?= $form->field($model, 'code')->textInput()  ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order')->textInput(['value' => 0]) ?>

    <?= $form->field($model, 'ico')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'image')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Save') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php LangActiveForm::end(); ?>

</div>
