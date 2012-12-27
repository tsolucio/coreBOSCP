<?php /*************************************************************************************************
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
 *************************************************************************************************/?>
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
			 echo Html::ajaxLink('vtentity/Timecontrol/create?preload[relatedto]='.$hdid, array('class' => 'icon','style'=>'float:right;'));
			 echo Html::icon('add');
			 echo '<span>'.Yii::t('core','insert').'</span></a>';?></th>
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