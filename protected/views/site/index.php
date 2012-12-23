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
