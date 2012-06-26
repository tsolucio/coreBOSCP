<div class="projectview">
<div class="view projectdetails">
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
	$relPrjdocs = $data->GetRelatedRecords('Documents');
	$relPrjticket = $data->GetRelatedRecords('HelpDesk');
	$relPrjmstone = $data->GetRelatedRecords('ProjectMilestone');
	$relPrjtask = $data->GetRelatedRecords('ProjectTask');
	$moduleNames = Yii::app()->cache->get('yiicpng.sidebar.availablemodules');
	if (is_array($moduleNames)) {
		foreach ($moduleNames as $mn) {
			if ($mn['module']=='ProjectTask') $mnpt = $mn['name'];
			if ($mn['module']=='ProjectMilestone') $mnpm = $mn['name'];
			if ($mn['module']=='HelpDesk') $mnpk = $mn['name'];
		}
	}
?>
</div>
<div class="view projectrelated">
<div class="prjtask"><?php $this->renderPartial('//project/_prjtask',array('relPrj'=>$relPrjtask,'modname'=>$mnpt));?></div>
<div class="prjmstone"><?php $this->renderPartial('//project/_prjmstone',array('relPrj'=>$relPrjmstone,'modname'=>$mnpm));?></div>
<div class="prjticket"><?php $this->renderPartial('//project/_prjticket',array('relPrj'=>$relPrjticket,'modname'=>$mnpk));?></div>
<div class="prjdocs"><?php $this->renderPartial('//project/_prjdocs',array('relPrj'=>$relPrjdocs));?></div>
</div>
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