<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\DomainsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="domains-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'domain_id') ?>

    <?= $form->field($model, 'domain_url') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('model/accomdomain', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('model/accomdomain', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
