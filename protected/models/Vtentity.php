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

class Vtentity extends CActiveRecord
{
	public $modelLinkName='vtentity';
	public $tablename;
	public $entityidField;

	public function __construct()
	{
		parent::__construct();
		$this->tablename=$this->getModule();
		$this->entityidField=$this->primaryKey();
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @return Project the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * return array of fields to appear in grid view
	 */
	public function gridViewColumns() {
		$lvf=$this->getListViewFields();
		$listviewfields=array_values($lvf['fields']);
		$fields=$this->getFieldsInfo();
		$module=$this->getModule();
		$fieldlabels=array();
		foreach ($listviewfields as $lvfield) {
			$found = false;
			foreach($fields as $pos=>$fielddetails){
				if ($fielddetails['name']==$lvfield) {
					$found = true;
					break;
				}
			}
			if (!$found) continue;
			$field=$fields[$pos];
			$key=$field['name'];
			$label=$field['label'];
			if (in_array($key,explode(',',$this->getLookupField())) || (strpos($key,'title') and in_array($module,array('HelpDesk','Documents')))) {
				$fieldlabels[$key]=array('name'=>$key,'header'=>$label,'type'=>'html','value'=>$this->getViewLinkField($key));
			} else {
				$fieldlabels[$key]=array('name'=>$key,'header'=>$label,'type'=>$this->convertVtigerUIType2Yii($field['uitype']));
			}
		}
		return $fieldlabels;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that will receive user inputs.
		$fldinfo = $this->getFieldsGroupedByType();
		$mandatory=$fldinfo['obligatorio'];
		$integral = $fldinfo['entero'];
		$numerical = $fldinfo['real'];
		$email = $fldinfo['email'];
		$dateinfo = $fldinfo['fecha'];
		$url = $fldinfo['url'];
		$writable=$this->getWritableFieldsArray();
		$attributes=array();
		foreach($writable as $write) {
			if (!is_array($write)) continue;
			array_push($attributes,$write['name']);
		}
		$safeFields=implode(',',$attributes);
		$return = array();
		if (!empty($mandatory)) $return[] = array($mandatory, 'required');
		if (!empty($integral)) $return[] = array($integral, 'numerical', 'integerOnly'=>true);
		if (!empty($numerical)) $return[] = array($numerical, 'numerical');
		if (!empty($dateinfo['fields'])) $return[] = array($dateinfo['fields'], 'date', 'format'=>$dateinfo['format']);
		if (!empty($email)) $return[] = array($email, 'email');
		if (!empty($url)) $return[] = array($url, 'url');
		if (!empty($safeFields)) $return[] = array($safeFields,'safe');
		return $return;
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
	        // NOTE: you may need to adjust the relation name and the related
	        // class name for the relations automatically generated below.
	        return array(
	            //'issues' => array(self::HAS_MANY, 'Issue', 'project_id'),
	            //'users' => array(self::MANY_MANY, 'User', 'tbl_project_user_assignment(project_id, user_id)'),
	        );
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
            $fields=$this->getFieldsInfo();
            $labels=array();
            foreach($fields as $field)
            {
				if (!is_array($field)) continue;
					$labels[$field['name']] = $field['label'];
            }
            return $labels;
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($pagectrl=null)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		//$criteria=new CDbCriteria();
		$criteria=$this->getDBCriteria();
		$sattr = array();
		if (!empty($_GET['fnumrows'])) {  // we have advanced search conditions, we process the input for later compare
			for ($nr=1;$nr<=$_GET['fnumrows'];$nr++) {
				if ($_GET['fdelete'.$nr]==1) continue; // this row has been deleted
				if ($_GET['fcol'.$nr]=='' or $_GET['fop'.$nr]=='') continue; // this row is empty
				$value = $_GET['fval'.$nr];
				$orig_value = $value;
				switch ($_GET['fop'.$nr]) {
					case 'e':
						$value = '='.$value;
						break;
					case 'n':
						$value = '<>'.$value;
						break;
					case 's':
						$value = $value.'%';
						break;
					case 'z':
						$value = '%'.$value;
						break;
					case 'c':
						$value = '%'.$value.'%';
						break;
					case 'k':
						$value = '<>%'.$value.'%';
						break;
					case 'l':
						$value = '<'.$value;
						break;
					case 'g':
						$value = '>'.$value;
						break;
					case 'm':
					case 'b':
						$value = '<='.$value;
						break;
					case 'h':
					case 'a':
						$value = '>='.$value;
						break;
					default:
						$value = '';
				}
				$conds = array(
						'original' => $orig_value,
						'value' => $value,
						'glue' => (empty($_GET['fglue'.($nr)]) ? '' : $_GET['fglue'.($nr)]),
						);
				$sattr[$_GET['fcol'.$nr]][] = $conds;
				$this->setAttribute($_GET['fcol'.$nr],$value);
			}
			Yii::app()->session[$this->getModule().'_searchvals']=$this->getAttributesArray();
			Yii::app()->session[$this->getModule().'_searchconds']=$sattr;
		}
		$attrs=$this->getAttributes();
		if (count($sattr)==0 and !empty(Yii::app()->session[$this->getModule().'_searchconds'])) {
			$sattr = Yii::app()->session[$this->getModule().'_searchconds'];
		}
		foreach ($attrs as $key=>$attr) {
			//if (!is_array($attr)) continue;  // we can only search simple values, not IN
			// FIXME: remove attributes that should not be searched.                
			//$criteria->compare($attr['name'],$this->getAttribute($attr['name']),true);
			if($attr!=" ") {
				if (!empty($sattr[$key])) {
					foreach ($sattr[$key] as $cond) {
						if($key == 'assigned_user_id'){
							$cvt = $this->getClientVtiger();
							$allUsers = $cvt->doInvoke('getAllUsers');
							foreach($allUsers as $id => $val){
								$allUsers[$id] = strtolower($val);
							}
							$matchUsers = preg_grep("/.*".strtolower($cond['original']).".*/",$allUsers);
							if(!empty($matchUsers)){
								$usrids = array_keys($matchUsers);
								$condition = "assigned_user_id IN ('".implode("','", $usrids)."')";
							}else{
								$condition = 'FALSE';
							}
							$criteria->addCondition($condition);
						}else{
							$criteria->compare($key,$cond['value'],true,$cond['glue'],false);
						}
					}
				} else {
					if($key == 'assigned_user_id'){
						$cvt = $this->getClientVtiger();
						$allUsers = $cvt->doInvoke('getAllUsers');
						foreach($allUsers as $id => $val){
							$allUsers[$id] = strtolower($val);
						}
						$matchUsers = preg_grep("/.*".strtolower($attr).".*/",$allUsers);
						if(!empty($matchUsers)){
							$usrids = array_keys($matchUsers);
							$condition = "assigned_user_id IN ('".implode("','", $usrids)."')";
						}else{
							$condition = 'FALSE';
						}
						$criteria->addCondition($condition);
					}else{
						$criteria->compare($key,$attr,true);
					}
				}
			}
		}
		$this->setCriteria($criteria);
		if (is_null($pagectrl))
		$pagectrl=array(
			'pageSize'=>Yii::app()->user->settings->get('pageSize'),
		);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'pagination'=>$pagectrl,
			'sort' => array('attributes' => array_keys($this->gridViewColumns())),
		));
	}

	public function GetRelatedRecords($relatedModule,$productDiscriminator='') {
		$cvt = $this->getClientVtiger();
		return $cvt->doGetRelatedRecords($this->getId(), $this->getModule(), $relatedModule, $productDiscriminator);
	}
	/**
	 * Get raw data for all available fields
	 */
	public function getRawDetailViewFields()
	{
		$dvfields=array();
		if (!empty($this->_attributes))
			$fields=$this->_attributes;
		else
			$fields=$this->getFieldsInfo();
		foreach ($fields as $field) {
			if (!is_array($field)) continue;
			$key=$field['name'];
			if($key!=='id') {
				$dvfields[$key]=$this->getAttribute($key);
			}
		}
		return $dvfields;
	}
	/**
	 * Get widget for all available fields
	 * @param array $htmloptionsAllFields array of fieldname => htmloptions for the field layout
	 */
	public function getDetailViewFields($htmloptionsAllFields=array())
	{
		$dvfields=array();
		if (!empty($this->_attributes))
			$fields=$this->_attributes;
		else
			$fields=$this->getFieldsInfo(true);
		foreach ($fields as $field) {
			if (!is_array($field)) continue;
			$key=$field['name'];
			if($key!=='id') {
				$value=$this->getAttribute($key);
				$uitype=intval($field['uitype']);
				$label=$field['label'];
				$sequence=$field['sequence'];
				$block=$field['block']['blockname'];
				$blocksequence=$field['block']['blocksequence'];
				unset($htmloptionsAllFields['dateformat']);
				if(isset($field['type']['name']) && !empty($field['type']['name']) && $field['type']['name']=='date')
					$htmloptionsAllFields['dateformat'] = $field['type']['format'];
				if (isset($field['type']['picklistValues']) && !empty($field['type']['picklistValues'])) {
					foreach ($field['type']['picklistValues'] as $idx => $plvalue) {
						if ($plvalue['value']==$value) {
							$value = $plvalue['label'];
							break;
						}
					}
				}
				$dvfields[$key]=$this->getVtigerViewField($uitype,$key,$value,$label,$sequence,$block,$blocksequence,$htmloptionsAllFields);
			}
		}
		return $dvfields;
	}
	public function getDetailViewFieldsID($id,$htmloptionsAllFields=array())
	{
		$this->findById($id);
		$dvfields=array();
		if (!empty($this->_attributes))
			$fields=$this->_attributes;
		else
			$fields=$this->getFieldsInfo();
		foreach ($fields as $field) {
			if (!is_array($field)) continue;
			$key=$field['name'];
			if($key!=='id') {
				$value=$this->getAttribute($key);
				$uitype=intval($field['uitype']);
				$label=$field['label'];
				$sequence=$field['sequence'];
				$block=$field['block']['blockname'];
				$blocksequence=$field['block']['blocksequence'];
				unset($htmloptionsAllFields['dateformat']);
				if(isset($field['type']['name']) && !empty($field['type']['name']) && $field['type']['name']=='date')
					$htmloptionsAllFields['dateformat'] = $field['type']['format'];
				$dvfields[$key]=$this->getVtigerViewField($uitype,$key,$value,$label,$sequence,$block,$blocksequence,$htmloptionsAllFields);
			}
		}
		return $dvfields;
	}
	
	/**
	 * Depending on the uitype parameter coming from coreBOS, decides what kind of widget to show
	 * @param integer $uitype
	 * @param string $fieldname
	 * @param mixed $fieldvalue
	 * @param array $htmlopts
	 */
	public function getVtigerViewField($uitype,$fieldname,$fieldvalue,$label,$sequence,$block,$blocksequence,$htmlopts=array())
	{
		$widget='';
		switch ($uitype) {
			case 1:
			case 2:
			case 7:
			case 9:
			case 11:
			case 25:
			case 31:
			case 32:
			case 71:
			case 85:
			case 106:
			case 19:
			case 20:
			case 21:
			case 22:
			case 24:
				$widget=array(
				'sequence'=>$sequence,
				'label'=>$label,
				'type'=>$this->convertVtigerUIType2Yii($uitype),
				'value'=>($this->convertVtigerUIType2Yii($uitype) == 'number' && empty($fieldvalue) ? 0 : $fieldvalue),
				'block'=>$block,
				'blocksequence'=>$blocksequence,
				);
				break;
			case 13:
			case 104:
			case 12:
			case 8:
				$widget=array(
				'sequence'=>$sequence,
				'label'=>$label,
				'type'=>$this->convertVtigerUIType2Yii($uitype),
				'value'=>$fieldvalue,
				'block'=>$block,
				'blocksequence'=>$blocksequence,
				);
				break;
			case 18:
			case 17:
				$widget=array(
				'sequence'=>$sequence,
				'label'=>$label,
				'type'=>$this->convertVtigerUIType2Yii($uitype),
				'value'=>$fieldvalue,
				'block'=>$block,
				'blocksequence'=>$blocksequence,
				);
				break;
			case 5:
				if (!isset($htmlopts['dateformat']) or empty($htmlopts['dateformat']))
					$htmlopts['dateformat'] = 'Y-m-d';
				$dateformat=str_replace('yyyy', 'Y', $htmlopts['dateformat']);
				$dateformat=str_replace('mm', 'm', $dateformat);
				$dateformat=str_replace('dd', 'd', $dateformat);
				unset($htmlopts['dateformat']);
				$widget=array(
						'sequence'=>$sequence,
						'label'=>$label,
						'type'=>$this->convertVtigerUIType2Yii($uitype),
						'value'=>(empty($fieldvalue) ? '' : date($dateformat,strtotime($fieldvalue))),
						'block'=>$block,
						'blocksequence'=>$blocksequence,
				);
				break;
			case 23:
			case 6:
			case 70:
				if (!isset($htmlopts['dateformat']) or empty($htmlopts['dateformat']))
					$htmlopts['dateformat'] = 'Y-m-d';
				$dateformat=str_replace('yyyy', 'Y', $htmlopts['dateformat']);
				$dateformat=str_replace('mm', 'm', $dateformat);
				$dateformat=str_replace('dd', 'd', $dateformat);
				$dateformat.=' h:i:s';
				unset($htmlopts['dateformat']);
				$widget=array(
						'sequence'=>$sequence,
						'label'=>$label,
						'type'=>$this->convertVtigerUIType2Yii($uitype),
						'value'=>(empty($fieldvalue) ? '' : date($dateformat,strtotime($fieldvalue))),
						'block'=>$block,
						'blocksequence'=>$blocksequence,
				);
				break;
			case 10:
			case 51:
			case 57:
			case 58:
			case 59:
			case 62:
			case 66:
			case 67:
			case 68:
			case 73:
			case 75:
			case 76:
			case 78:
			case 79:
			case 80:
			case 81:
			case 101:
				$widget=array(
				'sequence'=>$sequence,
				'label'=>$label,
				'type'=>$this->convertVtigerUIType2Yii($uitype),
				'value'=>$fieldvalue,
				'block'=>$block,
				'blocksequence'=>$blocksequence,
				);
				break;
			case 15:
			case 16:
			case 55:
			case 111:
			case 115:
			case 255:
				$widget=array(
				'sequence'=>$sequence,
				'label'=>$label,
				'type'=>$this->convertVtigerUIType2Yii($uitype),
				'value'=>$fieldvalue,
				'block'=>$block,
				'blocksequence'=>$blocksequence,
				);
				break;
			case 33:
			case 34:
				$widget=array(
				'sequence'=>$sequence,
				'label'=>$label,
				'type'=>$this->convertVtigerUIType2Yii($uitype),
				'value'=>$fieldvalue,
				'block'=>$block,
				'blocksequence'=>$blocksequence,
				);
				break;
			case 27:
				$widget=array(
				'sequence'=>$sequence,
				'label'=>$label,
				'value'=>($fieldvalue=='I'?yii::t('core', 'Internal'):yii::t('core', 'External')).
						'<span id="doc_dld_type" style="display:none" dld_type="'.$fieldvalue.'"></span>',
				'type'=>'raw',
				'block'=>$block,
				'blocksequence'=>$blocksequence,
				);
				break;
 			case 28:
//			case 29:
//			case 61:
			case 69:
                                $id=$this->getId();
                                $attr=$this->getAttributesArray();
                                if (!empty($attr['filetype']) and !empty($attr['filelocationtype']) and $attr['filelocationtype']=='I') {
                                	if (stripos($attr['filename'],'filedownload.download')) {
                                		$value=$attr['filename'];
                                	} else {
                                		$value='<a href=\'javascript: filedownload.download("'.yii::app()->baseUrl.'/index.php/vtentity/'.$this->getModule().'/download/'.$id.'?fn='.CHtml::encode($attachmentsdata[$id]['filename']).'&ft='.CHtml::encode($attachmentsdata[$id]['filetype']).'","")\'>'.CHtml::encode($attachmentsdata[$id]['filename'])."</a>";
                                	}
                                } else {
                                	$value=(empty($attachmentsdata[$id]['filename']) ? yii::t('core', 'none') : CHtml::encode($attachmentsdata[$id]['filename']));
                                }
                                $widget=array(
				'sequence'=>$sequence,
				'label'=>$label,
				'type'=>'raw',
				'value'=>$value,
				'block'=>$block,
				'blocksequence'=>$blocksequence,
				);
				break;
            case 26:
			case 52:
			case 53:
			case 54:
				$widget=array(
				'sequence'=>$sequence,
				'label'=>$label,
				'type'=>$this->convertVtigerUIType2Yii($uitype),
				'value'=>$fieldvalue,
				'block'=>$block,
				'blocksequence'=>$blocksequence,
				);
				break;
			case 56:
				$widget=array(
				'sequence'=>$sequence,
				'label'=>$label,
				'type'=>'raw',
				'value'=>CHtml::image(ICONPATH.'/16/'.($fieldvalue ? 'square_green.png' : 'square_red.png')),
				'block'=>$block,
				'blocksequence'=>$blocksequence,
				);
				break;
			case 'file':
				//CHtml::resolveNameID($model,$fieldname,$htmlopts);
				//$widget = CHtml::activeInputField('file', $model, $fieldname, $htmlopts);
				break;
			default:
				$widget=array(
						'sequence'=>$sequence,
						'label'=>$label,
						'value'=>$fieldvalue,
						'block'=>$block,
						'blocksequence'=>$blocksequence,
				);
		}
		return $widget;
	}

	public function convertVtigerUIType2Yii($uitype) {
		$yiiformat='raw';
		switch ($uitype) {
			case 1:
			case 2:
			case 11:
			case 85:
			case 15:
			case 16:
			case 55:
			case 111:
			case 115:
			case 255:
			case 33:
			case 34:
			case 26:
			case 52:
			case 53:
			case 54:
				$yiiformat='text';
				break;
			case 7:
			case 9:
			case 25:
			case 71:
				$yiiformat='number';
				break;
			case 31:
			case 32:
			case 106:
			case 19:
			case 20:
			case 21:
			case 22:
			case 24:
				$yiiformat='ntext';
				break;
			case 13:
			case 104:
			case 12:
				$yiiformat='email';
				break;
			case 18:
			case 17:
				$yiiformat='url';
				break;
			case 5:
				$yiiformat='text';  // date
				break;
			case 23:
			case 6:
			case 70:
				$yiiformat='text';  // datetime
				break;
			case 56:
				$yiiformat='boolean';
				break;
			case 10:
			case 51:
			case 57:
			case 58:
			case 59:
			case 62:
			case 66:
			case 67:
			case 68:
			case 73:
			case 75:
			case 76:
			case 78:
			case 79:
			case 80:
			case 81:
			case 101:
			case 27:
			case 28:
			case 69:
			default:
				$yiiformat='raw';
		}
		return $yiiformat;
	}

	function getViewLinkField($fieldname) {
		$currentPage = Yii::app()->getRequest()->getParam('Vtentity_page',1)-1;
		$pageSize=Yii::app()->user->settings->get('pageSize');
		$gridOffset="($currentPage*$pageSize)+1";
		return 'CHtml::link(CHtml::encode($data["'.$fieldname.'"]),"#vtentity/'.$this->getModule().'/list/".$data["id"]."/dvcpage/".('.$gridOffset.'+$row-1))';
	}

	public function getRelationInformation()
	{
		// see also VTActiveResource::defaultScope()
		if (Yii::app()->vtyiicpngScope=='vtigerCRM') {
			return array();
		} else {
			switch ($this->getModule()) {
				case 'Contacts':
					$condition = array('account_id'=>Yii::app()->user->accountId);
					break;
				case 'Accounts':
					$condition = array('id'=>Yii::app()->user->accountId);
					break;
				case 'Quotes':
					$condition = array(
						'account_id'=>Yii::app()->user->accountId,
						'contact_id'=>Yii::app()->user->contactId,
					);
					break;
				case 'SalesOrder':
					$condition = array(
						'account_id'=>Yii::app()->user->accountId,
						'contact_id'=>Yii::app()->user->contactId
					);
					break;
				case 'ServiceContracts':
					$condition = array(
						'sc_related_to'=>Yii::app()->user->contactId
					);
					break;
				case 'Invoice':
					$condition = array(
						'account_id'=>Yii::app()->user->accountId,
						'contact_id'=>Yii::app()->user->contactId
					);
					break;
				case 'HelpDesk':
					$condition = array(
						'parent_id'=>Yii::app()->user->contactId
					);
					break;
				case 'Assets':
					$condition = array('account'=>Yii::app()->user->accountId);
					break;
				case 'Project':
					$condition = array('linktoaccountscontacts'=>Yii::app()->user->contactId);
					break;
				case 'Products':
					$condition = array('relations'=>Yii::app()->user->contactId);
					break;
				case 'Services':
					$condition = array('relations'=>Yii::app()->user->contactId);
					break;
				case 'Documents':
					$condition = array('relations'=>array(Yii::app()->user->contactId,Yii::app()->user->accountId));
					break;
				default:
					$condition = array();
			}
			return $condition;
		}
	}

	public function exportRecords($printit=TRUE) {
		$this->unsetAttributes();
		$this->setScenario('search');
		if(isset($_GET[$this->modelName])) {
			$this->setAttributes($_GET[$this->modelName]);
		} elseif (isset(Yii::app()->session[$this->getModule().'_searchvals'])) {
			$this->setAttributes(Yii::app()->session[$this->getModule().'_searchvals']);
		}
		if(isset(Yii::app()->session['doctype'])){
			$this->doctype(Yii::app()->session['doctype']);
		}
		$this->search();
		if (!empty($this->_attributes))
			$fields=$this->_attributes;
		else
			$fields=$this->getFieldsInfo();
		$header = '';
		$fieldnames = array();
		foreach ($fields as $field) {
			if (!is_array($field)) continue;
			$key=$field['name'];
			if($key!=='id') {
				$header.=$field['label'].',';
				$fieldnames[]=$key;
			}
		}
		if (!$printit) ob_start();
		$header = trim($header,',');
		echo "$header\n";
		$criteria=new CDbCriteria;
		$cnt = $this->count($criteria);
		$criteria=new CDbCriteria;
		$criteria->order = 'createdtime';
		$criteria->limit = 100;
		for ($numrec=0;$numrec<=$cnt;$numrec+=100) {
			$criteria->offset = $numrec;
			$recs = $this->query($criteria,true);
			for ($nr=0;$nr<count($recs);$nr++) {
				$row = '';
				$recs[$nr]=$this->dereferenceIds($recs[$nr],false);
				$attrs = $recs[$nr]->getAttributes(true);
				foreach ($fieldnames as $fld) {
					$row.='"'.$attrs[$fld].'",';
				}
				echo trim($row,',')."\n";
			}
		}
		if (!$printit) {
			$ret = ob_get_clean();
			ob_end_clean();
		} else {
			$ret = '';
		}
		return $ret;
	}
	
	//get documents folders
	public function getDocumentFolders() {
			$response = new AjaxResponse();
			
			$vtModule = $this->getModule();
			$clientvtiger=$this->getClientVtiger();
			$folders = array();
			if(!$clientvtiger) Yii::log('login failed');
			else {
				$criteria = $this->getDbCriteria();
				$this->setCriteria($criteria);
				$entityArray = $this->getData('folderid');
				$wsEntityId = $clientvtiger->doInvoke('vtyiicpng_getWSEntityId',array('entityName'=>'DocumentFolders'));
				$folderids = array();
				foreach($entityArray as $entityRecord)
				{
					if(!in_array($entityRecord['folderid'], $folderids))
					{
						$foldername = $clientvtiger->doQuery("Select foldername,description from documentfolders Where id = ".$wsEntityId.$entityRecord['folderid']);

						$folders[]= array('folderid'=>$entityRecord['folderid'],'foldername'=>$foldername[0]['foldername'],'description'=>$foldername[0]['description']);
						$folderids[] = $entityRecord['folderid'];
						
					}
				}
			}
			return $folders;
		}	
}