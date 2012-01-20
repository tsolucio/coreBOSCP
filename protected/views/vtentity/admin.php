<?php
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

<h1><?php echo $this->entity; ?></h1>

<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
	'fields'=>$fields,
	'uitypes'=>$uitypes,
)); ?>
</div><!-- search-form -->

<?php
	$pageSize=Yii::app()->user->settings->get('pageSize');
	$currentPage = Yii::app()->getRequest()->getParam($model->modelName.'_page',1)-1;
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
			'template'=>'{view}{update}{delete}', //{related}',               
			'header'=>CHtml::dropDownList('pageSize',
				$pageSize,
				array(5=>5,10=>10,20=>20,30=>30,50=>50,100=>100),
				array('onchange'=>'$.fn.yiiGridView.update(\''.$model->modelLinkName.'-grid\',{ data:{pageSize: $(this).val() }})',
					'id'=>'select-pagesize'
				)
			),
			'viewButtonImageUrl'=>ICONPATH . '/16/view.png',
			'viewButtonUrl'=>'"#'.$model->modelLinkName.'/'.$model->getModule().'/list/".$data["'.$model->entityidField.'"]."/dvcpage/".('.$gridOffset.'+$row)',
			'updateButtonImageUrl'=>ICONPATH . '/16/edit.png',
			'updateButtonUrl'=>'"#'.$model->modelLinkName.'/'.$model->getModule().'/update/".$data["'.$model->entityidField.'"]."?dvcpage=".('.$gridOffset.'+$row)',
			'deleteButtonImageUrl'=>ICONPATH . '/16/delete.png',
			//'deleteConfirmation'=>CJavaScript::encode(Yii::t("zii","Are you sure you want to delete this item?")),
			'afterDelete'=>'function (link, success, data) {AjaxResponse.handle(data);}',
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
breadCrumb.add({ icon: 'browse', href: 'javascript:chive.goto(\'<?php echo $this->modelLinkName;?>/<?php echo $this->entity;?>/index\')', text: '<?php echo $this->entity; ?>'});
breadCrumb.show();
sideBar.activate(0);
</script>
