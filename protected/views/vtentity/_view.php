<div class="view">
<?php
	$this->widget('zii.widgets.CDetailView', array(
		'data'=>$data,
		'attributes'=>$data->getDetailViewFields(),
		'cssFile'=>BASEURL.'/themes/'.Yii::app()->getTheme()->getName().'/css/list.css',
	));
	echo CHTML::hiddenField('entityidValue',$data->__get($this->entityidField), array('id'=>'entityidValue'));
	$pnum = (empty($_GET[$this->modelName.'_page']) ? 1 : $_GET[$this->modelName.'_page']);
	echo CHTML::hiddenField('dvcpage',$pnum-1, array('id'=>'dvcpage'));
	echo CHTML::hiddenField('idfieldtxt',$data->getLookupFieldValue($this->entityLookupField,$data->getAttributes()), array('id'=>'idfieldtxt'));
	echo CHTML::hiddenField('idfieldval',$data->__get($this->entityidField), array('id'=>'idfieldval'));
?>
</div>
<script type="text/javascript">
function _viewSetBreadcrumb() {
	breadCrumb.add({ icon: 'view', href: 'javascript:chive.goto(\'<?php echo $this->modelLinkName;?>/<?php echo $this->entity;?>/list/'+jQuery('#idfieldval').val()+'/dvcpage/'+jQuery('#dvcpage').val()+'\')', text: jQuery('#idfieldtxt').val()});
	breadCrumb.show();
	sideBar.activate(0);
	chive.ajaxloaded;
}
_viewSetBreadcrumb(); // executed only once on first load, then called by CListView  afterAjaxUpdate
</script>