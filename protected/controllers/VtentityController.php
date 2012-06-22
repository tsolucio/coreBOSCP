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

class VtentityController extends Controller
{
	/**
	 * @var string the default layout for the views.
	 */
	public $layout='_table';

	/**
	 * @var Controller variables
	 */
	public $_model;
	public $modelName='Vtentity';
	public $modelLinkName='vtentity';
	public $entity;  // vtiger CRM entity name
	public $entityLookupField;
	public $entityidField;
	public $entityidValue;
	public $viewButtonEdit=false;
	public $viewButtonDelete=false;
	public $viewButtonCreate=false;
	public $viewButtonSearch=false;
	public $viewButtonDownloadPDF=false;

	public function __construct($id,$module)
	{
		$modelclass=$this->modelName;
		$model=$modelclass::model();
                $this->_model=$model;
		$this->entityLookupField=$model->getLookupField();
		$this->entityidField=$model->primaryKey();
		$this->entity=$model->getModule();
		parent::__construct($id,$module);
	}

	public function setCRUDpermissions($module) {
		$moduleAccessInformation = Yii::app()->cache->get('moduleAccessInformation');
		$this->viewButtonDelete = $moduleAccessInformation[$module]['deleteable'];
		$this->viewButtonEdit = $moduleAccessInformation[$module]['updateable'];
		//$this->viewButtonCreate = $moduleAccessInformation[$module]['createable'];
		// at this moment vtiger CRM has these two concepts together so, although REST has them
		// separate, create always comes true so we ignore it and map it to the edit permissions
		$this->viewButtonCreate = $this->viewButtonEdit;
		$this->viewButtonSearch = $moduleAccessInformation[$module]['retrieveable'];
	}

	public function vtyii_canCreate($module) {
		$moduleAccessInformation = Yii::app()->cache->get('moduleAccessInformation');
		//return $moduleAccessInformation[$module]['createable'];
		// at this moment vtiger CRM has these two concepts together so, although REST has them
		// separate, create always comes true so we ignore it and map it to the edit permissions
		return $moduleAccessInformation[$module]['updateable'];
	}

	public function vtyii_canEdit($module) {
		$moduleAccessInformation = Yii::app()->cache->get('moduleAccessInformation');
		// normally to decide this we would need to consult the permission not only on the module
		// but also the record ID. I count on the vtiger CRM REST interface for the record ID decision
		// As is, the REST will only return records the configured user has access to, so for any ID
		// that can be passed in through the application the module edit permission is valid
		// for any ID that can be forced into the URL, instead of calling REST to find out, we will send
		// it in and get a REST error in return
		return $moduleAccessInformation[$module]['updateable'];
	}

	public function vtyii_canDelete($module) {
		$moduleAccessInformation = Yii::app()->cache->get('moduleAccessInformation');
		// normally to decide this we would need to consult the permission not only on the module
		// but also the record ID. I count on the vtiger CRM REST interface for the record ID decision
		// As is, the REST will only return records the configured user has access to, so for any ID
		// that can be passed in through the application the module edit permission is valid
		// for any ID that can be forced into the URL, instead of calling REST to find out, we will send
		// it in and get a REST error in return
		return $moduleAccessInformation[$module]['deleteable'];
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
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','list','AutoCompleteLookup','Download','DownloadPDF','Addticket'),
				'users'=>array('@'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		// Eliminate any previous search conditions
		unset($_SESSION[$this->modelName]);

		$model=new $this->modelName;
		if (!$this->vtyii_canCreate($model->getModule())) {
			$response = new AjaxResponse();
			$response->addNotification('error', Yii::t('core', 'error'), Yii::t('core', 'errorCreateRow')."<br>".Yii::t('core', 'errorPermssion'));
			$response->send();
			return true;
		}
		$fields=$model->getWritableFieldsArray();
		$uitypes=$model->getUItype();
                $model->setIsNewRecord(true);
		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);

		// FIXME  uitype 33 MUST be transformed before saving, specially if it has validations
		if(isset($_POST[$this->modelName]))
		{
			$response = new AjaxResponse();
			$model->unsetAttributes();
			$model->setAttributes($_POST[$this->modelName]);
                        if($model->getModule()=='Documents' && $model->getAttribute('filelocationtype')=='I')
                        {
                        $uploadfile=CUploadedFile::getInstance($model,'filename');
                        $tmp_file=$uploadfile->getTempName();
                        $cont=base64_encode(file_get_contents($tmp_file));
                        $model->filename=array(
                        		"name"=>$uploadfile->getName(),
                        		"size"=>$uploadfile->getSize(),
                        		"type"=>$uploadfile->getType(),
                        		'content'=>$cont);//array("attachment_name"=>"ta.pdf",'attachment'=>"ta.pdf");
                        }
                        if($model->save()) {
				$_SESSION['deleteCache']='true'; // empty cache on next call
				$response->addNotification('success', Yii::t('core', 'success'), Yii::t('core', 'successCreateRow'));
				$response->redirectUrl = '#'.$this->modelLinkName.'/'.$this->entity.'/view/' . $model->__get($this->entityidField);
			} else {
				$response->addNotification('error', Yii::t('core', 'error'), Yii::t('core', 'errorCreateRow')."<br>".$model->getLastError());
				$response->addData(null, $this->render('//vtentity/create',array('model'=>$model,'fields'=>$fields,'uitypes'=>$uitypes),true));
			}
			$response->send();
			return true;
		}

		$this->render('//vtentity/create',array(
			'model'=>$model,
			'fields'=>$fields,
			'uitypes'=>$uitypes,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionUpdate()
	{
		$this->_model=null;
		$model=$this->loadModel(false);
		if (!$this->vtyii_canEdit($model->getModule())) {
			$response = new AjaxResponse();
			$response->addNotification('error', Yii::t('core', 'error'), Yii::t('core', 'errorUpdateRow')."<br>".Yii::t('core', 'errorPermssion'));
			$response->send();
			return true;
		}
		$fields=$model->getWritableFieldsArray();
		$uitypes=$model->getUItype();               
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName]))
		{
			$response = new AjaxResponse();
			$model->unsetAttributes();
			$model->setAttributes($_POST[$this->modelName]);
                        if($model->getModule()=='Documents' && $model->getAttribute('filelocationtype')=='I')
                        {
                        $uploadfile=CUploadedFile::getInstance($model,'filename');
                        $tmp_file=$uploadfile->getTempName();
                        $cont=base64_encode(file_get_contents($tmp_file));
                        $model->filename=array(
                        		"name"=>$uploadfile->getName(),
                        		"size"=>$uploadfile->getSize(),
                        		"type"=>$uploadfile->getType(),
                        		'content'=>$cont);//array("attachment_name"=>"ta.pdf",'attachment'=>"ta.pdf");
                        }
			if($model->save()) {
				$response->addNotification('success', Yii::t('core', 'success'), Yii::t('core', 'successUpdateRow'));
				if (empty($_POST['dvcpage']) or $_POST['dvcpage']<1)
				$response->redirectUrl = '#'.$this->modelLinkName.'/'.$this->entity.'/view/' . $model->__get($this->entityidField);
				else
				$response->redirectUrl = '#'.$this->modelLinkName.'/'.$this->entity.'/list/' . $model->__get($this->entityidField).'/dvcpage/'.$_POST['dvcpage'];
			} else {
				$response->addNotification('error', Yii::t('core', 'error'), Yii::t('core', 'errorUpdateRow')."<br>".$model->getLastError());
				$response->addData(null, $this->render('//vtentity/update',array('model'=>$model,'fields'=>$fields,'uitypes'=>$uitypes),true));
			}
			$response->send();
			return true;
		}

		$this->render('//vtentity/update',array(
			'model'=>$model,
			'fields'=>$fields,
			'uitypes'=>$uitypes,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 */
	public function actionDelete()
	{
		// Eliminate any previous search conditions
		unset($_SESSION[$this->modelName]);

		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->_model=null;
			$model=$this->loadModel();
			if (!$this->vtyii_canDelete($model->getModule())) {
				$response = new AjaxResponse();
				$response->addNotification('error', Yii::t('core', 'error'), Yii::t('core', 'errorDeleteRow')."<br>".Yii::t('core', 'errorPermssion'));
				$response->send();
				return true;
			}
			$result=$model->delete();
			$response = new AjaxResponse();
			if ($result) {
				$response->addNotification('success', Yii::t('core', 'success'), Yii::t('core', 'successDeleteRow'));
				$_SESSION['deleteCache']='true'; // empty cache on next call
				// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
				if(!isset($_GET['ajax']))
					$response->redirectUrl = '#'.$this->modelLinkName.'/'.$this->entity.'/index';
			} else {
				$response->addNotification('error', Yii::t('core', 'error'), Yii::t('core', 'errorDeleteRow'));
			}
			$response->send();
		}
		else
			throw new CHttpException(400,Yii::t('core', 'invalidRequest'));
	}

	/**
	 * Lists all records in DetailView (yii List View).
	 */
	public function actionList()
	{
		$pos=array('pageSize'=>1);
		if(isset($_GET['dvcpage'])) {
			$pos['currentPage']=$_GET['dvcpage'];                        
			unset($_GET['dvcpage']);
		}	
                $model=$this->_model;
                $model->unsetAttributes();
                $model->setScenario('search');
		if(isset($_GET[$this->modelName])) {                  
			$model->setAttributes($_GET[$this->modelName]);
			$_SESSION[$this->modelName]=$_GET[$this->modelName];
		} elseif (isset($_SESSION[$this->modelName])) {                  
			$model->setAttributes($_SESSION[$this->modelName]);
		}                                   

		$this->setCRUDpermissions($model->getModule());
		$this->viewButtonSearch=false;
		$this->viewButtonDownloadPDF=true;
		$dataProvider=$model->search($pos);
		$this->render('//vtentity/index',array(
			'dataProvider'=>$dataProvider,
			'model'=>$model,
		));
	}

	/**
	 * Lists all records in ListView (yii Grid View)
	 */
	public function actionIndex()
	{
		// page size drop down changed
		if (isset($_GET['pageSize'])) {
			// pageSize will be set on user's state
			Yii::app()->user->settings->set('pageSize',(int)$_GET['pageSize']);
			// unset the parameter as it would interfere with pager and repetitive page size change
			unset($_GET['pageSize']);
		}
		
                $model=$this->_model;
                $model->unsetAttributes();
                $model->setScenario('search');
                $fields=$model->getWritableFieldsArray();
		$uitypes=$model->getUItype();
		if (!empty($_SESSION['deleteCache']) and $_SESSION['deleteCache']=='true') {
			$model->deleteCache=true;
			unset($_SESSION['deleteCache']);
		}
		if(isset($_GET[$this->modelName])) {
			$model->setAttributes($_GET[$this->modelName]);
			$_SESSION[$this->modelName]=$_GET[$this->modelName];
		} elseif (isset($_SESSION[$this->entity])) {
			$model->setAttributes($_SESSION[$this->modelName]);
		}                

		$this->setCRUDpermissions($model->getModule());
		$this->viewButtonEdit=false;
		$this->viewButtonDelete=false;
		$this->render('//vtentity/admin',array(
			'model'=>$model,                    
			'fields'=>$fields,                
			'uitypes'=>$uitypes,
		));
	}

	/**
	 * Displays a particular model.
	 */
	public function actionView()
	{
		$this->_model=null;
		$model=$this->loadModel();
		$this->setCRUDpermissions($model->getModule());
		$this->viewButtonSearch=false;
		$this->render('//vtentity/view',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 */
	public function loadModel($dereference=true)
	{
		if($this->_model===null)
		{
			$entity=$this->modelName;
			$model=$entity::model();
			$model->doDereference=$dereference;
			if(isset($_GET[$this->entityidField]))
				$this->_model=$model->findbyPk($_GET[$this->entityidField]);                        
			if($this->_model===null)
			  throw new CHttpException(404,Yii::t('core', 'errorIdNotExist'));                        
		}                
		if($this->_model) $this->_model->setIsNewRecord(false);                
		$this->entityidValue=$this->_model->__get($this->entityidField);
		return $this->_model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']===$this->modelName.'-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	// http://www.yiiframework.com/wiki/25/using-cautocomplete-to-display-one-value-and-submit-another/
	public function actionAutoCompleteLookup()
	{
		if(Yii::app()->request->isAjaxRequest && isset($_GET['term']))
		{
			// term is the default GET variable name that is used by
			// the autocomplete widget to pass in user input
			$name = $_GET['term'];
			// this was set with the "max" attribute of the CAutoComplete widget
			$limit = min((isset($_GET['limit']) ? $_GET['limit'] : 100), 40);
			$criteria = new CDbCriteria;
			// lookupfield can contain list of fields
			$lookup_fields=explode(',', $this->entityLookupField);
			$criteria->condition = implode(" LIKE :sterm OR ", $lookup_fields)." LIKE :sterm";
			$criteria->params = array(":sterm"=>"%$name%");
			$criteria->limit = $limit;
			$model=Vtentity::model();
			$model->setCriteria($criteria);
			$entityArray = $model->getData($this->entityLookupField);
			$returnVal = array();
			foreach($entityArray as $entityRecord)
			{
				$returnVal[]= array('value'=>$model->getLookupFieldValue($this->entityLookupField, $entityRecord),
						'id'=>$entityRecord[$this->entityidField]);
			}
			echo CJSON::encode($returnVal);
		}
	}

	public function actionDownload() {
		$id=$_GET['id'];
		$saveasfile = "protected/runtime/cache/_$id";
		if(!file_exists($saveasfile)) {
			$model=Vtentity::model();
			$attachmentsdata=$model->getDocumentAttachment($id,true);
			$model->writeAttachment2Cache($id,$attachmentsdata[$id]['attachment']);
		}
		if(file_exists($saveasfile)) {
			header("Content-type: ".$_GET['ft']);
			header("Content-Type: application/download");
			header("Pragma: public");
			header("Cache-Control: private");
			header("Content-Disposition: attachment; filename=".$_GET['fn']);
			header("Content-Description: vtyiicpng download");
			readfile($saveasfile);
		}
	}

	public function actionDownloadPDF() {
		$model=Vtentity::model();
		$clientvtiger=$model->getClientVtiger();
		if(!$clientvtiger) {
			Yii::log('login failed');
			$recordInfo=0;
		} else {
			$recordInfo = $clientvtiger->doInvoke('getpdfdata',array('id'=>$_GET['id']));
		}
		if(empty($recordInfo) || !$recordInfo) {
			return null;
		} else {
			header("Content-Type: application/force-download");
			header('Content-Type: application/pdf');
			header("Content-Type: application/download");
			header("Expires: 0"); // set expiration time
			header("Pragma: public");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			$fn=$model->getModuleName().$recordInfo[0]['recordid'];
			header("Content-Disposition: attachment; filename=$fn.pdf");
			header("Content-Description: vtyiicpng download PDF");
			echo base64_decode($recordInfo[0]['pdf_data']);
		}
	}

	/**
	 * Create a new comment attached to the related entity
	 */
	public function actionAddticket()
	{
		$response = new AjaxResponse();
		$values = array();
		$model=Vtentity::model();
		$module=$model->getModule();
		$view=strtolower($module);
		switch ($module) {
			case 'HelpDesk':
				$values['from_portal']=1;
				$values['parent_id']=Yii::app()->user->contactId;
				// break has been delibertly left out!
			case 'Faq':
				$values['comments']=$_POST['ItemCommentParam'];
				$clientvtiger=$model->getClientVtiger();
				if(!$clientvtiger) {
					Yii::log('login failed');
					$recordInfo=0;
				} else {
					$recordInfo = $clientvtiger->doInvoke('addTicketFaqComment',array('id'=>$_GET['id'],'values'=>CJSON::encode($values)));
				}
				if($clientvtiger->hasError($recordInfo)) {
					$response->addNotification('error', Yii::t('core', 'error'), Yii::t('core', 'errorCreateRow')."<br>".$clientvtiger->lastError());
				} else {
					$response->addNotification('success', Yii::t('core', 'success'), Yii::t('core', 'successCreateRow'));
					$relComments = $clientvtiger->doGetRelatedRecords($_GET['id'], $module, 'ModComments', '');
					$response->addData(null, $this->renderPartial("//$view/_comments",array('relComments'=>$relComments),true));
				}
				break;
			default: // modcomments
				break;
		}
		$response->send();
		return true;
	}

}
