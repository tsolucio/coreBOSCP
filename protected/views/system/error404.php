<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo Yii::app()->name; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<link rel="stylesheet" type="text/css" href="<?php echo BASEURL; ?>/css/main.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->getBaseUrl(); ?>/css/style.css" />

<link rel="shortcut icon" href="<?php echo BASEURL; ?>/images/favicon.ico" type="image/x-icon" />
<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerCoreScript('jquery.ui');
?>
</head>
<body>
<div id="header">
</div>
<div id="login" style="width:580px;">
<div id="login-logo">
<img src="<?php echo BASEURL; ?>/images/loginVtigerCRM.png" alt="<?php echo Yii::app()->name; ?>" title="<?php echo Yii::app()->name; ?>" /><br>
<!--<p class="login-text"><?php echo Yii::t('core', 'customerPortal'); ?></p>-->
<br/>
<img src="<?php echo BASEURL; ?>/images/error404.png" alt="<?php echo Yii::app()->name; ?>" title="<?php echo Yii::app()->name; ?>" /><br>
<br/>
<p class="login-text"><?php echo Yii::t('core', 'customerPortalPageNotFound'); ?></p>
</div>
</body>
</html>