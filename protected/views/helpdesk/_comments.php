<table class="list">
<colgroup>
<col style="width: 15%;"></col>
<col style="width: 85%;"></col>
</colgroup>
<thead>
<tr>
<th colspan="3"><?php echo Yii::t('core','comments'); ?></th>
</tr>
</thead>
<tbody>
<?php
$comms = Vtentity::model();
$ids=array();
foreach ($relComments as $helpdeskc) {
	$ids[]=$helpdeskc['creator'];
}
$ids=array_unique($ids);
$deref=unserialize($comms->getComplexAttributeValues($ids));
$i=0;
foreach ($relComments as $helpdeskc) {
	$tm=$helpdeskc['creator'];
	echo '<tr class="'.($i % 2 == 0 ? 'even' : 'odd').'">
	<td><b>'.$helpdeskc['createdtime'].'</b></td>
	<td>'.$helpdeskc['commentcontent'].'</td>
	<td>'.CHtml::link(CHtml::encode($deref[$tm]['reference']),'#vtentity/'.$deref[$tm]['module']."/view/$tm").'</td>
	</tr>';
	$i++;
} ?>
</tbody>
</table>