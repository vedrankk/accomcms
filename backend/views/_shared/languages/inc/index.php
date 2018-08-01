<?php

use yii\helpers\Html;
use backend\components\grid\LangGridView;
use backend\widgets\DbLangWidget;

$this->title = $model::t('Title');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="<?=Yii::$app->controller->id?>-index">
      
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="panel">
        <?=  DbLangWidget::widget(); ?>
        <br>
         
    <?php if (!$model->isTranslate()):?>
        <?= Html::a(Yii::t('app', 'Create', ['item_name' => $model::t('Lang')]), ['create'], ['class' => 'btn btn-success']) ?>
    <?php endif; ?>
    </div>
    
    <?= LangGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $searchModel::gridColumns(),
    ]); ?>
</div>
