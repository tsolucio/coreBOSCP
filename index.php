<?php
/*
 * vtigerCRM vtyiiCPng - web based vtiger CRM Customer Portal
* Copyright (C) 2011 Opencubed shpk: JPL TSolucio, S.L./StudioSynthesis, S.R.L.
*
* This file is part of vtyiiCPng.
*
* The contents of this file are subject to the vtiger CRM Public License Version 1.0 ("License")
* You may not use this file except in compliance with the License
* The Original Code is:  Opencubed Open Source
* The Initial Developer of the Original Code is Opencubed.
* Portions created by Opencubed are Copyright (C) Opencubed.
* All Rights Reserved.
*
* vtyiiCPng is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*
*/

// Load Yii
require('protected/vtyiicpng.php');

if(!date_default_timezone_get())
{
	// Set a fallback timezone if the current php.ini does not contain a default timezone setting.
	// If the environment is setup correctly, we won't override the timezone.
	date_default_timezone_set("UTC");
}

// Create web application
$app = Yii::createWebApplication('protected/config/main.php');

// Yii debug mode
defined('YII_DEBUG') or define('YII_DEBUG', Yii::app()->debug);
$usg=$app->user;
// Define constants
define('BASEURL', Yii::app()->baseUrl);
define('ICONPATH', BASEURL . '/images/icons/' . Yii::app()->params->iconPack);

$validPaths = array(
	'site',
	'index.php',
);
$rt=Yii::app()->urlManager->parseUrl($app->request);
$isg=$app->user->isGuest;
if($app->user->isGuest and !preg_match('/^(' . implode('|', $validPaths) . ')/i', Yii::app()->urlManager->parseUrl($app->request)))
{
	if($app->request->isAjaxRequest)
	{
		$response = new AjaxResponse();
		$response->redirectUrl = Yii::app()->createUrl('site/login');
		$response->send();
	}
	else
	{
		$app->request->redirect(Yii::app()->createUrl('site/login'));
	}
}

// Language
if($app->session->itemAt('language'))
{
	$app->setLanguage($app->session->itemAt('language'));
}
elseif($app->request->getPreferredLanguage() && is_dir('protected/messages/' . $app->request->getPreferredLanguage()))
{
	$app->setLanguage($app->request->getPreferredLanguage());
}
else
{
	$app->setLanguage('en_us');
}

// Theme
$theme = $app->session->itemAt('theme') ? $app->session->itemAt('theme') : 'standard';
$app->setTheme($theme);

// Unset jQuery in Ajax requests
if($app->request->isAjaxRequest)
{
	$app->clientScript->scriptMap['jquery.js'] = false;
	$app->clientScript->scriptMap['jquery.min.js'] = false;
}

// Publish messages for javascript usage
Yii::app()->getComponent('messages')->publishJavaScriptMessages();

// Run application
$app->run();
