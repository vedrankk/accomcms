<?php
$this->context->layout = 'create';

$this->registerJsFile('https://use.fontawesome.com/a322008483.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('js/creation/templates.js', ['depends' => [backend\components\MaterializeAsset::className()]]);
//$this->registerJs("$('.modal').modal({dismissible: true, opacity: .5, inDuration: 300, outDuration: 200, startingTop: '4%', endingTop: '10%'});");
?>

<div class="container">
    <?php
$i = 0;
foreach($templates as $key => $val)
{
    if($i == 0){echo '<div class="row">';}
    ?>
        <div class="col s4 m4">
          <div class="card">
            <div class="card-image">
              <img src="<?= $val['image'] ?>">
              <span class="card-title"><?= $val['name'] ?></span>
            </div>
            <div class="card-content">
              <p><?= substr($val['description'], 0, 164) .'...<br><a href="#!" onclick="showMore('.$val['template_id'].')" class="modal-trigger" style="color:#1E90FF;">Read more</a>'?></p>
            </div>
            <div class="card-action">
                <a href="<?= sprintf('save-template?id=%s', $val['template_id']) ?>" class="btn">Select this template</a>
            </div>
          </div>
        </div>


    <?php
    if($i == 2){echo '</div>';}
    $i++;
    if($i == 2){$i = 0;}
}

?>

<div id="modal1" class="modal">
  <div class="modal-content">
      <h4 id="template-name"></h4>
      <h6 id="live-preview"></h6>
    <div class="col s6 left">
        <div class="single-image" style="display: none">
            <img class="responsive-img" src="" id="template-preview">
        </div>
         <div class="slider">
            <ul class="slides">
              
            </ul>
        </div>
    </div>
      <p id="template-description"></p>
  </div>
</div>
    
    
</div>
