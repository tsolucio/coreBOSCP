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

class ProjectController extends VtentityController
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
		$this->render('//project/index',array(
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
		$this->render('//project/_view',array(
			'model'=>$model,
			'data'=>$model,
			'fields'=>$fields,
			'uitypes'=>$uitypes,
		));
	}

}