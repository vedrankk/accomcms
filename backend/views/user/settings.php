<?php
use yii\helpers\Html;

$this->title = Yii::t('yii', 'Settings') .' : ' . $data->first_name .' ' .$data->last_name;

$this->params['breadcrumbs'][] = ['label' => Yii::t('yii', 'Profile'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('yii', 'Settings');
?>
<div class="user-settings">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_settings_form', [
        'data' => $data,
    ]) ?>

</div>