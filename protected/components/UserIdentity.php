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

class UserIdentity extends CUserIdentity
{
        public $lastLoginTime;
        public $startSupportDate;
        public $endSupportDate;

	/**
	 * Constructor.
	 * @param string username
	 * @param string password
	 * @param string vtiger host
	 */
	public function __construct($username,$password)
	{
		$this->username=$username;
		$this->password=$password;
	}

	/*
	 * Authenticates the user against database
	 * @return bool
	 */
	public function authenticate()
	{   
            $this->errorCode=0;
			$user=new User('search','Contacts');
            $found=$user->findByAttributes(array('username'=>$this->username,'password'=>$this->password));

            if($found===null||$found==false)
            {
            	$this->errorCode=self::ERROR_USERNAME_INVALID;
            }           
            else
            {
                if($this->lastLoginTime===null)
                {
                $lastLogin = time();
                }
                else
                {
                $lastLogin = strtotime($this->lastLoginTime);
                }
                //$lastLogin=User::model()->getLastLoginTime();
                $supportDates=$user->getSupportDates($this->username);
                $this->setState('lastLoginTime', $lastLogin);
                $this->setState('password', $this->password);
                $this->setState('username', $this->username);
                $this->setState("startSupportDate", $supportDates['startSupportDate']);
                $this->setState("endSupportDate", $supportDates['endSupportDate']);          
				// Create settings array
                $this->setState('settings', new UserSettingsManager($this->username));
                $this->errorCode=self::ERROR_NONE;
			}
			return !$this->errorCode;
	}
}