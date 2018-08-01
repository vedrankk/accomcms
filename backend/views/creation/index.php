<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->context->layout = 'create';
$this->registerCss('.help-block{color:red;}');
$this->registerJsFile('https://use.fontawesome.com/a322008483.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

?>
<div class="container" style="margin-top: 5%;">
   
    
  <div class="row">
      <?php Pjax::begin([
    // Pjax options
]);?>
      <?php $form = ActiveForm::begin(['options' => ['class' => 'col s6 offset-s3', 'data' => ['pjax' => true]]]); ?>
    <!--<form class="col s6 offset-s3">-->
      <div class="row">
       <div class="input-field col s12">
           <!--<i class="material-icons prefix">hotel</i>-->
           
           <?= $form->field($model, 'name', ['inputOptions' => ['class' => 'validate']]) ?>
          <!--<input id="name" type="text" class="validate">-->
          <!--<label for="name">Enter the name for your accomodation<span style="color: red;">*</span></label>-->
        </div>
      </div>
      <div class="row">
        <div class="input-field col s12">
            <?= $form->field($model, 'description', ['inputOptions' => ['class' => 'materialize-textarea']])->textarea() ?>
<!--          <i class="material-icons prefix">description</i>
          <textarea name="description" id="textarea1" class="materialize-textarea"></textarea>
          <label for="textarea1">Enter the description for your accomodation<span style="color: red;">*</span></label>-->
        </div>
      </div>
      <div class="row">
       <div class="input-field col s12">
           <?= $form->field($model, 'address', ['inputOptions' => ['class' => 'validate']]) ?>
<!--           <i class="material-icons prefix">home</i>
          <input id="address" type="text" class="validate">
          <label for="address">Enter the address of your accomodation<span style="color: red;">*</span></label>-->
        </div>
      </div>
        <div class="row">
       <div class="input-field col s12">
           <?= $form->field($model, 'facebook', ['inputOptions' => ['class' => 'validate']]) ?>
<!--           <i class="material-icons prefix"><i class="fa fa-facebook"></i></i>
          <input id="facebook" type="text" class="validate">
          <label for="facebook">Enter the URL of the Facebook account</label>-->
        </div>
      </div>
        <div class="row">
       <div class="input-field col s12">
           <?= $form->field($model, 'twitter', ['inputOptions' => ['class' => 'validate']]) ?>
<!--           <i class="material-icons prefix"><i class="fa fa-twitter"></i></i>
          <input id="twitter" type="text" class="validate">
          <label for="twitter">Enter the URL of the Twitter account</label>-->
        </div>
      </div>
        <div class="row">
       <div class="input-field col s12">
           <?= $form->field($model, 'youtube', ['inputOptions' => ['class' => 'validate']]) ?>
<!--           <i class="material-icons prefix"><i class="fa fa-youtube" aria-hidden="true"></i>
</i>
          <input id="youtube" type="text" class="validate">
          <label for="youtube">Enter the URL of the YouTube channel</label>-->
        </div>
      </div>
     <div class="fixed-action-btn">
        <?= Html::submitButton('<i class="large material-icons" >arrow_forward</i>', ['class' => 'btn-floating btn-large red right']) ?>
    </div>
        <!--<button class="waves-effect waves-light btn right"><i class="large material-icons" >arrow_forward</i></button>-->
    <?php ActiveForm::end(); Pjax::end(); ?>
  </div>
        
    
</div>
        



