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
			'template'=>'{view}{update}{delete}{dlpdf}{pay}', //{related}',
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
				'pay' => array (
					'label'=>Yii::t('core', 'paycyp'),
					'imageUrl'=>ICONPATH . '/16/pay.png',
					'url'=>'"javascript: sendtopaygateway(\'".$data["'.$model->entityidField."\"].\"')\"",
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
