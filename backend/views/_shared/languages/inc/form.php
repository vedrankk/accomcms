<?php

use yii\helpers\Html;
use common\components\DbLang;

$this->title = Yii::t('app', 'action-' . $this->context->action->id, ['item_name' => $model::rowName($model)]);
$this->params['breadcrumbs'][] = ['label' => $model::t('Title'), 'url' => ['index', 'db_lang' => $model::getLangParam()]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?=Yii::$app->controller->id?>-<?=$this->context->action->id?>">

    <h1><?= Html::encode($this->title) ?></h1>
    <h3><?= Yii::t('app', 'On language', ['item_name' => DbLang::getLangNameByLangId($model->dblang_id)]) ?> </h3>
    <?php if ($model->isTranslate()):?>
        <h3><?= Yii::t('app', 'Original value', ['item_name' => $model::rowParentName($model)]) ?> </h3>
    <?php endif; ?>

    <?= $this->render('_form', [
        'model' => $model,
        'parent_id' => $parent_id,
    ]) ?>

</div>
