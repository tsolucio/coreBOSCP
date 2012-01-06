<h2><?php echo Yii::t('core', 'search'); ?></h2>

<div class="form">
<?php echo CHtml::form('', 'post', array('id' => 'searchForm')); ?>

<table class="list">
	<colgroup>
		<col style="width: 150px; font-weight: bold;" />
		<col class="operator" />
		<col />
	</colgroup>
	<thead>
		<tr>
			<th><?php echo Yii::t('core', 'field'); ?></th>
			<th><?php echo Yii::t('core', 'operator'); ?></th>
			<th><?php echo Yii::t('core', 'value'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php $tabIndex = 10; ?>
		<?php foreach($row->getAttributes() AS $column) { ?>
			<tr>
				<td><?php  echo $column['label']; ?></td>
				<td><?php echo CHtml::dropDownList('operator['.$column['name'].']','', $operators); ?></td>
				<td>
					<?php #echo CHtml::activeTextField($row, $column->name, array('class'=>'text', 'tabIndex'=>$tabIndex)); ?>
					<?php echo CHtml::textField('Row[' . $column['name'] . ']', '', array('class'=>'text', 'tabIndex'=>$tabIndex)); ?>
				</td>
			</tr>
			<?php $tabIndex++; ?>
		<?php } ?>
	</tbody>
</table>

<div class="buttons">
	<a href="javascript:void(0);" onclick="$('form').submit();" class="icon button">
		<?php echo Html::icon('search', 16, false, 'core.insert'); ?>
		<span><?php echo Yii::t('core', 'search'); ?></span>
	</a>
</div>


<script type="text/javascript">
$('#searchForm').ajaxForm({
	success: function(responseText, statusText) {
		AjaxResponse.handle(responseText);
		$('#content-inner').html(responseText);
		init();
	},
        error: function(responseText, statusText) {
		AjaxResponse.handle(responseText);
		$('#content-inner').html(responseText.responseText);
		init();
	},
        complete: function(responseText, statusText) {
		AjaxResponse.handle(responseText);
		$('#content-inner').html(responseText.responseText);
		init();
	}
});

$('#searchForm').keydown(function(e) {
	if(e.which == 13)
	{
		$(this).submit();
	}	
});

$('table.list input:first').focus();
var schema = '<?php echo $this->schema; ?>';
var table = '<?php echo $this->table; ?>';
breadCrumb.add({ icon: 'search', href: 'javascript:chive.goto(\'entity/' + table + '/search\')', text: table});
sideBar.activate(0);
</script>

<input type="submit" name="submit" style="display: none;" />

<?php echo CHtml::endForm(); ?>
</div>