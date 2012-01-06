<div id="yiiFormEntity" class="yiiForm">

<?php
$form=$this->beginWidget('cpngActiveForm', array(
	'id'=>'entity-form',
	//'action'=>'index.php/entity/'.$model->getModule().'/edit/id/'.$model->getAttribute($this->entityid),
	'enableAjaxValidation'=>true,
        'enableClientValidation'=>true,
        'clientOptions'=>array('validateOnSubmit'=>true,'validateOnChange'=>false),
)); ?>
	<fieldset>
	<legend><?php echo $legend; ?></legend>
	<p class="note"><?php echo Yii::t('core', 'fieldsRequired'); ?></p>
        <div id="othermsg"></div>
	<?php echo $form->errorSummary($model); ?>
        <div class="row">
		<?php
			echo CHtml::hiddenField('Entity_page',Yii::app()->getRequest()->getParam('Entity_page'));
			echo CHtml::hiddenField('currentView',$this->currentView);
		?>
		
	</div>
       <?php
       foreach($fields as $field) {
       if (!is_array($field) || empty($field['name'])) continue;  // jump values, process only fields
       if($field['name'] !='id')
       {
                $fieldname=$field['name'];
                $fieldlabel=$field['label'];
                $uitype=intval($uitypes[$fieldname]);                
                if(isset($field['type']['refersTo']) && !empty($field['type']['refersTo'])) $refersTo=$field['type']['refersTo'];
                else $refersTo='';
                echo '<div class="row">';
		echo $form->labelEx($model,$fieldname);
		echo $form->getVtigerEditField($uitype,$model,$fieldname,array('maxlength'=>100),$refersTo);
		echo $form->error($model,$fieldname);
		echo '</div>';
       }}
       ?>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('core', 'create') : Yii::t('core', 'save')); ?> 
	</div>
	</fieldset>
<?php $this->endWidget(); ?>

</div>

<script type='text/javascript'>
$('form').submit(function(event)
	{
            $('#loading').css({'background-image': 'url(images/loading4.gif)'}).fadeIn();
        });
</script>
 