<div class="yiiForm">

<?php $form=$this->beginWidget('vtyiicpngActiveForm', array(
	'id'=>$this->modelName.'-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>true,
)); ?>
<fieldset>
	<p class="note"><?php echo Yii::t('core', 'fieldsRequired'); ?></p>
    <div id="othermsg"></div>

	<?php
	echo $form->errorSummary($model);
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
		}
	}
	?>

	<div class="row">
		<!-- administrative fields -->
		<?php echo CHtml::hiddenField('dvcpage',$_REQUEST['dvcpage']); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('core', 'create') : Yii::t('core', 'save')); ?>
	</div>
</fieldset>
<?php $this->endWidget(); ?>
<script type="text/javascript">
	$('form').ajaxForm({
		success: function(response) {
			if (response.data != undefined && response.data != '') 
				$('#content').html(response.data);
			AjaxResponse.handle(response);
		}
	});
</script>

</div><!-- form -->