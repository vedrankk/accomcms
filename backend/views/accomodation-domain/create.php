<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\AccomodationDomain */

$this->title = Yii::t('model/accomdomain', 'create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('model/accomdomain', 'Title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="accomodation-domain-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
