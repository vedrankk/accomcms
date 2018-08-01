<div class="navbar nav_title" style="border: 0;">
    <a href="/" class="site_title"><i class="fa fa-paw"></i> <span>Accom CMS</span></a>
</div>
<div class="clearfix"></div>

<div class="profile">
    <div class="profile_pic">
        <img src="http://placehold.it/128x128" alt="..." class="img-circle profile_img">
    </div>
    <div class="profile_info">
        <span>Welcome,</span>
        <h2><?= sprintf('%s %s', Yii::$app->user->identity->first_name, Yii::$app->user->identity->last_name) ?></h2>
    </div>
</div>