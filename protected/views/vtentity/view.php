<h1><?php
if(($cache=Yii::app()->cache)!==null) {
	if (($modname=$cache->get('yiicpng.sidebar.listmodules'))!==false) {
		$modname=$modname[$model->getModule()];
	} else {
	  $modname=Yii::t('core', $model->getModule());
	}
} else {
	$modname=Yii::t('core', $model->getModule());
}
echo $modname.' : '.$model->__get($this->entityLookupField); ?></h1>
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>$model->getDetailViewFieldsID($model->id),
	'cssFile'=>BASEURL.'/themes/'.Yii::app()->getTheme()->getName().'/css/list.css',
));
echo CHTML::hiddenField('entityidValue',$model->__get($this->entityidField), array('id'=>'entityidValue'));
echo CHTML::hiddenField('dvcpage',$_GET[$this->modelName.'_page']-1, array('id'=>'dvcpage'));
?>
<script type="text/javascript">
breadCrumb.add({ icon: 'view', href: 'javascript:chive.goto(\'<?php echo $this->modelLinkName;?>/<?php echo $this->entity;?>/view/<?php echo $model->__get($this->entityidField)?>\')', text: <?php echo CJavaScript::encode($model->__get($this->entityLookupField)); ?>});
breadCrumb.show();
sideBar.activate(0);
</script>