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

class HelpDeskController extends VtentityController
{
	
	public function __construct($id,$module)
	{
		parent::__construct($id,$module);
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
			actionCleansearch($model->getModule());
			Yii::app()->session[$model->getModule().'_searchvals']=$_GET[$this->modelName];
		} elseif (isset(Yii::app()->session[$model->getModule().'_searchvals'])) {                  
			$model->setAttributes(Yii::app()->session[$model->getModule().'_searchvals']);
		}                                   

		$this->setCRUDpermissions($model->getModule());
		$this->viewButtonSearch=false;
		$this->viewButtonDownloadPDF=false;
		$dataProvider=$model->search($pos);
		$fields=$model->getFieldsInfo();
		$uitypes=$model->getUItype();
		$this->render('//helpdesk/index',array(
			'dataProvider'=>$dataProvider,
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
		$fields=$model->getFieldsInfo();
		$uitypes=$model->getUItype();
		$this->render('//helpdesk/_view',array(
			'model'=>$model,
			'data'=>$model,
			'fields'=>$fields,
			'uitypes'=>$uitypes,
		));
	}

}