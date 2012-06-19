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
class vtyiicpngButtonColumn extends CButtonColumn
{
	/**
	 * Registers the client scripts for the button column.
	 */
	protected function registerClientScript()
	{
		$js=array();
		foreach($this->buttons as $id=>$button)
		{
			if(isset($button['click']))
			{
				$function=CJavaScript::encode($button['click']);
				$class=preg_replace('/\s+/','.',$button['options']['class']);
				$js[]="if(jQuery('body').attr('{$this->grid->id}deleteEventIsset') !== 'yes'){jQuery('body').attr('{$this->grid->id}deleteEventIsset', 'yes');jQuery('#{$this->grid->id} a.{$class}').off('click').on('click',$function);}";
			}
		}
	
		if($js!==array())
			Yii::app()->getClientScript()->registerScript(__CLASS__.'#'.$this->id, implode("\n",$js));
	}

	protected function checkButtons($data, $id) {
		$moduleAccessInformation = Yii::app()->cache->get('moduleAccessInformation');
		switch ($id) {
			case 'delete':
				$ret = $moduleAccessInformation[$data->getModule()]['deleteable'];
				break;
			case 'update':
				$ret = $moduleAccessInformation[$data->getModule()]['updateable'];
				break;
			case 'view':
				$ret = $moduleAccessInformation[$data->getModule()]['retrieveable'];
				break;
			case 'dlpdf':
				$mod = $data->getModule();
				$ret = $moduleAccessInformation[$mod]['retrieveable'] && in_array($mod,array('Invoice','Quotes','SalesOrder','PurchaseOrder'));
				break;
			default:
				$ret = true;
				break;
		}
		return $ret;
	}
	
	protected function renderDataCellContent($row, $data) {
		$tr = array();
		ob_start();
		foreach ($this->buttons as $id => $button) {
			if ($this->checkButtons($data, $id)) {
				$this->renderButton($id, $button, $row, $data);
			}
			$tr['{' . $id . '}'] = ob_get_contents();
			ob_clean();
		}
		ob_end_clean();
		echo strtr($this->template, $tr);
	}
}