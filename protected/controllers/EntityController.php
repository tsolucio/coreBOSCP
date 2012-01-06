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

class EntityController extends Controller
{
	const PAGE_SIZE=10;

	/**
	 * @var string specifies the default action
	 */
	public $defaultAction='browse';

	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_table;
	public $table;	
	public $schema='entity';
	public $lookupfield;
	public $entityid;
	public $entityidValue;
	public $viewButtonActive=FALSE;
	public $currentView='GridView'; // 'GridView' or 'ListView'

	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='_table';

	public function __construct($id, $module = null)
	{
		$request = Yii::app()->getRequest();		
		$this->table=$request->getParam('table');
		// FIXME  establish default lookup field and entityid based on vtiger CRM definition
		$this->entityid='id';
                //$clientvtiger=Entity::model($this->table)->getClientvtiger();
		$this->lookupfield=Entity::model($this->table)->getLookupField();
//		$this->entityid=$entityids[$this->table];
		parent::__construct($id, $module);
	}

	/**
	 * @return array action filters
	 */
//	public function filters()
//	{
//		return array(
//			'accessControl', // perform access control for CRUD operations
//		);
//	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // deny all users
				'users'=>array('*'),
			),
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','search','delete','admin','index','deleteid'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
      public function actionChangepassword()
	{
		$this->render('changepass');
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id=0)
	{
		if (empty($id)) {  // if no record to show we go to browse
			$this->actionBrowse();
			return true;
		}
		$this->viewButtonActive=true;
		$this->entityidValue=$id;
		$_GET['Entity_page'] = Yii::app()->getRequest()->getParam('Entity_page',1);
		$model=Entity::model($this->table);
		$dataProvider=$model->search(1,'view');
		$this->render('index',array(
			'model'=>$model,
                        'dataProvider'=>$dataProvider,
		));            
	}

	/**
	 * Displays one particular record.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionGoto($id=0)
	{
		if (empty($id)) {  // if no record to show we go to browse
			$this->actionBrowse();
			return true;
		}
		$this->viewButtonActive=true;
		$this->entityidValue=$id;
		$model=Entity::model($this->table);
		$model->setAttribute($this->entityid,$id);  // search only for this one
		$dataProvider=$model->search(1,'view');
		$this->render('index',array(
			'model'=>$model,
			'dataProvider'=>$dataProvider,
		));            
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		//$response = new AjaxResponse();
		$model=Entity::model($this->table);
                $fields=$model->getWritableFieldsArray();
                $module=$this->table;
                $uitypes=$model->getUItype($module);	

		// FIXME  uitype 33 MUST be transformed before saving, specially if it has validations
		if(isset($_POST['Entity']))
		{
			$model->_attributes=$_POST['Entity'];
                        $this->performAjaxValidation($model);
                        $newid=$model->create($_POST['Entity']);
			if(!empty($newid)) {				
				$baseurl=Yii::app()->baseUrl;
				$this->redirect($baseurl."/index.php#entity/".$module."/goto/id/".$newid);
				return true;
			} 
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
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionEdit($id)
	{
		$model=$this->loadModel($id);
                $module=$model->getModule();
                $fields=$model->getWritableFieldsArray();
                $uitypes=$model->getUItype($module);
                
		if(isset($_POST['Entity']))
		{
                        $model->unsetAttributes();
                        Yii::log(count($model->_attributes));
			$model->_attributes=$_POST['Entity'];
                        $this->performAjaxValidation($model);
			if($model->updateById($id,$_POST['Entity'])) {
                                $baseurl=Yii::app()->baseUrl;                               
				if (Yii::app()->getRequest()->getParam('currentView')=='GridView')
				$this->redirect($baseurl."/index.php#entity/".$module."/browse");                                     
				else				
                                $this->redirect($baseurl."/index.php#entity/".$module."/view/id/".$id);
				return true;
			}
		}
		$this->render('update',array(
			'model'=>$model,
                        'fields'=>$fields,
                        'uitypes'=>$uitypes,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'browse' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete()
	{
		$response = new AjaxResponse();
		$data = CJSON::decode($_POST['data'], true);
		$sql = "";
		try
		{
			foreach($data AS $attributes)
			{
				$row = Entity::model($this->table)->findByAttributes($attributes);
				$sql .= $row->delete($attributes) . "\n\n";
			}
		}
		catch (DbException $ex)
		{
			$response->addNotification('error', Yii::t('core', 'errorDeleteRow'), $ex->getText(), $sql, array('isSticky'=>true));
		}
		$response->refresh = true;
		$response->addNotification('success', Yii::t('core', 'successDeleteRows', array(count($data), '{rowCount}' => count($data))), null, $sql);
		$response->send();
	}

	public function actionDeleteid($id)
	{
		$response = new AjaxResponse();
		$model=new Entity($this->table);
		$responsemessage = $model->deleteById($id);
		$response->redirectUrl = Yii::app()->baseUrl . '/index.php#entity/' . $this->table . '/browse';
		$response->reload = true;
		if ($responsemessage[0]=='OK')
		$response->addNotification('success',Yii::t('core', 'successDeleteRows', array(1, '{rowCount}' => 1)), null, $responsemessage[1]);
		else
		$response->addNotification('error',Yii::t('core', 'errorDeleteRow'), null, $responsemessage[1]);
		$response->send();
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider($this->table);
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	public function actionBrowse($dProvider=0)
	{
		$model=Entity::model($this->table);
		if(isset($_GET['Entity']) && is_array($_GET['Entity'])) {
			// overwrite values with the ones comming in from the grid search
			$model->setAttributes($_GET['Entity'],false);  // FIXME: we can use true if we implement validators and safe field support in the EActiveResource class
		}
		// page size drop down changed
		if (isset($_GET['pageSize'])) {
			// pageSize will be set on user's state
			Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
			// unset the parameter as it would interfere with pager and repetitive page size change
			unset($_GET['pageSize']);
		}
		$lvf=$model->getListViewFields();

		if(empty($dProvider))   {
			$dataProvider=$model->search();
		} else {
			$dataProvider=$dProvider;
		}

		$_GET['Entity_page'] = Yii::app()->getRequest()->getParam('Entity_page',1);
		$this->render('admin',array(
			'model'=>$model,
			'dataProvider'=>$dataProvider,
			'lvfields'=>$lvf['fields'],
			'lvlinkfields'=>$lvf['linkfields'],
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=Entity::model($this->table);
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Entity']))
			$model->attributes=$_GET[$this->schema];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Entity::model($this->table);
		$model->findById($id);               
		if($model===null)
			throw new CHttpException(404,Yii::t('core', 'errorIdNotExist')."  ($id)");
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='entity-form')
		{
                        echo cpngActiveForm::validate($model);
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
          $lookup_fields=explode(',', $this->lookupfield);
		  $criteria->condition = implode(" LIKE :sterm OR ", $lookup_fields)." LIKE :sterm";          	
          $criteria->params = array(":sterm"=>"%$name%");
          $criteria->limit = $limit;
          $model=Entity::model($this->table);
          $model->setCriteria($criteria);
          $entityArray = $model->getData($this->lookupfield);
          $returnVal = array();
          foreach($entityArray as $entityRecord)
          {
          	$returnVal[]= array('value'=>$model->getLookupFieldValue($this->lookupfield, $entityRecord),
                          		 'id'=>$entityRecord[$this->entityid]);
          }
          echo CJSON::encode($returnVal);
       }
    }

	public function actionSearch() {

		$operatorConfig = array(				// needs value
			'LIKE'				=> 		array( 'needsValue' => true, 'translate' => 'opLike' ),
			'NOT LIKE'			=> 		array( 'needsValue' => true, 'translate' => 'opNotLike' ),
			'='					=> 		array( 'needsValue' => true, 'translate' => 'opEqual' ),
			'!=' 				=> 		array( 'needsValue' => true, 'translate' => 'opNotEqual' ),
			'REGEXP'			=> 		array( 'needsValue' => "?", 'translate' => 'opRegExp' ),
			'NOT REGEXP'		=> 		array( 'needsValue' => "?", 'translate' => 'opNotRegExp' ),
			'IS NULL'			=> 		array( 'needsValue' => false, 'translate' => 'opIsNull' ),
			'IS NOT NULL' 		=> 		array( 'needsValue' => false, 'translate' => 'opIsNotNull' ),
		);

		$operators = array_keys($operatorConfig);
		$idx=0;
		foreach ($operatorConfig as $key => $value) {
			$optexts[$idx]=Yii::t('core', $value['translate']);
			$idx++;
		}
		$config = array_values($operatorConfig);

		$row = Entity::model($this->table);
		$criter=new CDbCriteria();
		//$commandBuilder = $this->db->getCommandBuilder();
		if(isset($_POST['Row']))
		{
			$criteria=' where ';
			$i = 0;
			foreach($_POST['Row'] AS $column=>$value)
			{
				$operator = $operators[$_POST['operator'][$column]];
				if(strlen($value)>0)
				{
					$criteria .= ($i>0 ? ' AND ' : ' ') . $column . ' ' . $operator . ' ' ."'". $value."'"; 
					$criter->condition .= ($i>0 ? " AND " : " ") ." ".$column." ". $operator . " '" . $value."'";
				}
				elseif(isset($_POST['operator'][$column]) && $config[$_POST['operator'][$column]]['needsValue'] === false)
				{
					$criteria .= ($i>0 ? ' AND ' : ' ') . $column . ' ' . $operator;
					$criter->condition .= ($i>0 ? " AND " : " ") ." ".$column ." ".$operator;
				$i++;
				}
			}
			$module=$this->table;
			$query=$row->createFindSQLCommand($module, $criteria);
		}
		elseif(isset($_POST['query']))
		{
			$query = $_POST['query'];
		}

		if(isset($query))
		{
			$dProvider= new CActiveDataProvider('Entity', array(
				'criteria'=>$criter,
				'pagination'=>array(
					'pageSize'=>Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']),
					'route'=>'#entity/' . $this->getModule() . '/browse/',
				),
				'sort'=>array(
					'route'=>'#entity/' . $this->getModule() . '/browse/',
				),
			));
			$this->actionBrowse($dProvider);
		}
		else
		{
			$this->render('search', array(
				'row' => $row,
				'operators'=> $optexts,
			));
		}
	}

}
