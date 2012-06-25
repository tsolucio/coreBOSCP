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
class vtyiiCPngApplication extends CWebApplication
{
	public $site;
	public $loginuser;
	public $accesskey;
	public $attachment_folder;
	public $vtyiicpngScope;
	public $notSupportedModules;
	public $debug;
	
	/**
	 * Sends json output headers, outputs $json and ends the application.
	 * @param string $json
	 */
	public function endJson($json)
	{
		header("Content-type: application/json");
		self::end($json);
	}
}