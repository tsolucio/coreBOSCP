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
			case 'pay':
				$mod = $data->getModule();
				$ret = $moduleAccessInformation[$mod]['retrieveable'] && $mod == 'CobroPago' && $data->getAttribute('paid')==0;
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