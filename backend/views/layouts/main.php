<?php

/**
 * @var string $content
 * @var \yii\web\View $this
 */

use yii\helpers\Html;
use common\widgets\Alert;
use yii\widgets\Breadcrumbs;

$bundle = yiister\gentelella\assets\Asset::register($this);

?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta charset="<?= Yii::$app->charset ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="nav-<?= !empty($_COOKIE['menuIsCollapsed']) && $_COOKIE['menuIsCollapsed'] == 'true' ? 'sm' : 'md' ?>" >
<?php $this->beginBody(); ?>
<div class="container body">

    <div class="main_container">
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">

                <?= Yii::$app->controller->renderPartial('//layouts/menuPartial/_upperSideMenu') ?>
                
                <br />
                
                <?= Yii::$app->controller->renderPartial('//layouts/menuPartial/_menuContent') ?>
                
               <?= Yii::$app->controller->renderPartial('//layouts/menuPartial/_sidebarFooter') ?>
                
            </div>
        </div>
        
        <?= Yii::$app->controller->renderPartial('//layouts/menuPartial/_topNavigation') ?>
        
        <div class="right_col" role="main">
            <div class="clearfix"></div>
            <?= Breadcrumbs::widget([
                    'homeLink' => false,
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
        <footer>
            <div class="pull-right">
               
            </div>
            <div class="clearfix"></div>
        </footer>
    </div>

</div>

<?= Yii::$app->controller->renderPartial('//layouts/menuPartial/_notification') ?>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
