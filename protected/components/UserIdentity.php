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
        public $userDateFormat;

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
                $this->setState('userDateFormat', $user->getUserDateFormat());
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