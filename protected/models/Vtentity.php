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
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return $this->tablename;
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
			foreach($fields as $pos=>$fielddetails){
				if ($fielddetails['name']==$lvfield) {
					break;
				}
			}
			$field=$fields[$pos];
			$key=$field['name'];
			$label=$field['label'];
			if (in_array($key,explode(',',$this->getLookupField()))
			 or (strpos($key,'title') and in_array($module,array('HelpDesk','Documents'))))
				$fieldlabels[$key]=array('name'=>$key,'header'=>$label,'type'=>'html','value'=>$this->getViewLinkField($key));
			else
			$fieldlabels[$key]=array('name'=>$key,'header'=>$label,'type'=>$this->convertVtigerUIType2Yii($field['uitype']));
		}
		return $fieldlabels;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that will receive user inputs.
		$module = $this->getModule();
		$mandatory=$this->getMandatoryFields($module);
		$numerical=$this->getNumericalFields($module);
		$email=$this->getEmailFields($module);
                $writable=$this->getWritableFieldsArray();
                $attributes=array();
                foreach($writable as $write){
                    if (!is_array($write)) continue;
                    array_push($attributes,$write['name']);
                }
                $safeFields=implode(',',$attributes);
		return array(
				array($mandatory, 'required'),
				array($numerical, 'numerical', 'integerOnly'=>true),
				array($email, 'email'),
                                array($safeFields,'safe'),
		);
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
    		array_push($labels,$field['label']);
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
		$criteria=new CDbCriteria;
		$attrs=$this->getAttributes();
		foreach ($attrs as $key=>$attr) {
			//if (!is_array($attr)) continue;  // we can only search simple values, not IN
			// FIXME: remove attributes that should not be searched.                
//			$criteria->compare($attr['name'],$this->getAttribute($attr['name']),true);
                if($attr!=" ")
 		$criteria->compare($key,$attr,true);
		}
               // Yii::log(implode(',',$pagectrl));
		if (is_null($pagectrl))
		$pagectrl=array(
			'pageSize'=>Yii::app()->user->settings->get('pageSize'),
		);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'pagination'=>$pagectrl,
		));
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
			$fields=$this->getFieldsInfo();
		foreach ($fields as $field) {
			if (!is_array($field)) continue;
                       $key=$field['name'];
			if($key!=='id') {                            
				$value=$this->getAttribute($key);
				$uitype=intval($field['uitype']);
				$label=$field['label'];
				$dvfields[$key]=$this->getVtigerViewField($uitype,$key,$value,$label,$htmloptionsAllFields);
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
				$dvfields[$key]=$this->getVtigerViewField($uitype,$key,$value,$label,$htmloptionsAllFields);
			}
		}
		return $dvfields;
	}
	
	/**
	 * Depending on the uitype parameter coming from vtiger CRM, decides what kind of widget to show
	 * @param integer $uitype
	 * @param string $fieldname
	 * @param mixed $fieldvalue
	 * @param array $htmlopts
	 */
	public function getVtigerViewField($uitype,$fieldname,$fieldvalue,$label,$htmlopts=array())
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
				'label'=>$label,
				'type'=>$this->convertVtigerUIType2Yii($uitype),
				'value'=>$fieldvalue,
				);
				break;
			case 13:
			case 104:
			case 12:
			case 8:
				$widget=array(
				'label'=>$label,
				'type'=>$this->convertVtigerUIType2Yii($uitype),
				'value'=>$fieldvalue,
				);
				break;
			case 18:
			case 17:
				$widget=array(
				'label'=>$label,
				'type'=>$this->convertVtigerUIType2Yii($uitype),
				'value'=>$fieldvalue,
				);
				break;
			case 5:
				$dateformat='Y-m-d';
				$widget=array(
						'label'=>$label,
						'value'=>date($dateformat,strtotime($fieldvalue)),
				);
				break;
			case 23:
			case 6:
			case 70:
				// FIXME  get date format from portal user config or setup contact's preferences?
				$dateformat='Y-m-d h:i:s';
				$widget=array(
						'label'=>$label,
						'value'=>date($dateformat,strtotime($fieldvalue)),
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
				'label'=>$label,
				'type'=>$this->convertVtigerUIType2Yii($uitype),
				'value'=>$fieldvalue,
				);
				break;
			case 15:
			case 16:
			case 55:
			case 111:
			case 115:
			case 255:
				$widget=array(
				'label'=>$label,
				'type'=>$this->convertVtigerUIType2Yii($uitype),
				'value'=>$fieldvalue,
				);
				break;
			case 33:
			case 34:
				$widget=array(
				'label'=>$label,
				'type'=>$this->convertVtigerUIType2Yii($uitype),
				'value'=>$fieldvalue,
				);
				break;

                        case 27:
                               $widget=array(
				'label'=>$label,
				'value'=>($fieldvalue=='I'?yii::t('core', 'Internal'):yii::t('core', 'External')),
				);
                               break;
 			case 28:
//			case 29:
//			case 61:
			case 69:
                                $id=$this->getId();
                                $attachmentsdata=$this->getDocumentAttachment($id);
                                if (!empty($attachmentsdata[$id]['filetype'])) {
                                	$this->writeAttachment2Cache($id,$attachmentsdata[$id]['attachment']);
                                	$value='<a href=\'javascript: filedownload.download("'.yii::app()->baseUrl.'/index.php/vtentity/'.$this->getModule().'/download/'.$id.'?fn='.CHtml::encode($attachmentsdata[$id]['filename']).'&ft='.CHtml::encode($attachmentsdata[$id]['filetype']).'","")\'>'.CHtml::encode($attachmentsdata[$id]['filename'])."</a>";
                                } else {
                                	$value=CHtml::encode($attachmentsdata[$id]['filename']);
                                }
                                $widget=array(
				'label'=>$label,
				'type'=>'raw',
				'value'=>$value,
				);			
				break;
                        case 26:
			case 52:
			case 53:
			case 54:
				$widget=array(
				'label'=>$label,
				'type'=>$this->convertVtigerUIType2Yii($uitype),
				'value'=>$fieldvalue,
				);
				break;
			case 56:
				$widget=array(
				'label'=>$label,
				'type'=>'raw',
				'value'=>CHtml::image(ICONPATH.'/16/'.($fieldvalue ? 'square_green.png' : 'square_red.png')),
				);
				break;
			case 'file':
				//$widget=CHtml::activeFileField($model, $fieldname, $htmlopts);
				break;
			default:
				$widget=array(
						'label'=>$label,
						'value'=>$fieldvalue,
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

}