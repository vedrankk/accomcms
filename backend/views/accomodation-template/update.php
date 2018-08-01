<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\AccomodationTemplate */

$this->title = Yii::t('app', 'action-update', ['item_name' => $model->accom_template_id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('model/accomtemplates', 'Title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->accom_template_id, 'url' => ['view', 'id' => $model->accom_template_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="accomodation-template-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
