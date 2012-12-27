<?php /*************************************************************************************************
 * Evolutivo vtyiiCPng - web based vtiger CRM Customer Portal
 * Copyright 2012 JPL TSolucio, S.L.  --  This file is a part of vtyiiCPNG.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/?>
<div class="yiiForm">

<?php $form=$this->beginWidget('vtyiicpngActiveForm', array(
	'id'=>$this->modelName.'-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>true,
)); ?>
<fieldset>
        <legend><?php echo $legend; ?></legend>
	<p class="note"><?php echo Yii::t('core', 'fieldsRequired'); ?></p>
    <div id="othermsg"></div>

	<?php
	echo $form->errorSummary($model);
	foreach($fields as $field) {
		if (!is_array($field) || empty($field['name'])) continue;  // jump values, process only fields
		if($field['name'] !='id')
		{
			$fieldname=$field['name'];
			$fieldlabel=$field['label'];
			$uitype=intval($uitypes[$fieldname]);
			if(isset($field['type']['refersTo']) && !empty($field['type']['refersTo'])) $refersTo=$field['type']['refersTo'];
			else $refersTo='';
			$moreinfo='';
			if(isset($field['type']['name']) && !empty($field['type']['name']) && $field['type']['name']=='date')
				$moreinfo = array('dateformat'=>$field['type']['format']);
			$action=$model->getIsNewRecord()?'create':'edit';
			echo '<div class="row">';
			echo $form->labelEx($model,$fieldname,array('label'=>$fieldlabel));
			echo $form->getVtigerEditField($uitype,$model,$fieldname,array('maxlength'=>100),$refersTo,$action,$moreinfo);
			echo $form->error($model,$fieldname);
			echo '</div>';
		}
	}
	?>

	<div class="row">
		<!-- administrative fields -->
		<?php if(isset($_REQUEST['dvcpage'])) echo CHtml::hiddenField('dvcpage',$_REQUEST['dvcpage']); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->getIsNewRecord() ? Yii::t('core', 'create') : Yii::t('core', 'save')); ?>
	</div>
</fieldset>
<?php $this->endWidget(); ?>
<script type="text/javascript">
	$('#<?php echo $this->modelName ?>-form').ajaxForm({      
		dataType: 'json',
		beforeSerialize: removeEmptyFileInput,
		success: function(response) {
			if (response.data != undefined && response.data != '') 
				$('#content').html(response.data);
			AjaxResponse.handle(response);
		}
	});
	function removeEmptyFileInput(formData, options) { 
		if ($('#Vtentity_filename').val()=='' && $('#Vtentity_filename_E__').val()=='') {
			$('#Vtentity_filename').remove();
			$('#Vtentity_filename_E__').remove();
		}
	    return true; 
	} 
</script>

</div><!-- form -->