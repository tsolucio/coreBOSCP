<?php
/*************************************************************************************************
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
 *************************************************************************************************/

Yii::app()->clientScript->registerScript('search', "
$('#search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('".$model->modelLinkName."-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('//vtentity/_search',array(
	'model'=>$model,
	'fields'=>$fields,
	'uitypes'=>$uitypes,
)); ?>
</div><!-- search-form -->

<?php
	$modelURL=$model->modelLinkName.'/'.$model->getModule();
	$pageSize=Yii::app()->user->settings->get('pageSize');
	$currentPage = Yii::app()->getRequest()->getParam(get_class($model).'_page',1)-1;
	$gridOffset="($currentPage*$pageSize)";
	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$model->modelLinkName.'-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate'=>'chive.ajaxloaded',
	'beforeAjaxUpdate'=>'chive.ajaxloading',
	'selectableRows'=> 2,
	'columns'=>CMap::mergeArray(
		$model->gridViewColumns(),// $model->getDetailGridFields($lvfields,$lvlinkfields), this worked in ENTITY
		array(array(
			'class'=>'vtyiicpngButtonColumn',
			'template'=>'{view}{update}{delete}{dlpdf}', //{related}',               
			'header'=>CHtml::dropDownList('pageSize',
				$pageSize,
				array(5=>5,10=>10,20=>20,30=>30,50=>50,100=>100),
				array('onchange'=>'$.fn.yiiGridView.update(\''.$model->modelLinkName.'-grid\',{ data:{pageSize: $(this).val() }})',
					'id'=>'select-pagesize'
				)
			),
			'viewButtonImageUrl'=>ICONPATH . '/16/view.png',
			'viewButtonUrl'=>'"#'.$modelURL.'/list/".$data["'.$model->entityidField.'"]."/dvcpage/".('.$gridOffset.'+$row)',
			'updateButtonImageUrl'=>ICONPATH . '/16/edit.png',
			'updateButtonUrl'=>'"#'.$modelURL.'/update/".$data["'.$model->entityidField.'"]."?dvcpage=".('.$gridOffset.'+$row)',
			'deleteButtonImageUrl'=>ICONPATH . '/16/delete.png',
			'deleteButtonUrl'=>'"index.php/'.$modelURL.'/delete/".$data["'.$model->entityidField.'"]',
			//'deleteConfirmation'=>CJavaScript::encode(Yii::t("zii","Are you sure you want to delete this item?")),
			'afterDelete'=>'function (link, success, data) {AjaxResponse.handle(data);}',
			'buttons'=>array(
				'dlpdf' => array (
					'label'=>Yii::t('core', 'downloadpdf'),
					'imageUrl'=>ICONPATH . '/16/pdf_icon_16.gif',
					'url'=>'"javascript: filedownload.download(\''.yii::app()->baseUrl.'/index.php/'.$modelURL.'/downloadpdf/".$data["'.$model->entityidField."\"].\"','')\"",
				),
			),
		/*
			'buttons'=>array(
		        'related' => array (
		            'label'=>Yii::t('core', 'seeRelatedEntity'),
		            'imageUrl'=>ICONPATH . '/16/relation.png',
		            'url'=>'"#entity/'.$model->getModule().'/relation/".$data["id"]',
					//'click'=>"function () {chive.goto(&quot;entity/'.$model->getModule().'/relations/4&quot;); return false;}",
		        ),
			),
		*/
		))),
	'template'=>"{pager}\n{summary}\n{items}\n{pager}",
	'cssFile'=>BASEURL.'/themes/'.Yii::app()->getTheme()->getName().'/css/grid.css',
	'pager'=>array(
		'pageSize'=>$pageSize,
		'maxButtonCount'=>10,
    	'header'=>'',
		'cssFile'=>BASEURL.'/css/layout.css',
	),
	)); ?>

<script type="text/javascript">
breadCrumb.add({ icon: 'browse', href: 'javascript:chive.goto(\'<?php echo $modelURL;?>/index\')', text: '<?php echo $model->getModuleName(); ?>'});
breadCrumb.show();
sideBar.activate(0);
</script>
