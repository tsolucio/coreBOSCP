<?php 
$currentPage = Yii::app()->getRequest()->getParam('Entity_page',1)-1;
$pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']);
$gridOffset="($currentPage*$pageSize)+1";
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'entity-grid',
	'dataProvider'=>$dataProvider,
	'ajaxUpdate'=>TRUE,
	'afterAjaxUpdate'=>'chive.ajaxloaded',
	'beforeAjaxUpdate'=>'chive.ajaxloading',
	'updateSelector'=>'entity-grid',
	'selectableRows'=> 2,
	'filter'=>$model,
	'hideHeader'=>false,
	'columns'=>CMap::mergeArray(
		$model->getDetailGridFields($lvfields,$lvlinkfields),
		array(array(
			'class'=>'CButtonColumn',
			'template'=>'{view}{update}{delete}', //{related}',               
			'header'=>CHtml::dropDownList('pageSize',
				$pageSize,
				array(5=>5,10=>10,20=>20,30=>30,50=>50,100=>100),
				array('onchange'=>'$.fn.yiiGridView.update(\'entity-grid\',{ data:{pageSize: $(this).val() }})',
					'id'=>'select-pagesize'
				)
			),
			'buttons'=>array(
		        'view' => array (
		            'imageUrl'=>ICONPATH . '/16/view.png',
		        	'url'=>'"#entity/'.$model->getModule().'/view/id/".$data["id"]."/Entity_page/".('.$gridOffset.'+$row)',
		        ),
		        'update' => array (
		            'imageUrl'=>ICONPATH . '/16/edit.png',
		        	'url'=>'"#entity/'.$model->getModule().'/edit/id/".$data["id"]."/Entity_page/".('.($currentPage+1).')."/cv/GridView"',
		        ),
		        'delete' => array (
		            'imageUrl'=>ICONPATH . '/16/delete.png',
		        	'url'=>'"index.php/entity/'.$model->getModule().'/deleteid/id/".$data["id"]',
		        ),
//		        'related' => array (
//		            'label'=>Yii::t('core', 'seeRelatedEntity'),
//		            'imageUrl'=>ICONPATH . '/16/relation.png',
//		            'url'=>'"#entity/'.$model->getModule().'/deleteid/id/".$data["id"]',
//					//'click'=>"function () {chive.goto(&quot;entity/'.$model->getModule().'/relations/4&quot;); return false;}",
		/*
		http://localhost/cpng/
		<a class="icon" href="#entity/'.$model->getModule().'/relations/4" onclick="chive.goto(&quot;entity/'.$model->getModule().'/relations/4&quot;); return false;">
		*/
		      //  ),
			),
		)
	)),
    
	'template'=>"{pager}\n{summary}\n{items}\n{pager}",
	'cssFile'=>BASEURL.'/themes/'.Yii::app()->getTheme()->getName().'/css/grid.css',
	'pager'=>array(
		'pageSize'=>$pageSize,
		'maxButtonCount'=>10,
    	'header'=>'',
		'cssFile'=>BASEURL.'/css/layout.css',
	),
	
)); ?>
