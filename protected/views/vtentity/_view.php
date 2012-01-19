<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('projectname')); ?>:</b>
	<?php echo CHtml::encode($data->projectname); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('startdate')); ?>:</b>
	<?php echo CHtml::encode($data->startdate); ?>
	<br />
	<?php 
	  echo CHTML::hiddenField('entityidValue',$data->__get($this->entityidField), array('id'=>'entityidValue'));
	  echo CHTML::hiddenField('dvcpage',$_GET[$this->modelName.'_page']-1, array('id'=>'dvcpage'));
	?>
</div>
<script type="text/javascript">
breadCrumb.add({ icon: 'view', href: 'javascript:chive.goto(\'<?php echo $this->modelLinkName;?>/<?php echo $this->entity;?>/list/<?php echo $data->__get($this->entityidField)?>/dvcpage/<?php echo $_GET[$this->modelName.'_page']-1; ?>\')', text: <?php echo CJavaScript::encode($data->__get($this->entityLookupField)); ?>});
breadCrumb.show();
sideBar.activate(0);
</script>