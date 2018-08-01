<?php
use yii\helpers\Html;
use backend\models\Galleries;
use yii\widgets\ActiveForm;
//CSS dependencies 
$this->registerCssFile('/css/imageUpload/style.css');
$this->registerCssFile('//blueimp.github.io/Gallery/css/blueimp-gallery.min.css');
$this->registerCssFile('/css/imageUpload/jquery.fileupload.css');
$this->registerCssFile('/css/imageUpload/jquery.fileupload-ui.css');
//JS dependencies (Yes I know this is large)
$this->registerJsFile('js/imageUpload/jquery.ui.widget.js', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJsFile('//blueimp.github.io/JavaScript-Templates/js/tmpl.min.js', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJsFile('//blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJsFile('//blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJsFile('//blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJsFile('js/imageUpload/jquery.iframe-transport.js', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJsFile('js/imageUpload/jquery.fileupload.js', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJsFile('js/imageUpload/jquery.fileupload-process.js', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJsFile('js/imageUpload/jquery.fileupload-image.js', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJsFile('js/imageUpload/jquery.fileupload-audio.js', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJsFile('js/imageUpload/jquery.fileupload-video.js', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJsFile('js/imageUpload/jquery.fileupload-validate.js', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJsFile('js/imageUpload/jquery.fileupload-ui.js', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJsFile('js/imageUpload/main.js', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerCss('body {padding-top: 0px;}');

$this->title = Yii::t('model/galleries', 'upload_m', ['name' => $gallery['gallery_name']]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('model/galleries', 'Title'), 'url' => ['index', 'db_lang' => Galleries::getLangParam()]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('model/galleries', 'view_g'), 'url' => ['view-galleries', 'id' => $gallery['accomodation_id'], 'db_lang' => Galleries::getLangParam()]];
$this->params['breadcrumbs'][] = ['label' => $gallery['gallery_name'], 'url' => ['gallery-details', 'id' => $gallery['gallery_id'], 'db_lang' => Galleries::getLangParam()]];
$this->params['breadcrumbs'][] = ['label' => $this->title];

/*
 * For uploading images to the gallery
 */
?>

<form id="fileupload" action="upload-images" method="POST" enctype="multipart/form-data">
    
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
            <div class="col-lg-7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span><?= Yii::t('model/galleries', 'add_files') ?></span>
                    <input type="file" name="files[]" multiple>
                    <input type="hidden" name="id" value="<?=$_GET['id']?>">
                </span>
                <button type="submit" class="btn btn-primary start">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span><?= Yii::t('model/galleries', 'start_upload') ?></span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span><?= Yii::t('model/galleries', 'cancel_upload') ?></span>
                </button>
                <button type="button" class="btn btn-danger delete">
                    <i class="glyphicon glyphicon-trash"></i>
                    <span><?= Yii::t('model/galleries', 'delete') ?></span>
                </button>
                <input type="checkbox" class="toggle">
                <!-- The global file processing state -->
                <span class="fileupload-process"></span>
            </div>
            <!-- The global progress state -->
            <div class="col-lg-5 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                </div>
                <!-- The extended global progress state -->
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
    </form>

<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>
<?= Yii::$app->controller->renderPartial('_uploadTemplates'); ?>
</body>
</html>