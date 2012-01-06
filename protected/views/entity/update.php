<?php
if(($cache=Yii::app()->cache)!==null) {
	if (($modname=$cache->get('yiicpng.sidebar.listmodules'))!==false) {
		$modname=$modname[$model->getModule()];
	} else {
	  $modname=$model->getModule();
	}
} else {
	$modname=$model->getModule();
}
echo $this->renderPartial('_form',
	array(
	'model'=>$model,
	'fields'=>$fields,
	'uitypes'=>$uitypes,
	'legend'=>Yii::t('core', 'msgUpdate').' '.$modname,
)); ?>
<script type="text/javascript">
var table = '<?php echo $model->getModule(); ?>';
breadCrumb.add({ icon: 'edit', href: 'javascript:chive.goto(\'entity/' + table + '/edit/<?php echo $model->getAttribute($this->entityid); ?>\')', text: '<?php echo $model->getAttribute($this->lookupfield); ?>'});
sideBar.activate(0);
</script>