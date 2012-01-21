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

/*
 * READ Project model for instructions on how to customize this for any database table.
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
	private $_model;
	public $modelName='Vtentity';
	public $modelLinkName='vtentity';
	public $entity;  // vtiger CRM entity name
	public $entityLookupField;
	public $entityidField;
	public $entityidValue;
	public $viewButtonActive=FALSE;
	public $viewButtonSearch=FALSE;

	public function __construct($id,$module)
	{
		$modelclass=$this->modelName;
		$model=$modelclass::model();
		$this->entityLookupField=$model->getLookupField();
		$this->entityidField=$model->primaryKey();
		$this->entity=$model->getModule();
		parent::__construct($id,$module);
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
				'actions'=>array('index','view','list','AutoCompleteLookup'),
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
		$fields=$model->getWritableFieldsArray();
		$uitypes=$model->getUItype($this->entity);

		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);

		// FIXME  uitype 33 MUST be transformed before saving, specially if it has validations
		if(isset($_POST[$this->modelName]))
		{
			$response = new AjaxResponse();
			$model->setAttributes($_POST[$this->modelName]);
			if($model->save()) {
				$response->addNotification('success', Yii::t('core', 'success'), Yii::t('core', 'successCreateRow'));
				$response->redirectUrl = '#'.$this->modelLinkName.'/'.$this->entity.'/view/' . $model->__get($this->entityidField);
			} else {
				$response->addNotification('error', Yii::t('core', 'error'), Yii::t('core', 'errorCreateRow')."<br>".$model->getLastError());
				$response->addData(null, $this->render('create',array('model'=>$model,'fields'=>$fields,'uitypes'=>$uitypes),true));
			}
			$response->send();
			return true;
		}

		$this->render('create',array(
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
		$model=$this->loadModel();
		$fields=$model->getWritableFieldsArray();
		$uitypes=$model->getUItype($this->entity);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName]))
		{
			$response = new AjaxResponse();
			$model->unsetAttributes();
			$model->setAttributes($_POST[$this->modelName]);
			if($model->save()) {
				$response->addNotification('success', Yii::t('core', 'success'), Yii::t('core', 'successUpdateRow'));
				$response->redirectUrl = '#'.$this->modelLinkName.'/'.$this->entity.'/list/' . $model->__get($this->entityidField).'/dvcpage/'.$_POST['dvcpage'];
			} else {
				$response->addNotification('error', Yii::t('core', 'error'), Yii::t('core', 'errorUpdateRow')."<br>".$model->getLastError());
				$response->addData(null, $this->render('update',array('model'=>$model,'fields'=>$fields,'uitypes'=>$uitypes),true));
			}
			$response->send();
			return true;
		}

		$this->render('update',array(
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
			$result=$this->loadModel()->delete();

			$response = new AjaxResponse();
			if ($result) {
				$response->addNotification('success', Yii::t('core', 'success'), Yii::t('core', 'successDeleteRow'));
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
		$this->viewButtonActive=true;
		$pos=array('pageSize'=>1);
		if(isset($_GET['dvcpage'])) {
			$pos['currentPage']=$_GET['dvcpage'];
			unset($_GET['dvcpage']);
		}
		$model=new $this->modelName('search');
		if(isset($_GET[$this->modelName])) {
			$model->setAttributes($_GET[$this->modelName]);
			$_SESSION[$this->modelName]=$_GET[$this->modelName];
		} elseif (isset($_SESSION[$this->modelName])) {
			$model->setAttributes($_SESSION[$this->modelName]);
		}

		$dataProvider=$model->search($pos);
		$this->render('index',array(
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

		$model=new $this->modelName('search');
		$fields=$model->getWritableFieldsArray();
		$uitypes=$model->getUItype($this->entity);
		if(isset($_GET[$this->modelName])) {
			$model->setAttributes($_GET[$this->modelName]);
			$_SESSION[$this->modelName]=$_GET[$this->modelName];
		} elseif (isset($_SESSION[$this->entity])) {
			$model->setAttributes($_SESSION[$this->modelName]);
		}

		$this->viewButtonSearch=true;
		$this->render('admin',array(
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
		$this->viewButtonActive=true;
		$this->render('view',array(
			'model'=>$this->loadModel(),
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 */
	public function loadModel()
	{
		if($this->_model===null)
		{
			$entity=$this->modelName;
			if(isset($_GET[$this->entityidField]))
				$this->_model=$entity::model()->findbyPk($_GET[$this->entityidField]);
			if($this->_model===null)
				throw new CHttpException(404,Yii::t('core', 'errorIdNotExist'));
		}
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
			$limit = min($_GET['limit'], 40);
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

}
