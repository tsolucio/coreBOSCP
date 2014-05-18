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
			<td align="right">'.Formatter::fileSize($helpdeskd['filesize']).'</td>
			<td align="right">'.$helpdeskd['filedownloadcount'].'</td>
			<td align="right">'.$helpdeskd['modifiedtime'].'</td>
			</tr>';
			$i++;
		} ?>
	</tbody>
</table>