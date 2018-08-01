<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\Accomodation;
use kartik\select2\Select2;

$accomodationData = [ArrayHelper::map(Accomodation::find()->andWhere('parent_id IS NULL')->asArray()->all(), 'accomodation_id', 'name')][0];
$langData = [ArrayHelper::map(\backend\models\LanguagesDb::find()->andWhere('parent_id IS NULL')->asArray()->all(), 'lg_id', 'name')][0];
/* @var $this yii\web\View */
/* @var $model backend\models\AccomLanguages */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="accom-languages-form">

    <?php $form = ActiveForm::begin(); ?>

    <?=   
        $form->field($model, 'accomodation_id')->widget(Select2::classname(), [
          'data' => $accomodationData,
          'options' => ['placeholder' => Yii::t('model/accomlanguages',  'Accomodation'), 'onchange' => 'filterLangs(this.value)'],
          'pluginOptions' => [
          ],
        ]);

    ?>

    <?=   
        $form->field($model, 'lang_id', ['enableClientValidation' => false])->widget(Select2::classname(), [
          'data' => $langData,
          'options' => ['placeholder' => Yii::t('model/accomlanguages',  'Languages'), 'onchange' => 'addLang(this.value, this.options[this.selectedIndex].innerHTML, this.selectedIndex)'],
          'pluginOptions' => [
          ],
        ]);

    ?>

    
    <div id="selectedLangs">
        
    </div>
    <button onclick="save()" type="button" class="btn btn-success"><?=Yii::t('model/accomlanguages', 'Save') ?></button>


    <?php ActiveForm::end(); ?>

</div>
<?php $this->registerJsFile('js/accom_languages.js', ['depends' => [yii\web\JqueryAsset::className()]]);?>
