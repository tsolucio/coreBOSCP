<table class="list">
<colgroup>
<col style="width: 30%;"></col>
<col style="width: 55%;"></col>
<col style="width: 15%;"></col>
</colgroup>
<thead>
<tr>
<th colspan="3"><?php echo $modname; ?></th>
</tr>
</thead>
<tbody>
<?php
$i=0;
foreach ($relPrj as $prrow) {
	echo '<tr class="'.($i % 2 == 0 ? 'even' : 'odd').'">
	<td>'.CHtml::link($prrow['ticket_no'],"index.php#vtentity/HelpDesk/view/".$prrow['id']).'</td>
	<td>'.$prrow['title'].'</td>
	<td>'.$prrow['status'].'</td>
	</tr>';
	$i++;
} ?>
</tbody>
</table>