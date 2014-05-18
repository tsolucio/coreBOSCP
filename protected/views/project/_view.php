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
	chive.ajaxloaded();
}
_viewSetBreadcrumb(); // executed only once on first load, then called by CListView  afterAjaxUpdate
</script>