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