<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\widgets\AccomServiceWidget;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\AccomServicesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('model/accomservices', 'Title');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="accom-services-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?= Html::a(Yii::t('model/accomservices', 'create'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            [
                'attribute' => 'accomodation_id',
                'value' => 'accomodation.name',
            ],
            [
                'attribute' => 'services_id',
                'format' => 'html',
                'value' => function($model){return Html::a(Yii::t('model/accomservices', 'check_details'), ['view', 'id' => $model->accomodation_id]); },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                 'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => Yii::t('app', 'View'),
                        ]);
                    },

                 ],
                 'urlCreator' => function ($action, $model, $key, $index) {
                     if ($action === 'view') {
                         $url = \yii\helpers\Url::to(['view', 'id' => $model->accomodation_id]);
                        return $url;
                    }
                 }
            ],
        ],
    ]); ?>
</div>
