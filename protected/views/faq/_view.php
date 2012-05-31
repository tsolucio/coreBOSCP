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
<div class="faq_docs">
<table class="list">
	<colgroup>
		<col style="width: 10%;"></col>
		<col style="width: 25%;"></col>
		<col style="width: 65%;"></col>
	</colgroup>
	<thead>
		<tr>
			<th colspan="3"><?php echo Yii::t('core','attachments'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="even">
			<td>reference</td>
			<td>doc name and link</td>
			<td>description: <b>** FIX ME **</b> Put here a list of related documents for download. We need to be able to do related list REST queries for this</td>
		</tr>
	</tbody>
</table>
</div>
<div class="faq_comments">
<table class="list">
	<colgroup>
		<col style="width: 80%;"></col>
		<col style="width: 10%;"></col>
		<col style="width: 10%;"></col>
	</colgroup>
	<thead>
		<tr>
			<th colspan="3"><?php echo Yii::t('core','comments'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="even">
			<td>comment <b>** FIX ME **</b> Put here a list of related comments. We need to be able to do related list REST queries for this</td>
			<td>fecha</td>
			<td>usuario</td>
		</tr>
	</tbody>
</table>
</div>
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