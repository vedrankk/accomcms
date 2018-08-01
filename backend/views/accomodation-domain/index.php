<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AccomodationDomainSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('model/accomdomain', 'Title');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="accomodation-domain-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'action-create'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'accomodation_id',
                'value' => 'accomodation.name'
            ],
            [
                'attribute' => 'domain_id',
                'value' => 'domain.domain_url'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
