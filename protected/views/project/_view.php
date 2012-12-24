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
<div class="projectview">
<div class="view projectdetails">
<?php
	$this->widget('zii.widgets.CDetailView', array(
		'data'=>$data,
		'attributes'=>$data->getDetailViewFields(),
		'cssFile'=>BASEURL.'/themes/'.Yii::app()->getTheme()->getName().'/css/list.css',
	));
	echo CHtml::hiddenField('entityidValue',$data->__get($this->entityidField), array('id'=>'entityidValue'));
	$pnum = (empty($_GET[$this->modelName.'_page']) ? 1 : $_GET[$this->modelName.'_page']);
	echo CHtml::hiddenField('dvcpage',$pnum-1, array('id'=>'dvcpage'));
	echo CHtml::hiddenField('idfieldtxt',$data->getLookupFieldValue($this->entityLookupField,$data->getAttributes()), array('id'=>'idfieldtxt'));
	echo CHtml::hiddenField('idfieldval',$data->__get($this->entityidField), array('id'=>'idfieldval'));
	$relPrjdocs = $data->GetRelatedRecords('Documents');
	$relPrjticket = $data->GetRelatedRecords('HelpDesk');
	$relPrjmstone = $data->GetRelatedRecords('ProjectMilestone');
	$relPrjtask = $data->GetRelatedRecords('ProjectTask');
	$moduleNames = Yii::app()->cache->get('yiicpng.sidebar.availablemodules');
	if (is_array($moduleNames)) {
		if (isset($moduleNames['ProjectTask'])) $mnpt = $moduleNames['ProjectTask']['name'];
		if (isset($moduleNames['ProjectMilestone'])) $mnpm = $moduleNames['ProjectMilestone']['name'];
		if (isset($moduleNames['HelpDesk'])) $mnpk = $moduleNames['HelpDesk']['name'];
	}
?>
</div>
<div class="view projectrelated">
<div class="prjtask"><?php $this->renderPartial('//project/_prjtask',array('relPrj'=>$relPrjtask,'modname'=>$mnpt));?></div>
<div class="prjmstone"><?php $this->renderPartial('//project/_prjmstone',array('relPrj'=>$relPrjmstone,'modname'=>$mnpm));?></div>
<div class="prjticket"><?php $this->renderPartial('//project/_prjticket',array('relPrj'=>$relPrjticket,'modname'=>$mnpk));?></div>
<div class="prjdocs"><?php $this->renderPartial('//project/_prjdocs',array('relPrj'=>$relPrjdocs));?></div>
</div>
</div>
<script type="text/javascript">
function _viewSetBreadcrumb() {
	breadCrumb.add({ icon: 'view', href: 'javascript:chive.goto(\'<?php echo $this->modelLinkName;?>/<?php echo $this->entity;?>/list/'+jQuery('#idfieldval').val()+'/dvcpage/'+jQuery('#dvcpage').val()+'\')', text: jQuery('#idfieldtxt').val()});
	breadCrumb.show();
	sideBar.activate(0);
	chive.ajaxloaded;
}
_viewSetBreadcrumb(); // executed only once on first load, then called by CListView  afterAjaxUpdate
</script>