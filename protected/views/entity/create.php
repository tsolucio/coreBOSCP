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
   'legend'=>Yii::t('core','create')." $modname"));
?>
<script type="text/javascript">
var schema = '<?php echo $this->schema; ?>';
var table = '<?php echo $this->table; ?>';
breadCrumb.add({ icon: 'add', href: 'javascript:chive.goto(\'entity/' + table + '/create\')', text: table});
sideBar.activate(0);
</script>