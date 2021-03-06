<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\AccomodationDomain */

$this->title = $model->accomodation->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('model/accomdomain', 'Title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="accomodation-domain-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->accomdomain_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->accomdomain_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'confirm_delete'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            
            [
                'attribute' => 'accomodation_id',
                'value' => function($model){return $model->accomodation->name;}
            ],
            [
                'attribute' => 'domain_id',
                'value' => function($model){return $model->domain->domain_url;}
            ],
        ],
    ]) ?>

</div>
