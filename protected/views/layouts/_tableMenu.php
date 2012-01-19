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
				'visible' => $this->viewButtonActive,
			),
			array(
				'label' => Yii::t('core','delete'),
				'icon' => 'delete',
				'link' => array(
					'url' => '',
					'htmlOptions' => array(
							'class'=>'icon',
							'onclick' => "return deleteRecord($('#entityidValue').val(),".CJavaScript::encode(Yii::t('zii','Are you sure you want to delete this item?')).",'".$this->entity."','".$this->modelLinkName."');",
							),
				),
				'visible' => $this->viewButtonActive,
			),
			array(
				'label' => Yii::t('core','insert'),
				'icon' => 'add',
				'link' => array(
					'url' => $this->modelLinkName."/".$this->entity.'/create',
					'htmlOptions' => array('class'=>'icon'),
				),
				'visible' => true,
			),
			array(
				'label' => Yii::t('core','search'),
				'icon' => 'search',
				'link' => array(
					//'url' => $this->modelLinkName.'/' . $this->entity . '/search',
					'htmlOptions' => array('class'=>'icon','id'=>'search-button'),
				),
				'visible' => $this->viewButtonSearch,
			),
			/*
			array(
				'label' => Yii::t('core','drop'),
				'icon' => 'delete',
				'link' => array(
					'url' => 'javascript:void(0)',
					'htmlOptions' => array('class'=>'icon', 'onclick'=>'tableGeneral.drop()'),
				),
				'visible' => Yii::app()->user->privileges->hasPermission($this->modelLinkName, $this->module, 'DROP'),
			),
			*/
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