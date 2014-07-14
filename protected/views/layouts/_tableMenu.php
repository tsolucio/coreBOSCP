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
<div class="upperMenu">
<div class="tabMenu">
<?php
	$srchactive = Yii::app()->session[$this->entity.'_searchvals'];
	$this->widget('TabMenu', array(
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
				'visible' => $this->viewButtonSearch and empty($srchactive),
			),
			array(
					'label' => Yii::t('core','search'),
					'icon' => 'searchclear',
					'link' => array(
							'url' => "javascript:chive.goto('".$this->modelLinkName.'/'.$this->entity.'/cleansearch?modname='.$this->entity."');",
							'htmlOptions' => array('class'=>'icon','id'=>'searchclear-button'),
					),
					'visible' => $this->viewButtonSearch and !empty($srchactive),
			),
			array(
				'label' => Yii::t('core','download'),
				'icon' => 'arrow_turn_090',
				'link' => array(
					'url' => 'javascript:void(0)',
					'htmlOptions' => array(
						'class'=>'icon',
						'id' => 'download-button',
						'onclick'=>'javascript: filedownload.download(\''.yii::app()->baseUrl.'/index.php/vtentity/'.$this->entity."/downloadpdf/'+$('#entityidValue').val(),'');return false;"),
				),
				'visible' => $this->viewButtonDownloadPDF && in_array($this->entity,array('Invoice','Quotes','SalesOrder','PurchaseOrder','Documents')),
			),
			array(
				'label' => Yii::t('core','paycyp'),
				'icon' => 'pay',
				'link' => array(
					'url' => 'javascript:void(0)',
					'htmlOptions' => array(
						'class'=>'icon',
						'id' => 'paycyp-button',
						'onclick'=>'javascript: sendtopaygateway($("#entityidValue").val());return false;'
					),
				),
				'visible' => $this->viewButtonPayCyP && $this->entity=='CobroPago' && $this->_model->getAttribute('paid')==0,
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