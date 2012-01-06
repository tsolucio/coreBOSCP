<div id="dropEntityDialog" title="<?php echo Yii::t('core', 'dropEntity'); ?>" style="display: none">
<?php echo Yii::t('core', 'doYouReallyWantToDropEntity'); ?>
</div>
<?php
$this->widget('zii.widgets.CListView', array(
	'id'=>'entity-list',
	'ajaxUpdate'=>FALSE,
	'dataProvider'=>$dataProvider,     
	'itemView'=>'view',
	'enableSorting'=>false,
	'template'=>"{pager}\n{summary}\n{items}\n{pager}",
	'pager'=>array(
	    'pageSize'=>1,
	    'maxButtonCount'=>10,
	    'header'=>'',
		'cssFile'=>BASEURL.'/css/layout.css',
	),
));
?>
