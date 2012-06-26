<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'//project/_view',
	'enableSorting'=>false,
	'template'=>"{pager}\n{summary}\n{items}\n{pager}",
	'afterAjaxUpdate'=>'_viewSetBreadcrumb',
	'beforeAjaxUpdate'=>'chive.ajaxloading',
	'pager'=>array(
		'pageSize'=>1,
		'maxButtonCount'=>10,
		'header'=>'',
		'cssFile'=>BASEURL.'/css/layout.css',
	),
));
?>

