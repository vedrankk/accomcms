<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->context->layout = 'create';
$this->registerCss('');
$this->registerJsFile('https://use.fontawesome.com/a322008483.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('js/creation/domainSuggestion.js', ['depends' => [backend\components\MaterializeAsset::className()]]);
$this->registerCssFile('/css/creation/domainSuggestion.css');
?>
 <nav class="light-blue lighten-1" role="navigation">
    <div class="nav-wrapper container"><a id="logo-container" href="#" class="brand-logo">Logo</a>
    </div>
  </nav>

<div class="container" style="margin-top: 5%;">
     <div class="row">
       <div class="input-field col s12">
           <i class="material-icons prefix"><i class="fa fa-at"></i></i>
          <input id="domain" type="text" class="validate domain">
          <label for="domain">Write your full domain or some keywords and we will give you suggestions!</label>
        </div>
         <span id="domain-valid" style="display: none"></span>
      </div>
</div>