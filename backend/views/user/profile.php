<?php

use yii\helpers\Html;

$this->title = sprintf('%s %s', $model->first_name, $model->last_name);
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="user-view">
	<h1>Dashboard: <?= Html::encode($this->title)?></h1>
	<div class="row">
		<div class="col-lg-2 left bg-primary">
			<p>Menu Item 1</p>
			<p>Menu Item 2</p>		
			<p>Menu Item 3</p>
		</div>

		<div class="col-lg-10 right bg-success">
			<h3> Activity Log </h3>
			<div class="row col-lg-8">
				<p>Activity 1</p>
			</div>
			<div class="row col-lg-8">
				<p>Activity 2</p>
			</div>
			<div class="row col-lg-8">
				<p>Activity 3</p>
			</div>
		</div>
	</div>
</div>