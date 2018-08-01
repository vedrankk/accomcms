<?php
use yii\helpers\Html;
use backend\models\Galleries;
$this->title = Yii::t('model/galleries', 'view_g');
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('model/galleries', 'Title') , 'url' => ['index', 'db_lang' => Galleries::getLangParam()]];
$this->params['breadcrumbs'][] = ['label' => $this->title];
$this->registerCss('.img-wrap {display:inline-block;position:relative;} .delete-button{position:absolute;top:0;left:0;font-size: 2em;}');
$this->registerCssFile('https://fonts.googleapis.com/css?family=Roboto|Varela+Round');
$this->registerCssFile('https://fonts.googleapis.com/icon?family=Material+Icons');
$this->registerCssFile('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
$this->registerCssFile('/css/galleryView.css');
/*
 * Displays all the galleries for the current accomodation
 */
?>

<div class="galleries-view-galleries">
    <h1 class="text-center"><?=$model['accom_name']?></h1>

    <?php if(!empty($model['data'])):  ?>
    <table class="table">
        <thead>
            <tr>
                <th><?= Galleries::t('name') ?></th>
                <th><?= Galleries::t('actions') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($model['data'] as $key => $val){ ?>
            <tr>
                <td><?=$val['gallery_name']?></td>
                <td><?= !empty($val['parent_id']) ? Galleries::generateViewActions($val['gallery_id'], $val['parent_id']) : Galleries::generateViewActions($val['gallery_id']) ?></td>
            </tr>

        <?php } ?>
        </tbody>
    </table>
    <?php endif; ?>
    
       <?= Yii::$app->controller->renderPartial('_deleteModal'); ?>
</div>
<?php 
$this->registerJsFile('/js/gallery.js', ['depends' => [yii\web\JqueryAsset::className()]]);
?>