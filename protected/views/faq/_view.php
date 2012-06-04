<div class="view">
<div class="faq">
<div class="faq_left">
<div class="faq_q"><?php echo $data->__get('question'); ?></div>
<div class="faq_a"><?php echo $data->__get('faq_answer'); ?></div>
</div>
<div class="faq_right">
<table class="list">
	<colgroup>
		<col style="width: 25%;"></col>
		<col style="width: 4px;"></col>
		<col style="width: 70%;"></col>
	</colgroup>
	<thead>
		<tr>
			<th colspan="3"><?php echo $data->__get('faq_no'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="even">
			<td><label><?php echo Yii::t('core','product'); ?></label></td>
			<td></td>
			<td><?php echo $data->__get('product_id'); ?></td>
		</tr>
		<tr class="odd">
			<td><label><?php echo Yii::t('core','category'); ?></label></td>
			<td></td>
			<td><?php echo $data->__get('faqcategories'); ?></td>
		</tr>
		<tr class="even">
			<td><label><?php echo Yii::t('core','status'); ?></label></td>
			<td></td>
			<td><?php echo $data->__get('faqstatus'); ?></td>
		</tr>
		<tr class="odd">
			<td><label><?php echo Yii::t('core','createdtime'); ?></label></td>
			<td></td>
			<td><?php echo $data->__get('createdtime'); ?></td>
		</tr>
		<tr class="even">
			<td><label><?php echo Yii::t('core','modifiedtime'); ?></label></td>
			<td></td>
			<td><?php echo $data->__get('modifiedtime'); ?></td>
		</tr>
	</tbody>
</table>
</div>
</div>
<?php 
$relDocs = $data->GetRelatedRecords('Documents');
$relComments = $data->GetRelatedRecords('ModComments');
if (count($relDocs)>0) {
?>
<div class="faq_docs">
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
		$i=0;
		foreach ($relDocs as $faqd) {
			$data->setId($faqd['id']);
			$dlwidget=$data->getVtigerViewField(69,'','','');
			echo '<tr class="'.($i % 2 == 0 ? 'even' : 'odd').'">
			<td><b>'.$faqd['title'].'</b></td>
			<td>'.$faqd['notecontent'].'</td>
			<td>'.$dlwidget['value'].'</td>
			<td align="right">'.round($faqd['filesize']/1024,($faqd['filesize']<1024 ? 2 : 0)).'Kb</td>
			<td align="right">'.$faqd['filedownloadcount'].'</td>
			<td align="right">'.$faqd['modifiedtime'].'</td>
			</tr>';
			$i++;
		} ?>
	</tbody>
</table>
</div>
<?php } // close div attachments
if (count($relComments)>0) {
?>
<div class="faq_comments">
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
</div>
<?php } // close div comments ?>
<?php
	echo CHTML::hiddenField('entityidValue',$data->__get($this->entityidField), array('id'=>'entityidValue'));
	$pnum = (empty($_GET[$this->modelName.'_page']) ? 1 : $_GET[$this->modelName.'_page']);
	echo CHTML::hiddenField('dvcpage',$pnum-1, array('id'=>'dvcpage'));
?>
</div>
<script type="text/javascript">
breadCrumb.add({ icon: 'view', href: 'javascript:chive.goto(\'<?php echo $this->modelLinkName;?>/<?php echo $this->entity;?>/list/<?php echo $data->__get($this->entityidField)?>/dvcpage/<?php echo $pnum-1; ?>\')', text: <?php echo CJavaScript::encode($data->getLookupFieldValue($this->entityLookupField,$data->getAttributes())); ?>});
breadCrumb.show();
sideBar.activate(0);
</script>