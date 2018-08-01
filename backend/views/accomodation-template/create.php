<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\AccomodationTemplate */

$this->title = Yii::t('model/accomtemplates', 'create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('model/accomtemplates', 'Title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="accomodation-template-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
