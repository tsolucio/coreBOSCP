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
require(dirname(__FILE__).'/PortalConfig.php');

$mainConfig=array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name' => 'vtigerCRM vtyiiCPng',
	'theme' => 'standard',
	'site'=>$vtyiicpng_Server_Path,
	'loginuser'=>$vtyiicpng_Login_User,
	'accesskey'=>$vtyiicpng_Access_Key,
	'attachment_folder'=>$vtyiicpng_AttachmentFolderName,

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
			'forceTranslation' => true,
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

            	// Base entity model
				'vtentity/notranslate/index'=>'site/notranslate',
				'vtentity/<module:'.URL_MATCH.'>/AutoCompleteLookup'=>'vtentity/AutoCompleteLookup',

            	// Faq module specific class
            	'vtentity/<module:Faq>'=>'faq/index',
            	'vtentity/<module:Faq>/<action:'.URL_MATCH.'>'=>'faq/<action>',
            	'vtentity/<module:Faq>/<action:'.URL_MATCH.'>/<id:'.URL_MATCH.'>'=>'faq/<action>',
            	'vtentity/<module:Faq>/list/<id:'.URL_MATCH.'>/dvcpage/<dvcpage:'.URL_MATCH.'>'=>'faq/list',

            	// HelpDesk module specific class
            	'vtentity/<module:HelpDesk>'=>'helpdesk/index',
            	'vtentity/<module:HelpDesk>/<action:'.URL_MATCH.'>'=>'helpdesk/<action>',
            	'vtentity/<module:HelpDesk>/<action:'.URL_MATCH.'>/<id:'.URL_MATCH.'>'=>'helpdesk/<action>',
            	'vtentity/<module:HelpDesk>/list/<id:'.URL_MATCH.'>/dvcpage/<dvcpage:'.URL_MATCH.'>'=>'helpdesk/list',

            	// Project module specific class
            	'vtentity/<module:Project>'=>'project/index',
            	'vtentity/<module:Project>/<action:'.URL_MATCH.'>'=>'project/<action>',
            	'vtentity/<module:Project>/<action:'.URL_MATCH.'>/<id:'.URL_MATCH.'>'=>'project/<action>',
            	'vtentity/<module:Project>/list/<id:'.URL_MATCH.'>/dvcpage/<dvcpage:'.URL_MATCH.'>'=>'project/list',

            	// Default vtentity behaviour
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

	'vtyiicpngScope' => 'CPortal',  //  CPortal | vtigerCRM
	'notSupportedModules' => array(
			'vtigerCRM' => array(
				'Calendar','Events','Quotes','SalesOrder','PurchaseOrder','Invoice','Currency',
				'PriceBooks','Emails','Users','Groups','PBXManager','SMSNotifier','ModComments',
				'DocumentFolders'
			),
			'CPortal' => array(
					'Contacts','Accounts','Quotes','Services','Invoice','HelpDesk','Faq','Timecontrol',
					'Assets','Products','Documents','ProjectMilestone','ProjectTask','Project'
			),
		),

);
@include(dirname(__FILE__) . '/devel.php');
return $mainConfig;