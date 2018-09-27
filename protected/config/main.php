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

define('URL_MATCH', '([^\/]*)');
require(dirname(__FILE__).'/PortalConfig.php');

$mainConfig=array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name' => 'coreBOSCP',
	'theme' => 'standard',
	'site'=>$evocp_Server_Path,
	'loginuser'=>$evocp_Login_User,
	'accesskey'=>$evocp_Access_Key,
	'attachment_folder'=>$evocp_AttachmentFolderName,
	'company_tickets'=>$evocp_CompanyTickets,

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
					'Assets','Products','Documents','ProjectMilestone','ProjectTask','Project','CobroPago'
			),
		),

);
@include(dirname(__FILE__) . '/devel.php');
return $mainConfig;