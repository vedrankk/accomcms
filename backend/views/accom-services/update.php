<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\AccomServices */

$this->title = 'Update Accom Services: ' . $model->accom_services_id;
$this->params['breadcrumbs'][] = ['label' => 'Accom Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->accom_services_id, 'url' => ['view', 'id' => $model->accom_services_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="accom-services-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
