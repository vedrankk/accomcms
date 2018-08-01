<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\widgets\AccomServiceWidget;
use kartik\select2\Select2;
use backend\models\Accomodation;
use yii\helpers\ArrayHelper;

$accomodationData = [ArrayHelper::map(Accomodation::find()->andWhere('parent_id IS NULL')->asArray()->all(), 'accomodation_id', 'name')][0];

$this->title = 'Overview';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <div class="row">
    <?= 
        '<label class="control-label">Accomodations</label>';
        echo Select2::widget([
            'name' => 'accomodations',
            'data' => $accomodationData,
            'options' => [
                'placeholder' => Yii::t('model/overview', 'select_accom'),
                'onchange' => 'getData(this.value)',
            ],
        ]); 
    ?>
    </div>
    
    <div class="row col-lg-12" id="accomData" style="display: none;">
        <div class="row" >
            <h1 id="accomName" class="text-center text-success"></h1>
            <p class="text-center" style="display: none" id="editName">
                <input id="nameVal" type="text" placeholder="Edit Name...">
                <a href="#!" id="" onclick="cancelEdit('Name')"><i class="glyphicon glyphicon-remove-sign"></i></a>
            </p>
        </div>
        <div class="row">
            <p id="accomDescription" class="text-center"></p>
            <p class="text-center" style="display: none" id="editDescription">
                <textarea id="descriptionVal"></textarea>
                <a href="#!" onclick="cancelEdit('Description')"><i class="glyphicon glyphicon-remove-sign"></i></a>
            </p>
        </div>
        <div class="row">
        <h3><?= Yii::t('model/overview', 'basic_info') ?></h3>
            <p>
                <?= Yii::t('model/overview', 'address') ?> : <span id="accomAddress"></span>
                <p id="editAddress" style="display: none">
                    <input id="addressVal" type="text" placeholder="Edit Address...">
                    <a href="#!" id="" onclick="cancelEdit('Address')"><i class="glyphicon glyphicon-remove-sign"></i></a>
                </p>
            </p>
            <p>Facebook:  <span id="accomFacebook"></span><div style="display: none" id="editFacebook"><input id="facebookVal" type="text" placeholder="<?= Yii::t('model/overview', 'Edit', ['social' => 'Facebook']) ?>"> <a href="#!" onclick="cancelEdit('Facebook')"><i class="glyphicon glyphicon-remove-sign"></i></a></div></p>
            <p>YouTube:  <span id="accomYoutube"></span><div style="display: none" id="editYoutube"><input id="youtubeVal" type="text" placeholder="<?= Yii::t('model/overview', 'Edit', ['social' => 'YouTube']) ?>"> <a href="#!" onclick="cancelEdit('Youtube')"><i class="glyphicon glyphicon-remove-sign"></i></a></div></p>
            <p>Twitter:  <span id="accomTwitter"></span><div style="display: none" id="editTwitter"><input id="twitterVal" type="text" placeholder="<?= Yii::t('model/overview', 'Edit', ['social' => 'Twitter']) ?>"> <a href="#!" onclick="cancelEdit('Twitter')"><i class="glyphicon glyphicon-remove-sign"></i></a></div></p>
        </div>
        <button type="button" class="btn btn-success pull-right" id="saveData"><?= Yii::t('app', 'Save') ?></button>
        <div class="clearfix"></div>
        <hr>
    </div>
    
    <div class="row col-lg-12" style="display: none;" id="emails">
        <h3><?= Yii::t('model/overview', 'emails') ?> <a href="#!" id="showMailForm"><i class="glyphicon glyphicon-plus-sign"></i></a> </h3>
        <div  id="accomEmails"></div>
        <div id="newEmail" style="display: none">
            <h4><?= Yii::t('model/overview', 'new_mail') ?> <a href="#!" onclick="cancelNewEmail()"><i class="glyphicon glyphicon-remove-sign"></i></a></h4>
            <input type="text" placeholder="<?= Yii::t('model/overview', 'email_pl') ?> " id="emailVal">
            <input type="text" placeholder="<?= Yii::t('model/overview', 'title_pl') ?>" id="emailTitle">
            <button id="addMail" class="btn btn-success"><?= Yii::t('model/overview', 'new_mail') ?></button>
        </div>
        <hr>
    </div>
    
    <div class="row col-lg-12" id="accomLangs" style="display: none">
        <h3><?= Yii::t('model/overview', 'langs') ?></h3>
        <div id="accomLangsList"></div>
        <hr>
    </div>
    
    <div class="row col-lg-12" id="accomServices" style="display: none">
        <h3><?= Yii::t('model/overview', 'services') ?></h3>
        <div id="accomServicesList"></div>
        <hr>
    </div>
</div>
<div id="snackbar"></div>
<?php $this->registerCssFile('/css/snackbar.css'); $this->registerJsFile('js/overview.js', ['depends' => [yii\web\JqueryAsset::className()]]);?>
