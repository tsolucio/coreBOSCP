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
<h2><?php echo Yii::t('core', 'welcome2CP'); ?>!</h2>
<h3><?php echo Yii::t('core', 'loggedInAs'); ?> <?php echo Yii::app()->user->name; ?>.</h3>
<?php if (!empty($missingFunctions)) {
	echo "<div class='errorSummary'>$missingFunctions</div>"; 
}?>
<table class="list" style="float: left; width: 84%;">
	<colgroup>
		<col style="width: 200px;"></col>
		<col></col>
	</colgroup>
	<thead>
		<tr>
			<th colspan="2"><?php echo Yii::t('core', 'thankYou4Connect'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td align="center"><img src="<?php echo BASEURL; ?>/images/connect.gif"></td>
			<td align="center">
		<table class="list">
			<colgroup>
				<col style="width: 50%" />
				<col />
			</colgroup>
			<thead>
				<tr>
					<th colspan="2"><?php echo Yii::t('core', 'yourAccessInfo'); ?></th>
				</tr>
			</thead>
			<tbody>
	   <tr>
		<td align="right"><?php echo Yii::t('core', 'lastAccessInfo'); ?></td>
		<td><b><?php echo date('Y-m-d H:i',Yii::app()->user->lastLoginTime); ?></b></td>
	   </tr>
	   <tr>
		<td align="right"><?php echo Yii::t('core', 'startAccessDate'); ?></td>
		<td><b><?php echo Yii::app()->user->startSupportDate; ?></b></td>
	   </tr>
	   <tr>
		<td align="right"><?php echo Yii::t('core', 'endAccessDate'); ?></td>
		<td><b><?php echo Yii::app()->user->endSupportDate; ?></b></td>
	   </tr>
			</tbody>
		</table>
			</td>
		</tr>
	</tbody>
</table>
