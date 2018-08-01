<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\AccomLanguages */

$this->title = Yii::t('model/accomlanguages', 'create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('model/accomlanguages', 'Title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="accom-languages-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
