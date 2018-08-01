<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AccomLanguagesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('model/accomlanguages', 'Title');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="accom-languages-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('model/accomlanguages','create'), ['create'], ['class' => 'btn btn-success']) ?>
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
                'attribute' => 'lang_id',
                'value' => 'lang.name'
            ],
            [
                'attribute' => 'default_lang_id',
                'value' => function($model){return $model->default_lang_id != 0 ? $model->lang->name : '/';}
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
