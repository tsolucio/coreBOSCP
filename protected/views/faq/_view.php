<?php /*************************************************************************************************
 * vtigerCRM vtyiiCPng - web based vtiger CRM Customer Portal
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
			<td>'.CHtml::link($faqd['title'],"index.php#vtentity/Documents/view/".$faqd['id']).'</td>
			<td>'.$faqd['notecontent'].'</td>
			<td>'.$dlwidget['value'].'</td>
			<td align="right">'.Formatter::fileSize($faqd['filesize']).'</td>
			<td align="right">'.$faqd['filedownloadcount'].'</td>
			<td align="right">'.$faqd['modifiedtime'].'</td>
			</tr>';
			$i++;
		} ?>
	</tbody>
</table>
</div>
<?php } // close div attachments
echo '<div class="faq_comments" id="faq_comments">';
if (count($relComments)>0) {
	$this->renderPartial('//faq/_comments',array(
			'relComments'=>$relComments,
	));
}
echo '</div>'; // close div comments

echo CHtml::hiddenField('entityidValue',$data->__get($this->entityidField), array('id'=>'entityidValue'));
$pnum = (empty($_GET[$this->modelName.'_page']) ? 1 : $_GET[$this->modelName.'_page']);
echo CHtml::hiddenField('dvcpage',$pnum-1, array('id'=>'dvcpage'));
echo CHtml::hiddenField('idfieldtxt',$data->getLookupFieldValue($this->entityLookupField,$data->getAttributes()), array('id'=>'idfieldtxt'));
echo CHtml::hiddenField('idfieldval',$data->__get($this->entityidField), array('id'=>'idfieldval'));
?>
<div class="addfaqcomment">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'addfaqcomment-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>true,
	'action'=>array('vtentity/Faq/addticket/'.$data->__get($this->entityidField)),
)); ?>
<fieldset>
<textarea name="ItemCommentParam" rows="1" class="post-message" placeholder="<?php echo Yii::t('core','addonecomment')?>" style="height: 60px;"></textarea>
<div class="row buttons"><?php echo CHtml::submitButton(Yii::t('core', 'addcomment')); ?></div>
</fieldset>
<?php $this->endWidget(); ?>
</div>
</div>
<script type="text/javascript">
function _viewExecuteJS() {
	breadCrumb.add({ icon: 'view', href: 'javascript:chive.goto(\'<?php echo $this->modelLinkName;?>/<?php echo $this->entity;?>/list/'+jQuery('#idfieldval').val()+'/dvcpage/'+jQuery('#dvcpage').val()+'\')', text: jQuery('#idfieldtxt').val()});
	breadCrumb.show();
	sideBar.activate(0);
	$('#addfaqcomment-form').ajaxForm({      
		dataType: 'json',  
		success: function(response) {
			if (response.data != undefined && response.data != '') {
				$('#faq_comments').html(response.data);
				$('#ItemCommentParam').val('');
			}
			AjaxResponse.handle(response);
		}
	});
	chive.ajaxloaded();
}
_viewExecuteJS(); // executed only once on first load, then called by CListView  afterAjaxUpdate
</script>