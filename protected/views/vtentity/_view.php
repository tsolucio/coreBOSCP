<div class="view">
<?php
	$this->widget('zii.widgets.CDetailView', array(
		'data'=>$data,
		'attributes'=>$data->getDetailViewFieldsID($data->id),
		'cssFile'=>BASEURL.'/themes/'.Yii::app()->getTheme()->getName().'/css/list.css',
	));
	echo CHTML::hiddenField('entityidValue',$data->__get($this->entityidField), array('id'=>'entityidValue'));
	echo CHTML::hiddenField('dvcpage',$_GET[$this->modelName.'_page']-1, array('id'=>'dvcpage'));
?>
</div>
<script type="text/javascript">
breadCrumb.add({ icon: 'view', href: 'javascript:chive.goto(\'<?php echo $this->modelLinkName;?>/<?php echo $this->entity;?>/list/<?php echo $data->__get($this->entityidField)?>/dvcpage/<?php echo $_GET[$this->modelName.'_page']-1; ?>\')', text: <?php echo CJavaScript::encode($data->__get($this->entityLookupField)); ?>});
breadCrumb.show();
sideBar.activate(0);
</script>