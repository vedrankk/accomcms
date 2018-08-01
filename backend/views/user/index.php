<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\components\User;
/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('model/user','Title');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <h1><?= Html::encode($this->title) ?></h1>
        <?php 
            if($model::isSuperAdmin()){
                echo Html::a(Yii::t('app', 'Create', ['item_name' => Yii::t('model/user','User')]), ['create'], ['class' => 'btn btn-success']); 
            }
        ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            
            'first_name',
            'last_name',
            [
             'visible' => (Yii::$app->user->identity->role == User::ROLE_SUPERADMIN),
             'attribute' => 'role',
            ],
            'country',
             'lang',
            // 'auth_key',
            // 'password_hash',
            // 'password_reset_token',
             'email:email',
            [
                'attribute' => 'status',
                'value'=> function($model){return $model->status == backend\models\User::USER_ACTIVE ? 'Active' : 'Inactive';}, 
            ],

            [
                'attribute' => 'created_at',
                'value' => function($model){return Yii::$app->formatter->asDate($model->created_at);},
            ],
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
