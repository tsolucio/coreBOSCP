<?php
/**************************************************************************************************
 * Evolutivo vtyiiCPng - web based vtiger CRM Customer Portal
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

class UserIdentity extends CUserIdentity
{
		const ERROR_EXPIRED_SUPPORT=10;

        public $lastLoginTime;
        public $startSupportDate;
        public $endSupportDate;
        public $contactId;
        public $companyName;
        public $accountId;
        public $userId;

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
            $contactid=$user->findByAttributes(array('username'=>$this->username,'password'=>$this->password));

            if($contactid===null||$contactid==false)
            {
            	$this->errorCode=self::ERROR_USERNAME_INVALID;
            }           
            else
            {
                $supportDates=$user->getSupportDates($contactid);
                if ($supportDates['endSupportDate']<date('Y-m-d')) {
                	$this->errorCode=self::ERROR_EXPIRED_SUPPORT;
                } else {
                if($this->lastLoginTime===null)
                {
                $lastLogin = time();
                }
                else
                {
                $lastLogin = strtotime($this->lastLoginTime);
                }
                //$lastLogin=User::model()->getLastLoginTime();
                $this->setState('lastLoginTime', $lastLogin);
                $this->setState('password', $this->password);
                $this->setState('username', $this->username);
                $this->setState('userId', $user->userId);
                $this->setState('contactId', $contactid);
                $accinfo=$user->getAccountInfo($contactid);
                $this->setState('accountId', $accinfo['accountid']);
                $this->setState('companyName', $accinfo['accountname']);
                $this->setState("startSupportDate", $supportDates['startSupportDate']);
                $this->setState("endSupportDate", $supportDates['endSupportDate']);
				// Create settings array
                $this->setState('settings', new UserSettingsManager($this->username));
                $this->errorCode=self::ERROR_NONE;
                }
			}
			return $this->errorCode;
	}
}