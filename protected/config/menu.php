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

/**
This array defines the sidebar menu of the application.
Each entry in this array will be, at least, one entry in the menu.
Each entry can be one of three things:

1.- an array which will contain
  'name' => The menu text to show the user
  'link' => the ajax link to use to launch the action
  'icon' => the name of the icon to use for the entry

2.- a module name, if the module is available to the user it will be shown at that position

3.- the string '#vtigermodules#', which will be converted to all the modules available that haven't been already shown

For example, the next array would show the "About us" link first, followed by HelpDesk, then Invoices, all the other
available modules, and finally a few more menu options

$evocpMenu = array(
	array('name'=>Yii::t('core', 'about'),'link'=>'information/about','icon'=>'info'),
	'HelpDesk',
	'Invoice',
	'#vtigermodules#',
	array('name'=>Yii::t('core', 'Change Password'),'link'=>'site/changepassword','icon'=>'privileges'),
	array('name'=>Yii::t('core', 'logout'),'link'=>'site/logout','icon'=>'logout'),
);

The exact order given will be respected.

**/
if (Yii::app()->vtyiicpngScope=='CPortal') {
	$evocpMenu = Yii::app()->notSupportedModules['CPortal'];
	$evocpMenu[] = array('name'=>Yii::t('core', 'Change Password'),'link'=>'site/changepassword','icon'=>'privileges');
	$evocpMenu[] = array('name'=>Yii::t('core', 'logout'),'link'=>'site/logout','icon'=>'logout');
} else {
$evocpMenu = array(
	'#vtigermodules#',
	array('name'=>Yii::t('core', 'Change Password'),'link'=>'site/changepassword','icon'=>'privileges'),
	array('name'=>Yii::t('core', 'about'),'link'=>'information/about','icon'=>'info'),
	array('name'=>Yii::t('core', 'logout'),'link'=>'site/logout','icon'=>'logout'),
);
}