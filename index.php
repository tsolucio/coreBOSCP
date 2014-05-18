<?php
/*************************************************************************************************
 * coreBOSCP - web based coreBOS Customer Portal
 * Copyright 2011-2014 JPL TSolucio, S.L.   --   This file is a part of coreBOSCP.
 * Licensed under the GNU General Public License (the "License") either
 * version 3 of the License, or (at your option) any later version; you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOSCP distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://www.gnu.org/licenses/>
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

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
