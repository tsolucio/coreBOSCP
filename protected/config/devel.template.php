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

$mainConfig= CMap::mergeArray(
	$mainConfig,
	array(
	'site'=>'http://localhost/vtcpng',
	'loginuser'=>'admin',
	'accesskey'=>'U7f44ZhKCZ8ZTkZU',

	// application components
	'components' => array(
	
		// Database settings
		/*
		'db' => array(
			'class' => 'CDbConnection',
			'connectionString' => 'mysql:host=localhost;dbname=vt_ts521devel',
			'charset' => 'utf8',
			'username' => 'root',
			'password' => '',
			'schemaCachingDuration' => 3600,
			'emulatePrepare' => true,
		),
		*/
	),
));
