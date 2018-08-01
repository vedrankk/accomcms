<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\AccomServices */

$this->title = 'create';
$this->params['breadcrumbs'][] = ['label' => Yii::t('model/accomservices', 'Title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$accom_id = Yii::$app->request->get('id') !== null ? Yii::$app->request->get('id') : ''; 
?>
<div class="accom-services-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'id' => $accom_id,
    ]) ?>

</div>
