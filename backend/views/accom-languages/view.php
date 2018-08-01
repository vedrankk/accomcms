<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\AccomLanguages */

$this->title = $completeModel[0]['name'];
$this->params['breadcrumbs'][] = ['label' => Yii::t('model/accomlanguages','Title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="accom-languages-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <a class="btn btn-success" href="create?id=<?=$completeModel[0]['accomodation_id']?>"><?= Yii::t('model/accomlanguages', 'add_new') ?></a>
    
    <table class="table">
        <thead>
            <tr>
               <th><?= Yii::t('model/accomlanguages', 'lang_id') ?></th>
               <th><?= Yii::t('model/accomlanguages', 'default_lang_id') ?></th>
               <th><?= Yii::t('model/accomlanguages', 'actions') ?></th>
            </tr>
        </thead>
        <tbody>
    <?php
        foreach($completeModel as $key => $val)
        {
            $def = $val['default_lang_id'] == 1 ? Yii::t('model/accomlanguages', 'default_lang_id') : '/';
            ?>
            <tr>
                <td><?=$val['lang_name']?></td>
                <td><?=$def?></td>
                <td>
                   <a href="delete?id=<?=$val['accom_languages_id']?>" title="<?= Yii::t('model/accomlanguages', 'Delete') ?>" aria-label="Delete" data-pjax="0" data-confirm="<?= Yii::t('app', 'confirm_delete')?>" data-method="post">
                       <span class="glyphicon glyphicon-trash"></span>
                   </a> 
                    <a href="make-default?id=<?=$val['accom_languages_id']?>" title="<?= Yii::t('model/accomlanguages', 'make_def') ?>" aria-label="make_default" data-pjax="0"  data-method="post">
                       <span class="glyphicon glyphicon-refresh"></span>
                   </a>
                </td>
            </tr>
            <?php
        }
    ?>
        </tbody>
    </table>

</div>
