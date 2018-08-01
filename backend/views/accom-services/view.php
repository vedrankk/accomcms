<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\AccomServices */

$this->title = sprintf('%s', $model->name);
$this->params['breadcrumbs'][] = ['label' => Yii::t('model/accomservices', 'Title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="accom-services-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= sprintf('<p>%s</p>',Html::a(Yii::t('model/accomservices', 'create'), ['create', 'id' => $model->accomodation_id], ['class' => 'btn btn-success'])) ?>

    <table class="table">
        <thead>
            <tr>
                <th><?= Yii::t('model/accomservices', 'service_name') ?></th>
                <th><?= Yii::t('model/accomservices', 'actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($completeModel as $key => $val){ ?>
            <tr>
                <td><?= $val['service_name'] ?></td>
                <td><?= Html::a(Html::tag('i', '', ['class' => 'glyphicon glyphicon-trash']),['delete', 'id' => $val['accom_services_id']], ['data' => ['method' => 'post']]) ?></td>
            </tr>
            
            <?php } ?>
        </tbody>
    </table>

</div>
