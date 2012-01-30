<div class="yiiForm">

<?php $form=$this->beginWidget('vtyiicpngActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?><fieldset>
    <legend><?php echo Yii::t('core','search') ?></legend>
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
			echo $form->getVtigerEditField($uitype,$model,$fieldname,array('maxlength'=>100),$refersTo,'search');
			echo '</div>';
		}
	}
	?>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>
</fieldset>
<?php $this->endWidget(); ?>

</div><!-- search-form -->