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

$mainConfig= CMap::mergeArray(
	$mainConfig,
	array(
	'site'=>'http://localhost/vtcpng',
	'resource'=>'Accounts',
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
