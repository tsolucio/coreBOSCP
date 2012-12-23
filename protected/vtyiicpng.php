<?php
/**************************************************************************************************
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
*/

require(dirname(__FILE__).'/../yii/YiiBase.php');
require_once(dirname(__FILE__).'/components/vtyiicpngApplication.php');

class Yii extends YiiBase
{
	public static function createWebApplication($config=null)
	{
		$return=self::createApplication('vtyiiCPngApplication',$config);
		Yii::$classMap['CActiveRecord']=dirname(__FILE__).'/extensions/VTActiveResource/CActiveRecord.php';
		Yii::$classMap['CActiveRecordMetaData']=dirname(__FILE__).'/extensions/VTActiveResource/CActiveRecord.php';
		return $return;
	}
}