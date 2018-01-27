<?php 
/*************************************************************************************************
 * coreBOSCP - web based coreBOS Customer Portal
 * Copyright 2011-2014 JPL TSolucio, S.L.   --   This file is a part of coreBOSCP.
 * Licensed under the GNU General Public License (the "License") either
 * version 3 of the License, or (at your option) any later version; you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOSCP distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://www.gnu.org/licenses/>
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
?>
<div class="view">
<div class="helpdesk">
<?php
echo CHtml::hiddenField('entityidValue',$data->__get($this->entityidField), array('id'=>'entityidValue'));
$pnum = (empty($_GET[$this->modelName.'_page']) ? 1 : $_GET[$this->modelName.'_page']);
echo CHtml::hiddenField('dvcpage',$pnum-1, array('id'=>'dvcpage'));
echo CHtml::hiddenField('idfieldtxt',$data->getLookupFieldValue($this->entityLookupField,$data->getAttributes()), array('id'=>'idfieldtxt'));
echo CHtml::hiddenField('idfieldval',$data->__get($this->entityidField), array('id'=>'idfieldval'));
?>
<div class="helpdesk_left">
<div class="helpdesk_t"><?php echo $data->__get('ticket_title'); ?>&nbsp;</div>
<div class="helpdesk_q"><?php echo $data->__get('description'); ?>&nbsp;</div>
<div class="helpdesk_a"><?php echo $data->__get('solution'); ?>&nbsp;</div>
<div class="helpdesk_comments" id="helpdesk_comments">
<?php
$relComments = $data->GetRelatedRecords('ModComments');
if (is_array($relComments) and count($relComments)>0) {
	$this->renderPartial('//helpdesk/_comments',array(
		'relComments'=>$relComments,
	));
}
?>
</div>
<div class="helpdesk_docs" id="helpdesk_docs">
<?php
$relDocs = $data->GetRelatedRecords('Documents');
if (is_array($relDocs) and count($relDocs)>0) {
	$this->renderPartial('//helpdesk/_getdocs',array(
		'relDocs'=>$relDocs,
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
	<textarea name="ItemCommentParam" id="ItemCommentParam" rows="1" class="post-message" placeholder="<?php echo Yii::t('core','addonecomment')?>" style="height: 60px;"></textarea>
	<div class="row buttons"><?php echo CHtml::submitButton(Yii::t('core', 'addcomment'),array('style'=>"background-image:url('images/icons/fugue/16/add.png');background-repeat:no-repeat;padding-left:19px;background-position:2px 50%;")); ?></div>
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
	echo CHtml::label(Yii::t('core','addoneattachment'), 'filename').'<br/>';
	echo CHtml::fileField('filename','',array('id'=>'filename'));
	?>
	<div class="row buttons"><?php echo CHtml::submitButton(Yii::t('core', 'addattachment'),array('style'=>"background-image:url('images/icons/fugue/16/add.png');background-repeat:no-repeat;padding-left:19px;background-position:2px 50%;")); ?></div>
	</fieldset>
	<?php $this->endWidget(); ?>
	</div>
</div>
<?php }  // is closed
$moduleNames = Yii::app()->cache->get('yiicpng.sidebar.availablemodules');
if (is_array($moduleNames) and isset($moduleNames['Timecontrol'])) {
	$relTCs = $data->GetRelatedRecords('Timecontrol');
	if (is_array($relTCs) and count($relTCs)>0) {
		$relTCs = $data->dereferenceIds($relTCs);
		$this->renderPartial('//helpdesk/_gettcs',array(
			'relTCs'=>$relTCs,
			'TCName'=>$moduleNames['Timecontrol']['name'],
			'hdid'=>$data->__get($this->entityidField)
		));
	}
}
?>
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
	$dontShow=array('ticket_no','description','solution','ticket_title','update_log','modifiedby','from_portal','id','commentadded','from_mailscanner');
	$i=0;
	foreach ($fields as $field) {
		if (!is_array($field) || empty($field['name'])) continue;  // jump values, process only fields
		$fieldname=$field['name'];
		if (in_array($fieldname,$dontShow)) continue;  // do not show these fields
		$fieldlabel=$field['label'];
		$uitype=intval($uitypes[$fieldname]);
		if (isset($field['type']['refersTo']) && !empty($field['type']['refersTo'])) {
			$refersTo=$field['type']['refersTo'];
		} else {
			$refersTo='';
		}
		$datavalue=$data->__get($fieldname);
		if (isset($field['type']['picklistValues']) && !empty($field['type']['picklistValues'])) {
			foreach ($field['type']['picklistValues'] as $idx => $plvalue) {
				if ($plvalue['value']==$datavalue) {
					$datavalue = $plvalue['label'];
				}
			}
		}
		echo '<tr class="'.($i % 2 == 0 ? 'even' : 'odd').'">
		<td><b>'.$fieldlabel.'</b></td><td></td>
		<td>'.$datavalue.'</td>
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
		beforeSubmit: function(arr, form, options) { 
			chive.ajaxloading();
		},
		success: function(response) {
			if (response.data != undefined && response.data != '') {
				$('#helpdesk_comments').html(response.data);
				$('#ItemCommentParam').val('');
			}
			AjaxResponse.handle(response);
			chive.ajaxloaded();
		},
		error: function(response) {
			AjaxResponse.handle(response);
			chive.ajaxloaded();
		}
	});
	$('#addhelpdeskdoc-form').ajaxForm({
		dataType: 'json',
		beforeSubmit: function(arr, form, options) { 
			chive.ajaxloading();
		},
		success: function(response) {
			if (response.data != undefined && response.data != '') {
				$('#helpdesk_docs').html(response.data);
				$('#filename').val('');
			}
			AjaxResponse.handle(response);
			chive.ajaxloaded();
		},
		error: function(response) {
			AjaxResponse.handle(response);
			chive.ajaxloaded();
		}
	});
	chive.ajaxloaded();
}
_viewExecuteJS(); // executed only once on first load, then called by CListView  afterAjaxUpdate
</script>
