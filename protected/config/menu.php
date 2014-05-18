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