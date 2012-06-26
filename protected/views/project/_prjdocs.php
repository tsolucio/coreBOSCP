<table class="list">
<colgroup>
<col style="width: 30%;"></col>
<col style="width: 55%;"></col>
<col style="width: 15%;"></col>
</colgroup>
<thead>
<tr>
<th colspan="3"><?php echo yii::t('core', 'attachments'); ?></th>
</tr>
</thead>
<tbody>
<?php
$i=0;
foreach ($relPrj as $prrow) {
	echo '<tr class="'.($i % 2 == 0 ? 'even' : 'odd').'">
	<td>'.CHtml::link($prrow['note_no'],"index.php#vtentity/Documents/view/".$prrow['id']).'</td>
	<td>'.$prrow['notecontent'].'</td>
	<td>'.$prrow['title'].'</td>
	</tr>';
	$i++;
} ?>
</tbody>
</table>