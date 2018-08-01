<?php

use yii\helpers\Html;
$this->title = 'Upload';
?>

<div class="template-upload">
    
      <?= $this->render('_uploadForm', [
        'model' => $model,
    ]) ?>
    
</div>