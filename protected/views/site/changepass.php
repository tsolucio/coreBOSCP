<div id="yiiFormEntity" class="yiiForm">

<?php
$form=$this->beginWidget('vtyiicpngActiveForm', array(
	'id'=>'changepass-form',
	'enableAjaxValidation'=>false,
));
?>
	<fieldset>
	<legend><?php echo Yii::t('core', 'Change Password'); ?></legend>
	<p class="note"><?php echo Yii::t('core', 'fieldsRequired'); ?></p>

	<?php echo $form->errorSummary($model); ?>
        <p class="note"><div class="row" id="ChangePassMessage"></div>
        </p>
	<div class="row">
		<?php echo CHtml::label(Yii::t('core', 'newPassword'),'User_password',array('required'=>true)); ?>
		<?php echo $form->passwordField($model,'password',array('onfocus'=>"this.className='fieldOnFocus'",'onblur'=>"this.className=''")); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row">
		<?php echo CHtml::label(Yii::t('core', 'repeatPassword'),'User_password_repeat',array('required'=>true)); ?>
		<?php echo $form->passwordField($model,'password_repeat',array('onfocus'=>"this.className='fieldOnFocus'",'onblur'=>"this.className=''",'original-title'=>Yii::t('core', 'infoRepeatPassword'))); ?>
		<?php echo $form->error($model,'password_repeat'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::ajaxSubmitButton(Yii::t('core', 'save'),BASEURL.'/index.php/site/changepassword',array(
                'error'=>'js:function(){
                                            $("#ChangePassMessage").css(\'color\', \'red\');
                                            $("#ChangePassMessage").text("'.Yii::t('core', 'errNoUpdatePassword').'").show();
                                            return false;
                                        }',

                'success'=>'js:function(data) {
                     var atpos=data.indexOf("success")
                     if(atpos<1)
                     {
                     $("#ChangePassMessage").css(\'color\', \'green\');
  		     $("#ChangePassMessage").text("'.Yii::t('core', 'infoPasswordChange').'").show();
                     $("#User_password").val(\'\');
                     $("#User_password_repeat").val(\'\');
                     }
                     else
                     {
                     $("#ChangePassMessage").css(\'color\', \'red\');
  		     $("#ChangePassMessage").text("'.Yii::t('core', 'errNoUpdatePassword').'").show();
                     $("#User_password").val(\'\');
                     $("#User_password_repeat").val(\'\');
                     }
  	        }',
               'beforeSend'=>'js:function(){ 
                                            var password= $("#User_password").val();
                                            var password_repeat= $("#User_password_repeat").val();
                                            if(password==\'\')
                                            {
                                               $("#ChangePassMessage").css(\'color\', \'red\');
                                               $("#ChangePassMessage").text("'.Yii::t('core', 'errPasswordFields').'").show();
                                               return false;
                                            }
                                            if(password_repeat==\'\')
                                            {
                                               $("#ChangePassMessage").css(\'color\', \'red\');
                                               $("#ChangePassMessage").text("'.Yii::t('core', 'errPasswordFields').'").show();
                                               return false;
                                            }
                                            if(password!=password_repeat)
                                            {  $("#ChangePassMessage").css(\'color\', \'red\');
                                               $("#ChangePassMessage").text("'.Yii::t('core', 'errPasswordNoMatch').'").show();
                                               return false;
                                            }

                                        }'
                )); ?>
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
  	},
        beforeSend: function(){
                                            var password= $("#password").val();
                                            var password_repeat= $("#password_repeat").val();
                                            if(password!=password_repeat)
                                            {  $("#ChangePassMessage").css('color', 'red');
                                              $("#ChangePassMessage").text("<?php echo Yii::t('core', 'errInvalidEmail'); ?>").show();
                                              return false;
                                            }

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
