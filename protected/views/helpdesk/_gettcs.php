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
?>
<div class="helpdesk_comments" id="helpdesk_tcs">
<table class="list">
	<colgroup>
		<col style="width: 15%;"></col>
		<col style="width: 26%;"></col>
		<col style="width: 18%;"></col>
		<col style="width: 18%;"></col>
		<col style="width: 8%;"></col>
		<col style="width: 15%;"></col>
	</colgroup>
	<thead>
		<tr>
			<th colspan="6"><?php
			 echo $TCName;
			 $moduleAccessInformation = Yii::app()->cache->get('moduleAccessInformation');
			 if (is_array($moduleAccessInformation) and isset($moduleAccessInformation['Timecontrol']) and $moduleAccessInformation['Timecontrol']['updateable']) {
				 echo Html::ajaxLink('vtentity/Timecontrol/create?preload[relatedto]='.$hdid, array('class' => 'button icon','style'=>'float:right;'));
				 echo Html::icon('add');
				 echo '<span>'.Yii::t('core','insert').'</span></a>';
			 } ?>
			</th>
		</tr>
		<tr>
			<th><?php echo Yii::t('core','tcnumber'); ?></th>
			<th><?php echo Yii::t('core','reference'); ?></th>
			<th><?php echo Yii::t('core','start'); ?></th>
			<th><?php echo Yii::t('core','end'); ?></th>
			<th align="center"><?php echo Yii::t('core','total'); ?></th>
			<th><?php echo Yii::t('core','product'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$i=0;
		foreach ($relTCs as $helpdesktc) {
			echo '<tr class="'.($i % 2 == 0 ? 'even' : 'odd').'">
			<td><b>'.CHtml::link($helpdesktc['timecontrolnr'],'index.php#vtentity/Timecontrol/view/'.$helpdesktc['id']).'</b></td>
			<td>'.CHtml::link($helpdesktc['title'],'index.php#vtentity/Timecontrol/view/'.$helpdesktc['id']).'</td>
			<td>'.$helpdesktc['date_start'].' '.substr($helpdesktc['time_start'],0,5).'</td>
			<td>'.$helpdesktc['date_end'].' '.substr($helpdesktc['time_end'],0,5).'</td>
			<td align="right">'.$helpdesktc['totaltime'].'</td>
			<td>'.$helpdesktc['product_id'].'</td>
			</tr>';
			$i++;
		} ?>
	</tbody>
</table>
</div>