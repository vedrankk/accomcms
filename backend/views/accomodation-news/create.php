<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\AccomodationNews */

$this->title = Yii::t('model/accomnews', 'create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('model/accomnews', 'Title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="accomodation-news-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
