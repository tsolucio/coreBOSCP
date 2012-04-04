<h1><?php echo $model->getModuleName().' : '.$model->getLookupFieldValue($this->entityLookupField,$model->getAttributes()); ?></h1>
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>$model->getDetailViewFieldsID($model->id),
	'cssFile'=>BASEURL.'/themes/'.Yii::app()->getTheme()->getName().'/css/list.css',
));
echo CHTML::hiddenField('entityidValue',$model->__get($this->entityidField), array('id'=>'entityidValue'));
echo CHTML::hiddenField('dvcpage',$_GET[$this->modelName.'_page']-1, array('id'=>'dvcpage'));
?>
<script type="text/javascript">
breadCrumb.add({ icon: 'view', href: 'javascript:chive.goto(\'<?php echo $this->modelLinkName;?>/<?php echo $this->entity;?>/view/<?php echo $model->__get($this->entityidField)?>\')', text: <?php echo CJavaScript::encode($model->getLookupFieldValue($this->entityLookupField,$model->getAttributes())); ?>});
breadCrumb.show();
sideBar.activate(0);
</script>