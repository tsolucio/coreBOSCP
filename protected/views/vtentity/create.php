<?php
if(($cache=Yii::app()->cache)!==null) {
	if (($modname=$cache->get('yiicpng.sidebar.listmodules'))!==false) {
		$modname=$modname[$model->getModule()];
	} else {
	  $modname=Yii::t('core', $model->getModule());
	}
} else {
	$modname=Yii::t('core', $model->getModule());
}
?>
<?php echo $this->renderPartial('_form', 
        array('model'=>$model,
            'fields'=>$fields,
            'uitypes'=>$uitypes,
            'legend'=>Yii::t('core','create')." $modname"
            )); ?>

<script type="text/javascript">
breadCrumb.add({ icon: 'add', href: 'javascript:chive.goto(\'<?php echo $this->modelLinkName;?>/<?php echo $this->entity;?>/create\')', text: '<?php echo $this->entity; ?>'});
breadCrumb.show();
sideBar.activate(0);
</script>