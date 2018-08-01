<div id="myModal" class="modal fade">
	<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="icon-box">
					<i class="material-icons">&#xE5CD;</i>
				</div>				
				<h4 class="modal-title"><?= Yii::t('model/galleries', 'confirm') ?></h4>	
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<p><?= Yii::t('model/galleries', 'confirm_message') ?></p>
                                <div class="form-group">
                                  <label for="usr"><?= Yii::t('model/galleries', 'type_delete') ?></label>
                                  <input type="text" class="form-control" id="type-delete">
                                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
                                <button disabled type="button" id="btnYes" style="margin-bottom: 0px;" class="btn btn-danger"><?= Yii::t('app', 'Delete') ?></button>
			</div>
		</div>
	</div>
</div>   