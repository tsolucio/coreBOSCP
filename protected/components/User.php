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

class User extends CActiveRecord
{
	public $plainPassword;
        public $password_repeat;
        public $password;
        public $userId;

	public static function splitId($id)
	{
		if(preg_match('/(.*)@(.*)$/', base64_decode($id), $res))
		{
			return array(
				'User' => $res[1],
				'Host' => $res[2],
			);
		}
		else
		{
			return null;
		}
	}

	/**
	 * @see		ActiveRecord::model()
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @see		ActiveRecord::tableName()
	 */
	public function tableName()
	{
		return 'user';
	}

	/**
	 * @see		ActiveRecord::primaryKey()
	 */
	public function primaryKey() {
		return array(
			'Host',
			'User',
		);
	}
        
	public function findByAttributes($attributes,$condition='',$params=array())
	{
		$clientvtiger=$this->getClientVtiger();
		$email=$attributes['username'];
		$password=$attributes['password'];
		 
		if(!$clientvtiger) {
			Yii::log('login failed');
			$recordInfo=0;
		} else {
			$this->userId = $clientvtiger->_userid;  // save this for certain functionality in the model
			$recordInfo = $clientvtiger->doInvoke('authenticateContact',array('email'=>$email,'password'=>$password));
		}
		if(empty($recordInfo) || !$recordInfo)
		return null;
		else return $recordInfo;
	}

        public function findByEmail($email)
	{
	$clientvtiger=$this->getClientVtiger();
        if(!$clientvtiger) Yii::log('login failed');
        else{
           $recordInfo = $clientvtiger->doQuery("Select portal from Contacts where portal=1 and email='".$email."'");
        }
        $count=count($recordInfo);
        if($count==1) $this->User=$email;
	return $count;
	}

	public function findByPortalUserName($username) {
		$found=false;
		$clientvtiger=$this->getClientVtiger();
		if(!$clientvtiger) Yii::log('login failed');
		else{
			$recordInfo = $clientvtiger->doInvoke('findByPortalUserName',array('username'=>$username));
			if ($recordInfo) {
				$found=true;
			}
		}
		if($found) $this->User=$username;
		return $found;
	}

	public function sendRecoverPassword($username) {
		$sent=false;
		$clientvtiger=$this->getClientVtiger();
		if(!$clientvtiger) Yii::log('login failed');
		else{
			$recordInfo = $clientvtiger->doInvoke('sendRecoverPassword',array('username'=>$username));
			if ($recordInfo) {
				$sent=true;
			}
		}
		return $sent;
	}

        public function getSupportDates($contactid)	
        {        
	$clientvtiger=$this->getClientVtiger();
        $res=array();        
        if(!$clientvtiger) Yii::log('login failed');
        else{
           $recordInfo = $clientvtiger->doQuery("Select support_start_date,support_end_date from Contacts where portal=1 and id='$contactid'");
           if (is_array($recordInfo)) {
           $res['startSupportDate']=$recordInfo[0]['support_start_date'];
           $res['endSupportDate']=$recordInfo[0]['support_end_date'];
           } else {
           $res['startSupportDate']='support_start_date';
           $res['endSupportDate']='support_end_date';
           }
        }
	return $res;
	}

	public function getUserDateFormat()
	{
		$res = 'yyyy-mm-dd';
		$clientvtiger=$this->getClientVtiger();
		if(!$clientvtiger) Yii::log('login failed');
		else{
			//$recordInfo = $clientvtiger->doQuery("select date_format from users where id='".$this->userId."'");
			$recordInfo = $clientvtiger->doInvoke('getPortalUserDateFormat',array());
			if (is_array($recordInfo)) {
				$res = $recordInfo['result'];
			}
		}
		return $res;
	}

	public function getAccountInfo($contactid)
	{
		$accid='1x1'; // for vtiger CRM REST to work correctly we must assign an artificial number, we cannot use the empty string
		$accname=yii::t('core', 'none');
		$clientvtiger=$this->getClientVtiger();
		if(!$clientvtiger) Yii::log('login failed');
		else{
			$recordInfo = $clientvtiger->doQuery("Select account_id from Contacts where id='".$contactid."'");
			if (is_array($recordInfo)) {
				$accid=(empty($recordInfo[0]['account_id']) ? $accid : $recordInfo[0]['account_id']);
				$recordInfo = $clientvtiger->doQuery("Select accountname from Accounts where id='".$accid."'");
				if (is_array($recordInfo)) {
					$accname=$recordInfo[0]['accountname'];
				}
			}
		}
		$res=array(
				'accountid'=>$accid,
				'accountname'=>$accname,
				);
		return $res;
	}

        public function savePassword($pass)
        {
        $clientvtiger=$this->getClientVtiger();
        $email=isset(Yii::app()->user->name)?Yii::app()->user->name:$this->User;
        if(!$clientvtiger) Yii::log('login failed');
        else{
            $recordInfo = $clientvtiger->doInvoke('changePortalUserPassword',array('email'=>$email,'password'=>$pass));
            }            
        
	return $recordInfo;
        }

//        public function getLastLoginTime()
//
//        {
//	Yii::import('application.components.vtwsclib.Vtiger.WSClient.php');
//        $email=Yii::app()->user->name;;
//        $url = Yii::app()->site;
//        $clientvtiger = new WSClient($url);
//        $login = $clientvtiger->doLogin(Yii::app()->loginuser, Yii::app()->accesskey);
//        if(!$login) Yii::log('login failed');
//        else{
//           $res=array();
//           $recordInfo = $clientvtiger->doQuery("Select * from Contacts where portal=1 and email='".$email."'");
//        }
//        $res['startSupportDate']=$recordInfo[0]['support_start_date'];
//        $res['endSupportDate']=$recordInfo[0]['support_end_date'];
//
//	return $res;
//	}
	/**
	 * @see		ActiveRecord::rules()
	 */
	public function rules()
	{
		return array(
			array('User', 'type', 'type' => 'string'),
			array('Host', 'type', 'type' => 'string'),
			array('plainPassword', 'type', 'type' => 'string'),
                        array('Password', 'compare'),
                        array('Password,password_repeat', 'required'),

		);
	}

	/**
	 * @see		ActiveRecord::attributeLabels()
	 */
	public function attributeLabels()
	{
		return array(
			'User' => Yii::t('core', 'username'),
			'Password' => Yii::t('core', 'password'),
                        'password_repeat' => Yii::t('core', 'Repeat Password'),
			'plainPassword' => Yii::t('core', 'password'),                       
		);
	}
        public function getAttributeLabel($attribute)
    {
            $attributes=$this->attributeLabels();
            return  $attributes[$attribute] ;
    }

	public function getId()
	{
		return base64_encode($this->User . '@' . $this->Host);
	}

}
