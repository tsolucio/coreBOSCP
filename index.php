<?php
/**************************************************************************************************
 * Evolutivo vtyiiCPng - web based vtiger CRM Customer Portal
 * Copyright 2012 JPL TSolucio, S.L.  --  This file is a part of vtyiiCPNG.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
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

// Define constants
define('BASEURL', Yii::app()->baseUrl);
define('ICONPATH', BASEURL . '/images/icons/' . Yii::app()->params->iconPack);

$validPaths = array(
	'site',
	'index.php',
);

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
