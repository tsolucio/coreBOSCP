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
<table class="list">
	<colgroup>
		<col style="width: 15%;"></col>
		<col style="width: 34%;"></col>
		<col style="width: 18%;"></col>
		<col style="width: 10%;"></col>
		<col style="width: 8%;"></col>
		<col style="width: 15%;"></col>
	</colgroup>
	<thead>
		<tr>
			<th colspan="6"><?php echo Yii::t('core','attachments'); ?></th>
		</tr>
		<tr>
			<th><?php echo Yii::t('core','reference'); ?></th>
			<th><?php echo Yii::t('core','description'); ?></th>
			<th><?php echo Yii::t('core','name'); ?></th>
			<th><?php echo Yii::t('core','size'); ?></th>
			<th><?php echo Yii::t('core','downloads'); ?></th>
			<th><?php echo Yii::t('core','modifiedtime'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$docs = Vtentity::model();
		$ids='';
		foreach ($relDocs as $helpdeskd) { $ids.=$helpdeskd['id'].','; }
		$all_attachments=$docs->getDocumentAttachment(trim($ids,','),false);
		$i=0;
		foreach ($relDocs as $helpdeskd) {
			$idatt=$helpdeskd['id'];
			if (!empty($all_attachments[$idatt]['filetype'])) {
				$value='<a href=\'javascript: filedownload.download("'.yii::app()->baseUrl.'/index.php/vtentity/Documents/download/'.$idatt.'?fn='.CHtml::encode($all_attachments[$idatt]['filename']).'&ft='.CHtml::encode($all_attachments[$idatt]['filetype']).'","")\'>'.CHtml::encode($all_attachments[$idatt]['filename'])."</a>";
			} else {
				$fname = (empty($all_attachments[$idatt]['filename']) ? yii::t('core', 'none') : $all_attachments[$idatt]['filename']);
				$value=CHtml::encode($fname);
			}
			echo '<tr class="'.($i % 2 == 0 ? 'even' : 'odd').'">
			<td><b>'.CHtml::link($helpdeskd['title'],"index.php#vtentity/Documents/view/$idatt").'</b></td>
			<td>'.$helpdeskd['notecontent'].'</td>
			<td>'.$value.'</td>
			<td align="right">'.round($helpdeskd['filesize']/1024,($helpdeskd['filesize']<1024 ? 2 : 0)).'Kb</td>
			<td align="right">'.$helpdeskd['filedownloadcount'].'</td>
			<td align="right">'.$helpdeskd['modifiedtime'].'</td>
			</tr>';
			$i++;
		} ?>
	</tbody>
</table>