<h1><?php echo Yii::t('core', 'msgUpdate').' '.$model->__get($this->entityLookupField); ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,'fields'=>$fields,'uitypes'=>$uitypes)); ?>

<script type="text/javascript">
breadCrumb.add({ icon: 'edit', href: 'javascript:chive.goto(\'<?php echo $this->modelLinkName;?>/<?php echo $this->entity;?>/update/<?php echo $model->__get($this->entityidField);?>\')', text: <?php echo CJavaScript::encode($model->__get($this->entityLookupField)); ?>});
breadCrumb.show();
sideBar.activate(0);
</script>