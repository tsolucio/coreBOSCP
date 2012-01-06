<?php

class vtyiiCPngApplication extends CWebApplication
{
	public $site;
	public $resource;
	public $loginuser;
	public $accesskey;
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