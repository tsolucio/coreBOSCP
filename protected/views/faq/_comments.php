<table class="list">
<colgroup>
<col style="width: 15%;"></col>
<col style="width: 85%;"></col>
</colgroup>
<thead>
<tr>
<th colspan="2"><?php echo Yii::t('core','comments'); ?></th>
</tr>
</thead>
<tbody>
<?php
$i=0;
foreach ($relComments as $faqc) {
	echo '<tr class="'.($i % 2 == 0 ? 'even' : 'odd').'">
	<td><b>'.$faqc['createdtime'].'</b></td>
	<td>'.$faqc['commentcontent'].'</td>
	</tr>';
	$i++;
} ?>
</tbody>
</table>