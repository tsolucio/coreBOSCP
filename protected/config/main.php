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

define('URL_MATCH', '([^\/]*)');
require(dirname(__FILE__).'/PortalConfig.php');

$mainConfig=array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name' => 'evoCP',
	'theme' => 'standard',
	'site'=>$evocp_Server_Path,
	'loginuser'=>$evocp_Login_User,
	'accesskey'=>$evocp_Access_Key,
	'attachment_folder'=>$evocp_AttachmentFolderName,

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
		'version' => '1.0',
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