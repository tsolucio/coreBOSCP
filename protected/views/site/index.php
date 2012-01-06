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
