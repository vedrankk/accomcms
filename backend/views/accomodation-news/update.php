<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\AccomodationNews */
$this->title = Yii::t('model/accomnews', 'update', ['headline' => $model->news_headline]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('model/accomnews', 'Title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->news_headline, 'url' => ['view', 'id' => $model->news_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="accomodation-news-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
