<?php

?>

<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">

<div class="menu_section">
    <h3><?= Yii::t('app', 'General') ?></h3>
    <?=
    \yiister\gentelella\widgets\Menu::widget(
        [
            "items" =>  backend\components\MenuItems::getItems()
        ]
    )
    ?>
</div>

</div>