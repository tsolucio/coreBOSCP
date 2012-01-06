<div id="deleteRowDialog" title="<?php echo Yii::t('core', 'deleteRows'); ?>" style="display: none">
	<?php echo Yii::t('core', 'doYouReallyWantToDeleteSelectedRows'); ?>
</div>

<?php if($model->hasResultSet() && $model->getData()) { ?>

	<div class="list">
		<div class="buttonContainer">
			<?php 
				$cpngpager=$model->getPagination();
				$cpngpagerCP=$cpngpager->getCurrentPage();
				$cpngpagerPS=$cpngpager->getPageSize();
				$this->widget('LinkPager',array('pages'=>$cpngpager));
			?>
		</div>

		<?php $i = 0; ?>
		<table class="list <?php if($model->getIsUpdatable()) { ?>addCheckboxes editable<?php } ?>" style="width: auto; min-width: 200px;" id="browse">
			<colgroup>
				<col class="checkbox" />
				<?php if(isset($type) && $type == 'select' && $model->singleTableSelect) { ?>
					<col class="action" />
					<col class="action" />
					<col class="action" />
				<?php } ?>
				<?php foreach ($model->getColumns() AS $column) { ?>
					<?php echo '<col />'; ?>
				<?php } ?>
			</colgroup>
			<thead>
				<tr>
					<?php if($model->getQueryType() == 'select' && $model->singleTableSelect) { ?>
						<th><input type="checkbox" /></th>
						<th></th>
						<th></th>
						<th></th>
					<?php } ?>
					<?php foreach ($model->getColumns ()AS $column) { ?>
						<th>
							<?php echo ($model->getQueryType() == 'select' ? $model->getSort()->link($column) : $column); ?>
						</th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach($model->getData() AS $row) {?>
					<tr>
						<?php if($model->getQueryType() == 'select' && $model->singleTableSelect) { ?>
							<td>
								<input type="checkbox" name="browse[]" value="row_<?php echo $i; ?>" />
							</td>
							<td class="action">
								<a href="javascript:void(0);" class="icon" onclick="globalBrowse.deleteRow(<?php echo $i; ?>);">
									<?php echo Html::icon('delete', 16, false, 'core.delete'); ?>
								</a>
							</td>
							<td class="action">
								<?php 
								echo Html::ajaxLink('entity/vtiger_account/edit/'.$row[$model->entityid], array('class' => 'icon'));
								echo Html::icon('edit', 16, false, 'core.edit');
								echo "<span>";
								?>
							</td>
							<td class="action">
								<?php 
								echo Html::ajaxLink('entity/vtiger_account/relations/'.$row[$model->entityid], array('class' => 'icon'));
								echo Html::icon('relation', 16, false, 'core.showRelations');
								echo "<span>";
								?>
							</td>
						<?php } ?>
						
						<?php foreach($row AS $key=>$value) { ?>
							<td class="<?php echo $key; ?>">
								<?php if($model->singleTableSelect && DataType::getInputType($model->getTable()->columns[$key]->dbType) == "file" && $value) { ?>
									<?php if($model->hasPrimaryKey()) { ?>
									<a href="javascript:void(0);" class="icon" onclick="globalBrowse.download('<?php echo Yii::app()->createUrl('row/download'); ?>', {key: JSON.stringify(keyData[<?php echo $i; ?>]), column: '<?php echo $key; ?>', table: '<?php echo $model->table; ?>', schema: '<?php echo $model->schema; ?>'})">
										<?php echo Html::icon('save'); ?> 
										<?php echo Formatter::fileSize(strlen($value)); ?>
									</a>
									<?php } else { ?>
										<?php echo Formatter::fileSize(strlen($value)); ?>
									<?php }?>
								<?php } elseif($model->table !== null) { 
										if ($model->lookupfield==$key) {
											echo Html::ajaxLink('entity/vtiger_account/view/?table=vtiger_account&id='.$row[$model->entityid].'&Entity_page='.(($cpngpagerCP*$cpngpagerPS)+$i+1), array('class' => 'icon'));
											echo Html::icon('view', 16, false, 'core.view');
											echo "<span>";
										}?>
									<span><?php echo is_null($value) ? '<span class="null">NULL</span>' : (Yii::app()->user->settings->get('showFullColumnContent', 'entity.browse', $model->schema . '.' .  $model->table) ? htmlspecialchars($value) : StringUtil::cutText(htmlspecialchars($value), 100)); ?></span>
										<?php if ($model->lookupfield==$key) { echo "</span>";} ?>
								<?php } else { ?>
									<span><?php echo is_null($value) ? '<span class="null">NULL</span>' : (Yii::app()->user->settings->get('showFullColumnContent', 'entity.browse', $model->schema) ? htmlspecialchars($value) : StringUtil::cutText(htmlspecialchars($value), 100)); ?></span>
								<?php } ?>
							</td>
						<?php } ?>
					</tr>
					<?php $i++; ?>
				<?php } ?>
			</tbody>
			<?php if($model->getQueryType() != "explain") { ?>
			<tfoot>
				<tr>
					<th colspan="<?php echo 4 + count($row); ?>">
						<?php echo Yii::t('core', 'showingRowsOfRows', array('{start}' => $model->getStart(), '{end}' => min($model->getStart() + $model->getPagination()->getPagesize(), $model->getTotal()), '{total}' => $model->getTotal())); ?>
					</th>
				</tr>
			</tfoot>
			<?php } ?>
		</table>

	<div class="buttonContainer">
		<?php if ($model->getQueryType() == 'select' && $model->singleTableSelect) { ?>
			<div class="withSelected left">
				<span class="icon">
					<?php echo Html::icon('arrow_turn_090'); ?>
					<span><?php echo Yii::t('core', 'withSelected'); ?></span>
				</span>
				<a class="icon button" href="javascript:void(0)" onclick="globalBrowse.deleteRows()">
					<?php echo Html::icon('delete', 16, false, 'core.delete'); ?>
					<span><?php echo Yii::t('core', 'delete'); ?></span>
				</a>
				<!-- a class="icon button" href="javascript:void(0)" onclick="globalBrowse.exportRows()">
					<?php echo Html::icon('save', 16, false, 'core.export'); ?>
					<span><?php echo Yii::t('core', 'export'); ?></span>
				</a -->
			</div>
			<?php $keyData = $model->getKeyData(); 
				  if (count($keyData) > 0) 
				  { ?>
				<script type="text/javascript">
					var keyData = <?php echo CJSON::encode($keyData); ?>;
				</script>
			<?php } ?>
		<?php } ?>
	</div>
	<div class="buttonContainer">
		<?php $this->widget('LinkPager',array('pages'=>$model->getPagination())); ?>
	</div>

<?php } elseif($model->execute) { ?>
	<?php echo Yii::t('core', 'emptyResultSet'); ?>
<?php } ?>

<script type="text/javascript">
	globalBrowse.setup();
	AjaxResponse.handle(<?php echo $model->getResponse(); ?>);
	globalPost = { query: <?php echo CJSON::encode($model->getOriginalQueries()); ?> };
</script>