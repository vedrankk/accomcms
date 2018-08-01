<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\Accomodation;
use kartik\select2\Select2;

$accomodationData = [ArrayHelper::map(Accomodation::find()->andWhere('parent_id IS NULL')->asArray()->all(), 'accomodation_id', 'name')][0];
$langData = [ArrayHelper::map(\backend\models\LanguagesDb::find()->andWhere('parent_id IS NULL')->asArray()->all(), 'lg_id', 'name')][0];
$this->registerJsFile('http://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJsFile('http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.8/summernote.js', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJsFile('js/summernote.js', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerCssFile('http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.8/summernote.css', ['depends' => [\yii\bootstrap\BootstrapAsset::className()]]);
$this->registerCssFile('/css/snackbar.css');
/* @var $this yii\web\View */
/* @var $model backend\models\AccomodationNews */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="accomodation-news-form">

    <?php $form = ActiveForm::begin(); ?>

    <?=   
        $form->field($model, 'accomodation_id')->widget(Select2::classname(), [
          'data' => $accomodationData,
          'options' => ['placeholder' => Yii::t('model/accomnews',  'accomodation_id')],
          'pluginOptions' => [
          ],
        ]);

    ?>

    <?=   
        $form->field($model, 'lang_id')->widget(Select2::classname(), [
          'data' => $langData,
          'options' => ['placeholder' => Yii::t('model/accomnews',  'lang_id')],
          'pluginOptions' => [
          ],
        ]);

    ?>

    <?= $form->field($model, 'news_headline')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'news_text')->hiddenInput() ?>
    <textarea id="summernote"><p><?= $model->news_text ?></p></textarea>
    
    <div id="test" style="display: none"></div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <div id="snackbar"></div>
    <script>
    
</script>
   
</div>
