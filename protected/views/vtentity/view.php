<h1><?php echo $this->entity.' : '.$model->__get($this->entityLookupField); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>Vtentity::model($this->entity)->getDetailViewFieldsID($data->id),
));
?>