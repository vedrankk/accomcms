<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Domains */

$this->title = Yii::t('app', 'action-update', ['item-name' => $model->domain_url]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('model/domains', 'Title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->domain_id, 'url' => ['view', 'id' => $model->domain_url]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="domains-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
