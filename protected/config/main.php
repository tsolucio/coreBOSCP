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

define('URL_MATCH', '([^\/]*)');

$mainConfig=array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name' => 'vtigerCRM vtyiiCPng',
	'theme' => 'standard',
	'site'=>'http://studiosynthesisdemo.com/cpvtiger',
	'resource'=>'Accounts',
	'loginuser'=>'admin',
	'accesskey'=>'U7f44ZhKCZ8ZTkZU',
        //'accesskey'=>'2x3JIqix9MzwErR',

	// Activate debuging
	'debug'=>true,

	// preloading 'log' component
	'preload' => array('log'),
        
	// autoloading model and component classes
	'import' => array(
		'application.models.*',
		'application.components.*',
		'application.components.helpers.*',
		'application.components.helpers.utils.*',
		'application.components.vtwsclib.*',
		'application.components.vtwsclib.Vtiger.*',
		//'application.components.export.*',
		'application.controllers.*',
		'application.extensions.*',
		'application.extensions.VTActiveResource.*',
	),
	
	// application components
	'components' => array(
	
		'session' => array(
			'class' => 'ChiveHttpSession',
			'sessionName' => 'vtyiicpngSession',
			'cookieMode' => 'only',
			'savePath' => 'protected/runtime/sessions',
		),

		'request' => array(
			'class' => 'ChiveHttpRequest',
			'enableCookieValidation' => true,
		),

		'locale' => array(
			'dateFormat' => 'middle',
			'dateTimeFormat' => 'middle'
		),
		'coreMessages'=>array('basePath'=>'protected/messages'),

		// Log database
		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
				array(
					'class' => 'CFileLogRoute',
					'levels' => 'error, warning, info, trace',
				),
				array(
					'class' => 'CProfileLogRoute',
					'levels' => 'error, warning, info, trace',
					'showInFireBug' => false,
				),
			),
		),

		// User settings
		'user' => array(
			// Enable cookie-based authentication
			'allowAutoLogin' => true,
		),

		'messages' => array(
		    'class' => 'application.components.messages.CXmlMessageSource',
			'cachingDuration' => 24 * 60 * 60, // 24h
		),
		
		'cache' => array(
			'class' => 'CFileCache',
		),

		// URL-Manager
		'urlManager' => array(
            'urlFormat' => 'path',
			'showScriptName' => !isset($_GET['__vtcpng_refresh_on']),
            'rules' => array(

				// Site
                'site/changeLanguage/<id:(.*)>' => 'site/changeLanguage',
                'site/changeTheme/<id:(.*)>' => 'site/changeTheme',

				'vtentity/notranslate/index'=>'site/notranslate',
				'vtentity/<module:'.URL_MATCH.'>/AutoCompleteLookup'=>'vtentity/AutoCompleteLookup',
            	'vtentity/<module:'.URL_MATCH.'>'=>'vtentity/index',
            	'vtentity/<module:'.URL_MATCH.'>/<action:'.URL_MATCH.'>'=>'vtentity/<action>',
            	'vtentity/<module:'.URL_MATCH.'>/<action:'.URL_MATCH.'>/<id:'.URL_MATCH.'>'=>'vtentity/<action>',
           		'vtentity/<module:'.URL_MATCH.'>/list/<id:'.URL_MATCH.'>/dvcpage/<dvcpage:'.URL_MATCH.'>'=>'vtentity/list',
            ),
        ),

        // View Renderer (template engine)
        'viewRenderer' => array(
            'class' => 'CPradoViewRenderer',
        ),

	),

	// application-level parameters
	'params' => array(
		'iconPack' => 'fugue',
		'version' => '5.3',
	),

);
@include(dirname(__FILE__) . '/devel.php');
return $mainConfig;