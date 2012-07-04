<div class="upperMenu">
<div class="tabMenu">
	<?php $this->widget('TabMenu', array(
		'items' => array(
			array(
				'label' => Yii::t('core','edit'),
				'icon' => 'edit',
				'link' => array(
					'url' => '',
					'htmlOptions' => array(
							'class'=>'icon',
							'onclick'=>"chive.goto('".$this->modelLinkName."/".$this->entity."/update/'+$('#entityidValue').val()+'?dvcpage='+$('#dvcpage').val());return false;",
							),
				),
				'visible' => $this->viewButtonEdit,
			),
			array(
				'label' => Yii::t('core','delete'),
				'icon' => 'delete',
				'link' => array(
					'url' => '',
					'htmlOptions' => array(
							'class'=>'icon',
							'onclick' => "return deleteRecord($('#entityidValue').val(),".CJavaScript::encode(Yii::t('zii','Are you sure you want to delete this item?')."\n")."+$('#idfieldtxt').val(),'".$this->entity."','".$this->modelLinkName."');",
							),
				),
				'visible' => $this->viewButtonDelete,
			),
			array(
				'label' => Yii::t('core','insert'),
				'icon' => 'add',
				'link' => array(
					'url' => $this->modelLinkName."/".$this->entity.'/create',
					'htmlOptions' => array('class'=>'icon'),
				),
				'visible' => $this->viewButtonCreate,
			),
			array(
				'label' => Yii::t('core','search'),
				'icon' => 'search',
				'link' => array(
					'url' => '',
					'htmlOptions' => array('class'=>'icon','id'=>'search-button'),
				),
				'visible' => $this->viewButtonSearch,
			),
			array(
				'label' => Yii::t('core','downloadpdf'),
				'icon' => ICONPATH . '/16/pdf_icon_16.gif',
				'link' => array(
					'url' => 'javascript:void(0)',
					'htmlOptions' => array('class'=>'icon', 'onclick'=>'javascript: filedownload.download(\''.yii::app()->baseUrl.'/index.php/vtentity/'.$this->entity."/downloadpdf/'+$('#entityidValue').val(),'');return false;"),
				),
				'visible' => $this->viewButtonDownloadPDF && in_array($this->entity,array('Invoice','Quotes','SalesOrder','PurchaseOrder')),
			),
			array(
				'label' => Yii::t('core','export'),
				'icon' => 'export',
				'link' => array(
					'url' => $this->modelLinkName.'/' . $this->entity . '/export',
					'htmlOptions' => array('class'=>'icon'),
				),
				'visible' => false,
			),
			array(
				'label' => Yii::t('core','import'),
				'icon' => 'import',
				'link' => array(
					'url' => $this->modelLinkName.'/' . $this->entity . '/import',
					'htmlOptions' => array('class'=>'icon'),
				),
				'visible' => false,
			),
		),
	));
	?>
</div>
<div class="upperTitle"><?php echo $this->moduleName; ?></div>
</div>