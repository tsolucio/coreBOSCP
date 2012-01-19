<?php
/*
 * vtigerCRM vtyiiCPng - web based vtiger CRM Customer Portal
* Copyright (C) 2011 Opencubed shpk: JPL TSolucio, S.L./StudioSynthesis, S.R.L.
*
* This file is part of vtyiiCPng.
*
* The contents of this file are subject to the vtiger CRM Public License Version 1.0 ("License")
* You may not use this file except in compliance with the License
* The Original Code is:  Opencubed Open Source
* The Initial Developer of the Original Code is Opencubed.
* Portions created by Opencubed are Copyright (C) Opencubed.
* All Rights Reserved.
*
* vtyiiCPng is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*
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