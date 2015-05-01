<div id="languageDialog" title="<?php echo Yii::t('core', 'chooseLanguage'); ?>">
	<table style="width: 100%">
		<tr>
		<?php if (count($languages) > 0 ) {?>
			<td style="width: 50%; vertical-align: top">
				<?php $i = 0; ?>
				<?php $languageCount = count($languages); ?>
				<?php foreach($languages as $language) { ?>
					<a href="<?php echo $language['url']; ?>" class="icon" style="display: block; margin-bottom: 3px">
						<img class="icon icon16" src="<?php echo BASEURL . '/' . $language['icon']; ?>" alt="<?php echo $language['label']; ?>" />
						<span><?php echo $language['label']; ?></span>
					</a>
					<?php $i++; ?>
					<?php if($i == ceil($languageCount / 2)) { ?>
						</td>
						<td style="width: 50%; vertical-align: top">
					<?php } ?>
				<?php } ?>
			</td>
		<?php } else { ?>
			<td>
				<?php echo Yii::t('core', 'noOtherLanguagesAvailable'); ?>
			</td>
		<?php } ?>
		</tr>
	</table>
</div>

<div id="themeDialog" title="<?php echo Yii::t('core', 'chooseTheme'); ?>">
	<table style="width: 100%">
		<tr>
		<?php if (count($themes) > 0 ) {?>
			<?php $i = 0; ?>
			<?php $themeCount = count($themes); ?>
			<?php foreach($themes AS $theme) { ?>

				<td style="width: 150px; vertical-align: top">
					<a href="<?php echo $theme['url']; ?>" class="icon">
						<img class="icon icon16" src="<?php echo BASEURL . '/' . $theme['icon']; ?>" alt="<?php echo $theme['label']; ?>" />
						<span><?php echo $theme['label']; ?></span>
					</a>
				</td>

				<?php $i++; ?>
				<?php if ($i % 3 == 0 && $themeCount > $i) { ?>
					</tr><tr>
				<?php } ?>

			<?php } ?>
		<?php } else { ?>
			<td>
				<?php echo Yii::t('core', 'noOtherThemesAvailable'); ?>
			</td>
	<?php } ?>
		</tr>
	</table>
</div>

<div id="forgotPassDialog" title="<?php echo Yii::t('core','forgotPassword'); ?>">
           <?php echo CHtml::errorSummary($forgotpassword, '', ''); ?>
    <div id="MessageDIV"> <span id="Message"></span></div>
		<div id="forgotpassword-form">
                    <?php echo CHtml::beginForm();?>
                    <div class="formItems non-floated" style="text-align: left;">
                    <div class="item row1">			
                    <div class="left" style="float: none;">
			 <span class="icon">
                    <?php  echo CHtml::activeLabel($forgotpassword, 'yourEmailId', array('label'=>Yii::t('core','yourEmailId'))); ?>
                         </span>
                    </div>
                   <div class="right">
                    <?php   echo CHtml::activeTextField($forgotpassword,'username', array('class'=>'text', 'name' => 'usernamemail')); ?>
                         </div>
                            </div>
                    </div>
                    <div class="buttons">
                        <span>
			<?php //echo Html::icon('mailsend', 16, false, 'core.mail'); ?>
                        <?php echo CHtml::ajaxSubmitButton(Yii::t('core','sendPassword'),BASEURL.'/index.php/site/sendmail',array(
                                        'error'=>'js:function(){
                                            $("#MessageDIV #Message").css(\'color\', \'red\');
                                            $("#MessageDIV #Message").text("'.Yii::t('core','errSendEmail').'").show();
                                            return false;
                                        }',
                                        'beforeSend'=>'js:function(){
                                            var x= $("#usernamemail").val();
                                            var atpos=x.indexOf("@");
                                            var dotpos=x.lastIndexOf(".");
                                            if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length)
                                              {
                                              $("#MessageDIV #Message").css(\'color\', \'red\');
                                              $("#MessageDIV #Message").text("'.Yii::t('core','errInvalidEmail').'").show();
                                              return false;
                                            }
                                              
                                        }',
                                        'success'=>'js:function(data){
                                            if(data==="success")
                                            {
                                            $("#MessageDIV #Message").css(\'color\', \'green\');
                                            $("#MessageDIV #Message").text("'.Yii::t('core','EmailSent').'").show();
                                            }
                                            else if(data==="fail")
                                            {
                                            $("#MessageDIV #Message").css(\'color\', \'red\');
                                            $("#MessageDIV #Message").text("'.Yii::t('core','errSendEmail').'").show();
                                            }
                                            else if(data==="wrongmail")
                                            {
                                            $("#MessageDIV #Message").css(\'color\', \'red\');
                                            $("#MessageDIV #Message").text("'.Yii::t('core','errEmailIdNotFound').'").show();
                                            }
                                        }',
                                        'complete'=>'js:function(){
                                          
                                        }')); ?>
                        </span>
                    </div>
                
                        <?php echo CHtml::closeTag('form'); ?>
              </div> 
</div>
<!-- div id="login-image"><img src="<?php echo BASEURL; ?>/images/connect.gif"></div -->
<div id="login">
	<div id="login-logo">
		<img src="<?php echo BASEURL; ?>/images/logo-big.png" alt="<?php echo Yii::app()->name; ?>" title="<?php echo Yii::app()->name; ?>" /><br>
		<p class="login-text"><?php echo Yii::t('core', 'customerPortal'); ?></p>
	</div>

	<?php if($validBrowser) { ?>

		<?php echo CHtml::errorSummary($form, '', ''); ?>

		<div id="login-form">
			<?php echo CHtml::form(BASEURL.'/index.php/site/login','post',array('name'=>'form','id'=>'form')); ?>
			<div class="formItems non-floated" style="text-align: left;">
				
				<div class="item row1">
					<div class="left" style="float: none;">
						<span class="icon">
							<?php echo CHtml::activeLabel($form,'contactEmail',array('label'=>Yii::t('core','contactEmail'))); ?>
						</span>
					</div>
					<div class="right">
						<?php echo CHtml::activeTextField($form,'username', array('class'=>'text', 'name' => 'username', 'value'=>'')) ?>
					</div>
				</div>
				<div class="item row2">
					<div class="left">
						<span class="icon">
							<?php echo CHtml::activeLabel($form, 'password'); ?>
						</span>
					</div>
					<div class="right">
						<?php echo CHtml::activePasswordField($form, 'password', array('class' => 'text', 'value' => '', 'name' => 'password', 'autocomplete' => "off")); ?>
					</div>
				</div>
			</div>

			<div class="buttons">
				<a class="icon button primary" href="javascript:void(0);" onclick="document.form.submit()">
					<?php echo Html::icon('login', 16, false, 'core.login'); ?>
					<span><?php echo Yii::t('core', 'login'); ?></span>
				</a>
				<?php echo CHtml::submitButton(Yii::t('core', 'login'), array("style" => "width: 0px; height: 0px; opacity: 0")); ?>
			</div>

			<?php echo CHtml::closeTag('form'); ?>
		</div>
	<?php } else { ?>
		<div id="login-form">
			<?php echo Yii::t('core', 'incompatibleBrowserWarning'); ?>
			<div style="margin-top: 10px">
				<a href="http://www.firefox.com">
					<img src="<?php echo BASEURL; ?>/images/browsers/firefox.jpg" alt="Mozilla Firefox" title="Mozilla Firefox" />
				</a>
				<a href="http://www.google.com/chrome">
					<img src="<?php echo BASEURL; ?>/images/browsers/chrome.jpg" alt="Google Chrome" title="Google Chrome" />
				</a>
				<a href="http://www.opera.com">
					<img src="<?php echo BASEURL; ?>/images/browsers/opera.jpg" alt="Opera" title="Opera" />
				</a>
				<a href="http://www.microsoft.com/windows/Internet-explorer/default.aspx">
					<img src="<?php echo BASEURL; ?>/images/browsers/internetexplorer.jpg" alt="Microsoft Internet Explorer" title="Microsoft Internet Explorer" />
				</a>
			</div>
		</div>
	<?php } ?>

</div>

<script type="text/javascript">
$(function() {
	$('#LoginForm_username').focus();
});


//$('form').validate({
//rules:
//{
// usernamemail :
// {
//    required: true,
//    email: true
// }
//}
//});
 
//$('form').submit(function() {
//
//var email= $("#usernamemail").val();
//var dataString = 'usernamemail='+ email;
//
//$.ajax({
//  type: "POST",
//  url: "/cp/index.php/site/sendmail/",
//  data: dataString,
//  success: function() {
//      alert('po');
//      if ($("input:first").val() == "correct") {
//        $("#MessageDIV #Message").text("Validated...").show();
//        return true;
//      }
//     $("#MessageDIV #Message").text("Not valid Email!").show().fadeOut(10000);
//      return false;
//  }
//    });
//});
</script>