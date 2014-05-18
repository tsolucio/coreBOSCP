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
		if($field['name'] !='id' && $field['name'] !='assigned_user_id')
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
