<div class="view">
<div class="helpdesk">
<?php
echo CHTML::hiddenField('entityidValue',$data->__get($this->entityidField), array('id'=>'entityidValue'));
$pnum = (empty($_GET[$this->modelName.'_page']) ? 1 : $_GET[$this->modelName.'_page']);
echo CHTML::hiddenField('dvcpage',$pnum-1, array('id'=>'dvcpage'));
echo CHTML::hiddenField('idfieldtxt',$data->getLookupFieldValue($this->entityLookupField,$data->getAttributes()), array('id'=>'idfieldtxt'));
echo CHTML::hiddenField('idfieldval',$data->__get($this->entityidField), array('id'=>'idfieldval'));
?>
<div class="helpdesk_left">
<div class="helpdesk_t"><?php echo $data->__get('ticket_title'); ?>&nbsp;</div>
<div class="helpdesk_q"><?php echo $data->__get('description'); ?>&nbsp;</div>
<div class="helpdesk_a"><?php echo $data->__get('solution'); ?>&nbsp;</div>
<div class="helpdesk_docs" id="helpdesk_docs">
<?php
$relDocs = $data->GetRelatedRecords('Documents');
if (count($relDocs)>0) {
	$this->renderPartial('//helpdesk/_getdocs',array(
		'relDocs'=>$relDocs,
	));
}
?>
</div>
<div class="helpdesk_comments" id="helpdesk_comments">
<?php
$relComments = $data->GetRelatedRecords('ModComments');
if (count($relComments)>0) {
	$this->renderPartial('//helpdesk/_comments',array(
		'relComments'=>$relComments,
	));
}
?>
</div>
<?php if ($data->__get('ticketstatus')!='Closed' and $data->__get('ticketstatus')!=Yii::t('core', 'closed')) {?>
<div class="addelements">
	<div class="addhelpdeskcomment">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'addhelpdeskcomment-form',
		'enableAjaxValidation'=>false,
		'enableClientValidation'=>true,
		'action'=>array('vtentity/HelpDesk/addticket/'.$data->__get($this->entityidField)),
	)); ?>
	<fieldset>
	<textarea name="ItemCommentParam" rows="1" class="post-message" placeholder="<?php echo Yii::t('core','addonecomment')?>" style="height: 60px;"></textarea>
	<div class="row buttons"><?php echo CHtml::submitButton(Yii::t('core', 'addcomment')); ?></div>
	</fieldset>
	<?php $this->endWidget(); ?>
	</div>
	<div class="addhelpdeskattachment">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'addhelpdeskdoc-form',
		'enableAjaxValidation'=>false,
		'enableClientValidation'=>true,
		'htmlOptions'=>array('enctype' => 'multipart/form-data'),
		'action'=>array('vtentity/Documents/adddocument/'.$data->__get($this->entityidField)),
	));
	echo '<fieldset>';
	echo CHTML::label(Yii::t('core','addoneattachment'), 'filename').'<br/>';
	echo CHTML::fileField('filename','',array('id'=>'filename'));
	?>
	<div class="row buttons"><?php echo CHtml::submitButton(Yii::t('core', 'addattachment')); ?></div>
	</fieldset>
	<?php $this->endWidget(); ?>
	</div>
</div>
<?php }  // is closed ?>
</div>
<div class="helpdesk_right">
<table class="list">
	<colgroup>
		<col style="width: 25%;"></col>
		<col style="width: 4px;"></col>
		<col style="width: 70%;"></col>
	</colgroup>
	<thead>
		<tr>
			<th colspan="3"><?php echo $data->__get('ticket_no'); ?></th>
		</tr>
	</thead>
	<tbody>
<?php 
	$dontShow=array('ticket_no','description','solution','ticket_title','update_log','modifiedby','from_portal','id');
	$i=0;
	foreach ($fields as $field) {
		if (!is_array($field) || empty($field['name'])) continue;  // jump values, process only fields
		$fieldname=$field['name'];
		if (in_array($fieldname,$dontShow)) continue;  // do not show these fields
		$fieldlabel=$field['label'];
		$uitype=intval($uitypes[$fieldname]);
		if(isset($field['type']['refersTo']) && !empty($field['type']['refersTo'])) $refersTo=$field['type']['refersTo'];
		else $refersTo='';
		echo '<tr class="'.($i % 2 == 0 ? 'even' : 'odd').'">
		<td><b>'.$fieldlabel.'</b></td><td></td>
		<td>'.$data->__get($fieldname).'</td>
		</tr>';
		$i++;
	} ?>
	</tbody>
</table>
</div>
</div>
</div>
<script type="text/javascript">
function _viewExecuteJS() {
	breadCrumb.add({ icon: 'view', href: 'javascript:chive.goto(\'<?php echo $this->modelLinkName;?>/<?php echo $this->entity;?>/list/'+jQuery('#idfieldval').val()+'/dvcpage/'+jQuery('#dvcpage').val()+'\')', text: jQuery('#idfieldtxt').val()});
	breadCrumb.show();
	sideBar.activate(0);
	$('#addhelpdeskcomment-form').ajaxForm({      
		dataType: 'json',  
		success: function(response) {
			if (response.data != undefined && response.data != '') {
				$('#helpdesk_comments').html(response.data);
				$('#ItemCommentParam').val('');
			}
			AjaxResponse.handle(response);
		}
	});
	$('#addhelpdeskdoc-form').ajaxForm({
		dataType: 'json',
		success: function(response) {
			console.debug(response);
			if (response.data != undefined && response.data != '') {
				$('#helpdesk_docs').html(response.data);
				$('#filename').val('');
			}
			AjaxResponse.handle(response);
		}
	});
	chive.ajaxloaded;
}
_viewExecuteJS(); // executed only once on first load, then called by CListView  afterAjaxUpdate
</script>