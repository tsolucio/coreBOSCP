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
?>
</div>
<script type="text/javascript">
breadCrumb.add({ icon: 'view', href: 'javascript:chive.goto(\'<?php echo $this->modelLinkName;?>/<?php echo $this->entity;?>/list/<?php echo $data->__get($this->entityidField)?>/dvcpage/<?php echo $pnum-1; ?>\')', text: <?php echo CJavaScript::encode($data->getLookupFieldValue($this->entityLookupField,$data->getAttributes())); ?>});
breadCrumb.show();
sideBar.activate(0);
</script>