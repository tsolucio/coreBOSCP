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