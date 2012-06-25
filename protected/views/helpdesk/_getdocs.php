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
			<td><b>'.$helpdeskd['title'].'</b></td>
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