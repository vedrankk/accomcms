<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\User;
/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->first_name .' ' .$model->last_name ;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('yii', 'Update'), ['update', 'id' => $model->user_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('yii', 'Delete'), ['delete', 'id' => $model->user_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'first_name',
            'last_name',
            'role',
            'country',
            'lang',
            'email:email',
            [
              'attribute' => 'status',
               'value'=> $model->status == User::USER_ACTIVE ? 'Active' : 'Inactive',
            ],
            [
                'attribute' => 'created_at',
                'value' => function($model){return User::formatDate($model->created_at);},
            ],
        ],
    ]) ?>

</div>
