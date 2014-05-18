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