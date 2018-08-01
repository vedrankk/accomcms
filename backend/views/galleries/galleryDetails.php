<?php
use yii\helpers\Html;
use backend\models\Galleries;
$this->registerCss('.img-wrap {display:inline-block;position:relative;} .delete-button{position:absolute;top:0;left:0;font-size: 2em;}');
$this->registerCssFile('https://fonts.googleapis.com/css?family=Roboto|Varela+Round');
$this->registerCssFile('https://fonts.googleapis.com/icon?family=Material+Icons');
$this->registerCssFile('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
$this->registerCssFile('/css/galleryView.css');
$this->registerCssFile('/css/galleryImageView.css');

//If the value is a translation the ID for the adding images to the gallery is set to PARENT
//And the delete url is set so it calls the delete-translation
if(!empty($model['parent_id']))
{
    $newId = $model['parent_id'];
    $a = Html::a(Yii::t('model/galleries', 'delete_g'), ['delete-translation', 'id' => $model['gallery_id'], 'db_lang' => Galleries::getLangParam()], ['data-method' => 'post']);
}
//If the value is original, the delete url is set so it calls the delete modal
else{
    $newId = $model['gallery_id'];
    $a = Html::a(Yii::t('model/galleries', 'delete_g'), '#myModal', ['class' => 'confirm-delete trigger-btn', 'data-url' => 'delete-gallery', 'data-id' => $model['gallery_id'], 'data-toggle' => 'modal']);
}

$this->title = $model['gallery_name'];
$this->params['breadcrumbs'][] = ['label' => Yii::t('model/galleries', 'Title'), 'url' => ['index', 'db_lang' => Galleries::getLangParam()]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('model/galleries', 'view_g'), 'url' => ['view-galleries', 'id' => Galleries::find()->where(['gallery_id' => Yii::$app->request->get('id')])->one()->accomodation_id, 'db_lang' => Galleries::getLangParam()]];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="galleries-view">
    <h1 class="text-center"><?= $model['gallery_name']?></h1>
    <p class="text-center"><?= $model['gallery_description'] ?></p>
    
    <?= $a ?>
    
    <h3 class="text-left"><?= Yii::t('app', 'images') ?> <?= Html::a('<i class="glyphicon glyphicon-plus-sign"></i>', ['gallery-upload', 'id' => $newId]) ?> </h3>
    <div class="col-lg-12">
       <div class="gal">
           <!--Displays all images in this gallery-->
           <!--The $images array is created from the GalleryImages table-->
        <?php foreach($images as $key => $val){ ?>
        <div class="img-wrap" id="img<?= $val['image_id'] ?>">
            <img src="<?=$url.$val['image_name']?>">
            <a href="#!" onclick="deleteImage(<?= $val['image_id']?>, '<?= $model['gallery_name']?>', '<?= $accomodation_name ?>')"  class="delete-button text-danger"><i class="glyphicon glyphicon-remove"></i></a>
        </div>
        <?php } ?>
        </div>
    </div>
    
    
 

<?= Yii::$app->controller->renderPartial('_deleteModal'); ?>
    
</div>
<div id="snackbar"></div>
<?php
$this->registerCssFile('/css/snackbar.css'); $this->registerJsFile('js/gallery.js', ['depends' => [yii\web\JqueryAsset::className()]]);
?>