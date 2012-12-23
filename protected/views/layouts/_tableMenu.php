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