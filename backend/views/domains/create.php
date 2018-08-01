<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Domains */

$this->title = Yii::t('model/domains', 'create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('model/domains', 'Title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="domains-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
