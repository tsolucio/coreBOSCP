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