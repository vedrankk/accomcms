<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\AccomodationDomain */

$this->title = Yii::t('app', 'action-update', ['item_name' => $model->accomodation->name] );
$this->params['breadcrumbs'][] = ['label' => Yii::t('model/accomdomain', 'Title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->accomodation->name, 'url' => ['view', 'id' => $model->accomdomain_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="accomodation-domain-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
