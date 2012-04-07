 <?php echo $this->renderPartial('_form', array(
    'model'=>$model,
    'fields'=>$fields,
    'uitypes'=>$uitypes,
    'legend'=>Yii::t('core','edit')." ".$model->__get($this->entityLookupField),
    )); ?>

<script type="text/javascript">
breadCrumb.add({ icon: 'edit', href: 'javascript:chive.goto(\'<?php echo $this->modelLinkName;?>/<?php echo $this->entity;?>/update/<?php echo $model->__get($this->entityidField);?>\')', text: <?php echo CJavaScript::encode($model->getLookupFieldValue($this->entityLookupField,$model->getAttributes())); ?>});
breadCrumb.show();
sideBar.activate(0);
</script>