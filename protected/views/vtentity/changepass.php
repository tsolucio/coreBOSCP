<div id="yiiFormEntity" class="yiiForm">

<?php
$form=$this->beginWidget('cpngActiveForm', array(
	'id'=>'changepass-form',
	'enableAjaxValidation'=>false,
));
$model=User::model();
?>
	<fieldset>
	<legend><?php echo 'Change Password'; ?></legend>
	<p class="note"><?php echo Yii::t('core', 'fieldsRequired'); ?></p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'new password'); ?>
		<?php echo $form->passwordField($model,'password',array('onfocus'=>"this.className='fieldOnFocus'",'onblur'=>"this.className=''",'original-title'=>'Widget class name must only contain word characters.')); ?>
		<?php echo $form->error($model,'newpassword'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'repeat password'); ?>
		<?php echo $form->passwordField($model,'repeatpassword',array('original-title'=>'Widget class name must only contain word characters.')); ?>
		<?php echo $form->error($model,'repeatpassword'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Save'); ?>
                <?php echo CHtml::Button('Cancel'); ?>
	</div>
	</fieldset>
<?php $this->endWidget(); ?>

</div><!-- form -->
<script type='text/javascript'>
  $(function() {
    $('#changepass-form [original-title]').tipsy({trigger: 'focus', gravity: 'w'});
  });

  $('#changepass-form').ajaxForm({
  	success: function(responseText, statusText) {
  		AjaxResponse.handle(responseText);
  		$('div.ui-layout-center').html(responseText);
  		init();
  	}
  });

  $('#changepass-form').keydown(function(e) {
  	if(e.which == 13)
  	{
  		$(this).submit();
  	}
  });

  $('table.list input:first').focus();

</script>
