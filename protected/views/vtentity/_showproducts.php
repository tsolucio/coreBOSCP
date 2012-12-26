<?php /*************************************************************************************************
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
 *************************************************************************************************/?>
<br/>
<table class="list">
	<colgroup>
		<col style="width: 15%;"></col>
		<col style="width: 25%;"></col>
		<col style="width: 15%;"></col>
		<col style="width: 15%;"></col>
		<col style="width: 15%;"></col>
		<col style="width: 15%;"></col>
	</colgroup>
	<thead>
		<tr>
			<th colspan="6"><?php echo Yii::t('core','product'); ?></th>
		</tr>
		<tr>
			<th><?php echo Yii::t('core','product'); ?></th>
			<th><?php echo Yii::t('core','description'); ?></th>
			<th style="text-align:right"><?php echo Yii::t('core','quantity'); ?></th>
			<th style="text-align:right"><?php echo Yii::t('core','listprice'); ?></th>
			<th style="text-align:right"><?php echo Yii::t('core','discount'); ?></th>
			<th style="text-align:right"><?php echo Yii::t('core','impuestos'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
		$i=0;
		foreach ($relProducts as $pdoline) {
			$taxes = array();
			if (!empty($pdoline['tax1'])) $taxes[]=$pdoline['tax1'];
			if (!empty($pdoline['tax2'])) $taxes[]=$pdoline['tax2'];
			if (!empty($pdoline['tax3'])) $taxes[]=$pdoline['tax3'];
			echo '<tr class="'.($i % 2 == 0 ? 'even' : 'odd').'">
			<td><b>'.$pdoline['productid'].'</b></td>
			<td>'.$pdoline['comment'].'</td>
			<td align="right">'.number_format($pdoline['quantity'],2).'</td>
			<td align="right">'.number_format($pdoline['listprice'],2).'</td>
			<td align="right">'.(empty($pdoline['discount_percent']) ? $pdoline['discount_amount'] : $pdoline['discount_percent']).'</td>
			<td align="right">'.implode(',',$taxes).'</td>
			</tr>';
			$i++;
		} ?>
	</tbody>
</table>