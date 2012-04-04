<?php
/*
 * Chive - web based MySQL database management
 * Copyright (C) 2010 Fusonic GmbH
 *
 * This file is part of Chive.
 *
 * Chive is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * Chive is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public
 * License along with this library. If not, see <http://www.gnu.org/licenses/>.
 */

class SiteController extends Controller
{

	public function __construct($id, $module=null) {

		$request = Yii::app()->getRequest();

		if($request->isAjaxRequest)
		{
			$this->layout = false;
		}

		parent::__construct($id, $module);

	}

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * @see		CController::accessRules()
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('login', 'changeLanguage', 'changeTheme','sendmail'),
				'users' => array('*'),
			),
			array('deny',
				'users' => array('?'),
			),
		);
	}

	public function actionChangepassword()
	{

		$response = new AjaxResponse();
		$username=Yii::app()->user->name;
		$model=new User('search','Contacts');
		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			$pass=$_POST['User']['password'];
			if($model->savePassword($pass)) {
				echo 'success';
			}
		}
		$this->render('changepass',array('model'=>$model));
	}

	public function actionSendmail()
	{
		$username = Yii::app()->getRequest()->getParam('usernamemail');
		$model=new User('search','Contacts');
		$found=$model->findByPortalUserName($username);
		if(!$found) {
			echo 'wrongmail';
		} else {
			$sent=$model->sendRecoverPassword($username);
			if($sent) {
					echo 'success';
			} else {
					echo 'fail';
			}
		}
	}

	protected function createPassword($length) {
		$chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$i = 0;
		$password = "";
		while ($i <= $length) {
			$password .= $chars{mt_rand(0,strlen($chars)-1)};
			$i++;
		}
		return $password;
	}

	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) &&  $_POST['ajax']==='changepass-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		$this->render('index', array(
			'formatter' => Yii::app()->getDateFormatter()
		));
	}

	public function actionNoTranslate()
	{
		$this->render('index', array(
			'missingFunctions' => Yii::t('core','errmissingFunctions'),
			'formatter' => Yii::app()->getDateFormatter()
		));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$this->layout = "login";

		// Languages
		$availableLanguages = glob('protected/messages/??_??');
		$currentLanguage = Yii::app()->getLanguage();

		if(strlen($currentLanguage) == 2)
		{
			$currentLanguage .= '_' . $currentLanguage;
		}

		$languages = array();
		foreach($availableLanguages AS $key => $language) 
		{
			$full = basename($language);

			$languages[] = array(
				'label' => Yii::t('language', $full),
				'icon' => 'images/language/' . $full . '.png',
				'url' => Yii::app()->createUrl('/site/changeLanguage/' . $full),
				'htmlOptions' => array('class'=>'icon'),
			);
		}

		$availableThemes = Yii::app()->getThemeManager()->getThemeNames();
		$activeTheme = Yii::app()->getTheme()->getName();

		$themes = array();
		foreach($availableThemes AS $theme) {

			if($activeTheme == $theme)
				continue;

			$themes[] = array(
				'label'=> ucfirst($theme),
				'icon'=> '/themes/' . $theme . '/images/icon.png',
				'url'=>Yii::app()->request->baseUrl . '/site/changeTheme/' . $theme,
				'htmlOptions'=>array('class'=>'icon'),
			);
		}

		$form = new LoginForm();
		// collect user input data
		$request = Yii::app()->getRequest();
		if($request->isPostRequest || $request->getQuery("username") !== null)
		{
			if($request->isPostRequest)
			{
				$form->attributes = array(
					"username" => $request->getPost("username"),
					"password" => $request->getPost("password"),
				);
			}
			else
			{
				$form->attributes = array(
					"username" => $request->getQuery("username"),
					"password" => ($request->getQuery("password") !== null ? $request->getQuery("password") : ""),
				);
			}
			// validate user input and redirect to previous page if valid
			if($form->validate())
			{
				$this->redirect(Yii::app()->homeUrl);
			}
		}

		$forgotpassword = new ForgetPasswordForm();

		$validBrowser = true;
		if($_SERVER['HTTP_USER_AGENT'])
		{
			preg_match('/MSIE (\d+)\.\d+/i', $_SERVER['HTTP_USER_AGENT'], $res);
			if(count($res) == 2 && $res[1] <= 7)
			{
				$validBrowser = false;
			}
		}

		$this->render('login',array(
			'form'=>$form,
			'forgotpassword'=>$forgotpassword,
			'languages'=>$languages,
			'themes'=>$themes,
			'validBrowser' => $validBrowser,
		));
	}

	public function actionKeepAlive()
	{
		Yii::app()->end('OK');
	}

	/**
	 * Change the language
	 */
	public function actionChangeLanguage()
	{
		Yii::app()->session->add('language', Yii::app()->getRequest()->getParam('id'));
		$this->redirect(Yii::app()->createUrl('site/login'));
	}

	/**
	 * Change the theme
	 */
	public function actionChangeTheme()
	{
		Yii::app()->session->add('theme', $_GET['id']);
		$this->redirect(Yii::app()->homeUrl);
	}

	/**
	 * Logout the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	public function actionSearch()
	{             
		$query=Yii::app()->getRequest()->getParam('q');
		$items = array();
		$module='Accounts';
		$m=Yii::app()->getRequest()->getParam('module');
		if (empty($m)) $_GET['module']=$module;
		$lastSchemaName = '';
		$model=new Vtentity();
		$res=$model->findAllSearch($query);
		foreach($res AS $table)
		{
			$number=count($table);
			$module=$table[$number-1];
			$id=$table[$number-2];
			if($module =='Project') $name=$table[0];
			else  $name=$table[1];
			 
			if($module != $lastSchemaName)
			{
				$items[] = CJSON::encode(array(
						'text' => '<span class="icon schema">' . Html::icon('database') . '<span>' . StringUtil::cutText($module, 30) . '</span></span>',
						'target' => Yii::app()->createUrl('#vtentity/' . $module) . '/index',
						'plain' => $module,
				));
			}
			 
			$lastSchemaName=$module;
			$items[] = CJSON::encode(array(
					'text' => '<span class="icon table">' . Html::icon('table') . '<span>' . StringUtil::cutText($name, 30) . '</span></span>',
					'target' => Yii::app()->createUrl("#vtentity/$module/view/" . $id),
					'plain' => $name
			));
		}
		Yii::app()->end(implode("\n", $items));
	}

	/**
	 * Lists all modules.
	 */
	public function actionList()
	{
		// Create list for sideBar usage
		// Omit these modules because they are not yet supported
		$notSupported=Yii::app()->notSupportedModules[Yii::app()->vtyiicpngScope];
                $api_cache_id='yiicpng.sidebar.availablemodules';
                $schemata = Yii::app()->cache->get( $api_cache_id  );
               
                // If the results were false, then we have no valid data, so load it
                if($schemata===false){
                $schemata = array();
		$url = Yii::app()->site;
		$clientvtiger = new WSClient($url);
		$login = $clientvtiger->doLogin(Yii::app()->loginuser, Yii::app()->accesskey);
                if(!$login)
			Yii::log('login failed');
		else {
			// get available modules from vtiger CRM
			$listModules = $clientvtiger->doListTypes();
			// flatten array
			$flatlm=array();
			foreach($listModules AS $key=>$schema) {
				$flatlm[$key]=$schema['name'];
			}
			$listModules = $clientvtiger->doTranslate($flatlm, Yii::app()->getLanguage(), '');
			if (is_array($listModules)) {
				reset($flatlm);
				foreach($listModules AS $moduleName) {
					if ((Yii::app()->vtyiicpngScope=='vtigerCRM' and !in_array(current($flatlm), $notSupported))
					 or (Yii::app()->vtyiicpngScope=='CPortal' and in_array(current($flatlm), $notSupported)))
						$schemata[] = array('module'=>current($flatlm),'name'=>$moduleName);
					next(($flatlm));
				}
				if(($cache=Yii::app()->cache)!==null)
					$cache->set('yiicpng.sidebar.listmodules',$listModules);  // cache until next execution
                                        $cache->set('yiicpng.sidebar.availablemodules',$schemata);
			} else {
				$schemata=array(array('module'=>'notranslate','name'=>Yii::t('core','errNoTranslateFunction')));
			}
		}}            
		Yii::app()->endJson(CJSON::encode($schemata));
	}

}