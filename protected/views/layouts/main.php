<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo Yii::app()->name; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" type="text/css" href="<?php echo BASEURL; ?>/css/main.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->getBaseUrl(); ?>/css/style.css" />

<link rel="shortcut icon" href="<?php echo BASEURL; ?>/images/favicon.ico" type="image/x-icon" />

<script type="text/javascript">
// Set global javascript variables
var basePath = '<?php echo BASEURL; ?>';
var baseUrl = '<?php echo Yii::app()->urlManager->baseUrl; ?>';
var iconPath = '<?php echo ICONPATH; ?>';
var themeUrl = '<?php echo Yii::app()->theme->baseUrl; ?>';
</script>

<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerCoreScript('bbq');
Yii::app()->clientScript->registerCoreScript('yiiactiveform');
$baseScriptUrl=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets'));
Yii::app()->clientScript->registerScriptFile($baseScriptUrl.'/gridview/jquery.yiigridview.js',CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile($baseScriptUrl.'/listview/jquery.yiilistview.js',CClientScript::POS_END);

$scriptFiles = array(
	'js/jquery/jquery.ui.js',
	'js/jquery/jquery.autocomplete.js',
	'js/jquery/jquery.blockUI.js',
	'js/jquery/jquery.form.js',
	'js/jquery/jquery.jeditable.js',
	'js/jquery/jquery.layout.js',
	'js/jquery/jquery.purr.js',
	'js/jquery/jquery.selectboxes.js',
	'js/jquery/jquery.hotkey.js',
	'js/lib/json.js',
	'js/main.js',
	'js/ajaxResponse.js',
	'js/chive.js',
	'js/breadCrumb.js',
	'js/sideBar.js',
	'js/bookmark.js',
	'js/notification.js',
	'js/tipsy/jquery.tipsy.js',
   	'assets/lang_js/' . Yii::app()->getLanguage() . '.js',
	'js/advancesearch.js',
);
foreach($scriptFiles AS $file)
{
	echo '<script type="text/javascript" src="' . BASEURL . '/' . $file . '"></script>' . "\n";
}
echo '<link rel="stylesheet" href="' . BASEURL .'/js/tipsy/css/tipsy.css" type="text/css" />' . "\n";
?>

<?php Yii::app()->clientScript->registerScript('userSettings', Yii::app()->user->settings->getJsObject(), CClientScript::POS_HEAD); ?>
<script type="text/javascript">
$(document).ready(function() {
	sideBar.loadMenu();
});
</script>
</head>
<body>

<div id="loading"><?php echo Yii::t('core', 'loading'); ?>...</div>

<div class="ui-layout-north">
	<div id="header">
		<div id="headerLeft">
			<a class="icon button" href="<?php echo (substr(BASEURL,0, -9)=='index.php' ? BASEURL : BASEURL.'/index.php'); ?>">
				<img src="<?php echo BASEURL; ?>/images/logo.png" alt="<?php echo Yii::app()->name; ?>" height="22" style="position: relative; top: 6px;" />
			</a>
		</div>
		<div id="headerRight">
			<input type="text" id="globalSearch" value="<?php echo Yii::t('core', 'search'); ?>" onclick="this.value = '';" class="search" />
			<a class="icon button" href="javascript:chive.refresh();">
				<?php echo Html::icon('refresh', 16, false, 'core.refresh'); ?>
			</a>
			<a class="icon button" href="<?php echo Yii::app()->urlManager->baseUrl; ?>/site/logout">
				<?php echo Html::icon('logout', 16, false, 'core.logout'); ?>
			</a>
		</div>
	</div>
</div>
<div class="ui-layout-west">

<div id="sideBar">
<div class="sidebarHeader schemaList">
	<a class="icon" href="javascript:void(0)">
		<?php echo Html::icon('yiiCPng', 24); ?>
		<span><?php echo Yii::t('core','customerPortal'); ?></span>
	</a>
	<img class="loading" src="<?php echo BASEURL; ?>/images/loading.gif" alt="<?php echo Yii::t('core', 'loading'); ?>..." />
</div>
<div class="sidebarContent schemaList">
	<script type="text/x-vtyiicpng-template" id="menutemplate">
		<?php echo Html::ajaxLink('{linkName}', array('class' => 'icon')); ?>
		<?php echo Html::icon('{iconName}'); ?>
		<span>{menuName}</span>
		</a>
	</script>
	<div class="noEntries">
		<?php echo Yii::t('core', 'noMenuOptions'); ?>
	</div>
	<ul id="schemaList" class="list icon">
	</ul>
</div>
</div>
</div>
<div class="ui-layout-center" id="content"><?php echo $content; ?></div>

</body>
</html>