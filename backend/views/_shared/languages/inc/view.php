<?php

use yii\helpers\Html;
use backend\components\LangDetailView;
use common\components\DbLang;

$action_suffix = $model->isTranslate() ? '-translation' : '';
$title = $model->rowName($model);
$this->title = Yii::t('app', 'view-title', ['item_name' => $title]);
$this->params['breadcrumbs'][] = ['label' => $model->t('Title'), 'url' => ['index', 'db_lang' => $model::getLangParam()]];
$this->params['breadcrumbs'][] = $title;
?>

<div class="<?=Yii::$app->controller->id?>-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <h3><?= Yii::t('app', 'On language', ['item_name' => DbLang::getLangNameByLangId($model->lang_id)]) ?></h3>
    <p>
        <?= Html::a(Yii::t('app', 'Update'), ["update{$action_suffix}", 'id' => $model->getPrimaryKey(), 'db_lang' => $model::getLangParam()], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ["delete{$action_suffix}", 'id' => $model->getPrimaryKey(), 'db_lang' => $model::getLangParam()], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'confirm_delete'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?= LangDetailView::widget([
        'model' => $model,
        'attributes' => $model::viewAttributes(),
    ]) ?>

</div>
