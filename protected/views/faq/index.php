<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'//faq/_view',
	'enableSorting'=>false,
	'template'=>"{pager}\n{summary}\n{items}\n{pager}",
	'afterAjaxUpdate'=>'chive.ajaxloaded',
	'beforeAjaxUpdate'=>'chive.ajaxloading',
	'pager'=>array(
		'pageSize'=>1,
		'maxButtonCount'=>10,
		'header'=>'',
		'cssFile'=>BASEURL.'/css/layout.css',
	),
));
?>

