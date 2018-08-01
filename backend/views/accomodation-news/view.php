<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\AccomodationNews */

$this->title = $model->news_headline;
$this->params['breadcrumbs'][] = ['label' => Yii::t('model/accomnews', 'Title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="accomodation-news-view">
    
    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->news_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->news_id], [
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
                'value' => $model->accomodation->name
            ],
            [
                'attribute' => 'lang_id',
                'value' => $model->lang->name
            ],
            'news_headline',
            'news_text:raw',
        ],
    ]) ?>

</div>
