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

class cpngActiveForm extends CActiveForm
{
	/**
	 * Depending on the uitype parameter coming from vtiger CRM, decides what kind of widget to show
	 * @param integer $uitype
	 * @param class $model
	 * @param string $fieldname
	 * @param array $htmlopts
	 */
	public function getVtigerEditField($uitype,$model,$fieldname,$htmlopts=array(),$capturerefersto)
	{
		$widget='';
		switch ($uitype) {
			case 1:
			case 2:
			case 7:
			case 8:
			case 9:
			case 11:
			case 12:
			case 13:
			case 17:
			case 18:
			case 25:
			case 31:
			case 32:
			case 71:
			case 85:
			case 104:
			case 106:
				$widget=$this->textField($model,$fieldname,$htmlopts);
				break;
			case 5:
			case 23:
				// FIXME  get date format from portal user config or setup contact's preferences?
				$dateformat='yy-mm-dd';
				$htmlopts['size']=10;
				$htmlopts['maxlength']=10;
				if (empty($htmlopts['id'])) {
					CHtml::resolveNameID($model,$fieldname,$htmlopts);
				}
				$widget=$this->textField($model,$fieldname,$htmlopts);
				$widget.='<script type="text/javascript">
						$(document).ready(function() {
							$("#' . $htmlopts['id'] . '").datepicker({showOn: "button", dateFormat: "'.$dateformat.'", buttonImage: "' . ICONPATH . '/16/calendar.png' . '", buttonImageOnly: true, buttonText: "' . Yii::t('core', 'showCalendar') . '"});
						});
						</script>';
				break;
			case 6:
			case 70:
				// FIXME  get date format from portal user config or setup contact's preferences?
				$dateformat='Y-m-d';
				$htmlopts['size']=19;
				$htmlopts['maxlength']=19;
				if (empty($htmlopts['id'])) {
					CHtml::resolveNameID($model,$fieldname,$htmlopts);
				}
				$widget=$this->textField($model,$fieldname,$htmlopts);
				$widget.='<script type="text/javascript">
						$(document).ready(function() {
							$("#' . $htmlopts['id'] . '").datepicker({showOn: "button", dateFormat: "'.$dateformat.' " + (new Date()).getHours() + ":" + (new Date()).getMinutes()+ ":" + (new Date()).getSeconds(), buttonImage: "' . ICONPATH . '/16/calendar.png' . '", buttonImageOnly: true, buttonText: "' . Yii::t('core', 'showCalendar') . '"});
						});
						</script>';
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
				$widget='';
				$cname=get_class($model);
				echo CHtml::hiddenField($cname."[$fieldname]");
				if (!empty($capturerefersto) and is_array($capturerefersto)) {
					echo CHtml::hiddenField($cname.$fieldname."_type",$capturerefersto[0]);
					if (count($capturerefersto)>1) {
						$translation=false;
						if(($cache=Yii::app()->cache)!==null) {
							if (($modname=$cache->get('yiicpng.sidebar.listmodules'))!==false) {
								$translation=true;
							}
						}
						$pl10values=array();
						foreach ($capturerefersto as $modulename) {
							$pl10values[$modulename]=($translation ? $modname[$modulename] : $modulename);
						}
						echo $this->dropDownList($model,$cname.$fieldname."_select",$pl10values,array(
						  'onchange'=>"jQuery('#".$cname.$fieldname."_display').val('');jQuery('#".$cname.'_'.$fieldname."').val('');jQuery('#".$cname.$fieldname."_type').val(jQuery('#".$cname.'_'.$cname.$fieldname."_select').val());",
						)).'&nbsp;';
					}
				} else { // this has to be an error, but we will setup the type field to avoid JS error
					echo CHtml::hiddenField($cname.$fieldname."_type");
				}
				$htmlopts['onClick']="if (jQuery('#".$cname.$fieldname."_display').val()=='".Yii::t('core', 'search')."') jQuery('#".$cname.$fieldname."_display').val('');";
				$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'model'=>$model,
					'name'=>$cname.$fieldname."_display",
					'value'=>Yii::t('core', 'search'),
					'source'=>'js:function(request, response){ // el truco es reescribir source
						     $.getJSON("'.Yii::app()->createUrl('entity').'"+"/"+$("#'.$cname.$fieldname."_type".'").val()+"/AutoCompleteLookup",
						     {
						       term: request.term,
						     }, 
						     response);
						  }',
					'options'=>array(
						'minLength'=>2,
						'showAnim' => 'fold',
						'delay'=>500, //number of milliseconds before lookup occurs
						'matchCase'=>false, //match case when performing a lookup?
						'select' => 'js:function(event, ui){ jQuery("#'.$cname.'_'.$fieldname.'").val(ui.item["id"]); }'
					),
					'htmlOptions'=>$htmlopts,
				));
				break;
			case 15:
			case 16:
			case 55:
			case 111:
			case 115:
			case 255:
				$values=array_values((is_array(Entity::getPicklistValues($fieldname))?Entity::getPicklistValues($fieldname):array()));
                                $plvalues=count($values)>0?array_combine($values,$values):array();
				$widget=$this->dropDownList($model,$fieldname,$plvalues,$htmlopts);
				break;
			case 33:
			case 34:				
                                $plvalues=array_values(Entity::getPicklistValues($fieldname));
				$htmlopts['multiple']='true';
				$widget=$this->listBox($model,$fieldname,$plvalues,$htmlopts);
				break;
			case 19:
			case 20:
			case 21:
			case 22:
			case 24:
				unset($htmlopts['size']);
				unset($htmlopts['maxlength']);
				$widget=$this->textArea($model,$fieldname,$htmlopts);
				break;
			case 26:
			case 27:
			case 28:
			case 29:
			case 61:
			case 69:
				// Document fields, don't know how to treat these yet
				break;
			case 56:
				$widget=$this->checkBox($model,$fieldname,$htmlopts);
				break;
			case 'file':
				$widget=CHtml::activeFileField($model, $fieldname, $htmlopts);
				break;
                        case 53:
                                $values=Entity::getUsersInSameGroup();
                                $plvalues=$values;
				$widget=$this->dropDownList($model,$fieldname,$plvalues,$htmlopts);
				break;

			default:
				$widget=$this->textField($model,$fieldname,$htmlopts);
		}
		return $widget;
	}       
}