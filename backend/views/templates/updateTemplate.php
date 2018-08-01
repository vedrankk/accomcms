<?php

use yii\helpers\Html;
$this->title = 'Upload';
?>

<div class="template-upload">
    
      <?= $this->render('_updateForm', [
        'model' => $model,
    ]) ?>
    
</div>