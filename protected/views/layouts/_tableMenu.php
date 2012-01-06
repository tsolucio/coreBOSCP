<div class="tabMenu">
	<?php $this->widget('TabMenu', array(
		'items' => array(
			array(
				'label' => Yii::t('core','edit'),
				'icon' => 'edit',
				'link' => array(
					'url' => $this->schema.'/' . $this->table . '/edit/id/'.
							(isset($data) ? $data['id'] : $this->entityidValue).'/Entity_page/'.Yii::app()->getRequest()->getParam('Entity_page',1).
							'/cv/ListView',
					'htmlOptions' => array('class'=>'icon'),
				),
				'visible' => $this->viewButtonActive,
			),
			array(
				'label' => Yii::t('core','delete'),
				'icon' => 'delete',
				'link' => array(
					'url' => 'javascript: tableBrowse.deleteRecord();',
					'htmlOptions' => array('class'=>'icon'),
				),
				'visible' => $this->viewButtonActive,
			),
			array(
				'label' => Yii::t('core','insert'),
				'icon' => 'add',
				'link' => array(
					'url' => $this->schema.'/' . $this->table . '/create',
					'htmlOptions' => array('class'=>'icon'),
				),
				'visible' => true,
			),
			array(
				'label' => Yii::t('core','search'),
				'icon' => 'search',
				'link' => array(
					'url' => $this->schema.'/' . $this->table . '/search',
					'htmlOptions' => array('class'=>'icon'),
				),
				'visible' => true,
			),
			/*
			array(
				'label' => Yii::t('core','drop'),
				'icon' => 'delete',
				'link' => array(
					'url' => 'javascript:void(0)',
					'htmlOptions' => array('class'=>'icon', 'onclick'=>'tableGeneral.drop()'),
				),
				'visible' => Yii::app()->user->privileges->hasPermission($this->schema, $this->module, 'DROP'),
			),
			*/
			array(
				'label' => Yii::t('core','export'),
				'icon' => 'export',
				'link' => array(
					'url' => $this->schema.'/' . $this->table . '/export',
					'htmlOptions' => array('class'=>'icon'),
				),
				'visible' => false,
			),
			array(
				'label' => Yii::t('core','import'),
				'icon' => 'import',
				'link' => array(
					'url' => $this->schema.'/' . $this->table . '/import',
					'htmlOptions' => array('class'=>'icon'),
				),
				'visible' => false,
			),
		),
	));
	?>
</div>