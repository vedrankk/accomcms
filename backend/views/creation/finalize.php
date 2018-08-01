<?php
$this->context->layout = 'create';

$this->registerJsFile('https://use.fontawesome.com/a322008483.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

?>

<div class="container">
    <h1>Overview</h1>
    <p class="deep-purple-text text-darken-1">This is all the data that you inputed. If you wish to change some of it, you will be able to do from the options menu.</p>
    <div id="accom-data" class="row">
        <h3><?= $accomodation['name'] ?></h3>
        <p><?= $accomodation['description'] ?></p>
        <p><?= $accomodation['address'] ?></p>
        <p><?= !empty($accomodation['facebook']) ? 'Facebook: ' .$accomodation['facebook'] : '' ?></p>
        <p><?= !empty($accomodation['twitter']) ? 'Twitter: ' .$accomodation['twitter'] : '' ?></p>
        <p><?= !empty($accomodation['youtube']) ? 'YouTube: ' .$accomodation['youtube'] : '' ?></p>
    </div>
    <div id="domain" class="row">
        <p class="blue-text text-darken-2">Chosen Domain</p>
        <p><?= $domain['domain_url'] ?> </p>
    </div>
    <div id="languages" class="row">
        <h2>Chosen Languages for <?= $accomodation['name'] ?> </h2>
        <?php
        foreach($languages as $key => $val){ ?>
            <p><?= $val['name'] ?></p>
        <?php } ?>
    </div>
    <div id="services" class="row">
        <h2>Chosen services for <?= $accomodation['name'] ?> </h2>
         <?php
        foreach($services as $key => $val){ ?>
            <p><?= $val['name'] ?></p>
        <?php } ?>
    </div>
    <div id="emails" class="row">
        <h2>Emails</h2>
         <?php
        foreach($emails as $key => $val){ ?>
            <p><?= $val['title'] .': ' . $val['email'] ?></p>
        <?php } ?>
    </div>
    <a href="save" class="btn right">Finish</a>
</div>