<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'viewData' => array('fields'=>$fields, 'uitypes'=>$uitypes),
	'itemView'=>'//helpdesk/_view',
	'enableSorting'=>false,
	'template'=>"{pager}\n{summary}\n{items}\n{pager}",
	'afterAjaxUpdate'=>'_viewExecuteJS',
	'beforeAjaxUpdate'=>'chive.ajaxloading',
	'pager'=>array(
		'pageSize'=>1,
		'maxButtonCount'=>10,
		'header'=>'',
		'cssFile'=>BASEURL.'/css/layout.css',
	),
));
?>

