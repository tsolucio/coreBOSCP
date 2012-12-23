<?php
/**************************************************************************************************
 * vtigerCRM vtyiiCPng - web based vtiger CRM Customer Portal
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
class TabMenu extends CWidget
{
	public $items=array();

	public function run()
	{

		$items=array();

		$controller=$this->controller;
		$action=$controller->action;

		foreach($this->items as $item)
		{

			if(isset($item['visible']) && !$item['visible'])
				continue;

			$item2=array();
			$item2['label']=$item['label'];

			$item2['htmlOptions'] = isset($item['htmlOptions']) ? $item['htmlOptions'] : array();

			$item2['a']['htmlOptions'] = array();
			$item2['a']['htmlOptions'] = $item['link']['htmlOptions'];


			if(isset($item['icon']))
			{
				$item2['icon']=$item['icon'];

				if(isset($item['htmlOptions']['class']))
				{
					$item2['a']['htmlOptions']['class'] .= ' icon';
				}
				else
				{
					$item2['a']['htmlOptions']['class'] = 'icon';
				}
			}

			$item2['icon'] = isset($item['icon']) ? $item['icon'] : null;
			$item2['a']['href'] = $item['link']['url'];

			if($this->isActive($item['link']['url'], $action->id))
			{
				if(isset($item['htmlOptions']['class']))
				{
					$item2['htmlOptions']['class'] .= ' active';
				}
				else
				{
					$item2['htmlOptions']['class'] = 'active';
				}
			}


			$items[]=$item2;
		}

		$this->render('tabMenu',array('items'=>$items));
	}

	protected function isActive($url,$action)
	{
		if(preg_match('/'.$action.'$/i', $url, $res))
		{
			return (bool)$res[0];
		}
		else
		{
			return false;
		}
	}
}