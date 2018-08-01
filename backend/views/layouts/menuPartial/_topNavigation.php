<?php 
use common\widgets\LanguagePicker;
?>
<div class="top_nav">

<div class="nav_menu">
    <nav class="" role="navigation">
        <div class="nav toggle">
            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
        </div>

        <ul class="nav navbar-nav navbar-right">
            <li class="">
                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <img src="http://placehold.it/128x128" alt=""><?= sprintf('%s %s', Yii::$app->user->identity->first_name, Yii::$app->user->identity->last_name) ?>
                    <span class=" fa fa-angle-down"></span>
                </a>
                <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <li><a href="javascript:;">  Profile</a>
                    </li>
                    <li>
                        <a href="javascript:;">
                            <span class="badge bg-red pull-right">50%</span>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;">Help</a>
                    </li>
                    <li><a href="/en/site/logout" data-method="post">Logout</a>
                    </li>
                </ul>
            </li>
            <li class="">
               <?= backend\widgets\BackendLanguagePicker::widget() ?>
            </li>
        </ul>
    </nav>
</div>

</div>