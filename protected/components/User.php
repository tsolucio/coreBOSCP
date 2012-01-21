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

class User extends CActiveRecord
{
	public $plainPassword;
        public $password_repeat;
        public $password;

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
			$recordInfo = $clientvtiger->doInvoke('authenticateContact',array('email'=>$email,'password'=>$password));
		}
		if(empty($recordInfo))
		return null;
		else
		return $this->populateRecord($recordInfo);
	}

        public function findByEmail($email)
	{
	$clientvtiger=$this->getClientVtiger();
        if(!$clientvtiger) Yii::log('login failed');
        else{
           $recordInfo = $clientvtiger->doQuery("Select * from Contacts where portal=1 and email='".$email."'");
        }
		return count($recordInfo);
	}

        public function getSupportDates($email)	
        {        
	$clientvtiger=$this->getClientVtiger();
        $res=array();        
        if(!$clientvtiger) Yii::log('login failed');
        else{
           $recordInfo = $clientvtiger->doQuery("Select * from Contacts where portal=1 and email='".$email."'");
           $res['startSupportDate']=$recordInfo[0]['support_start_date'];
           $res['endSupportDate']=$recordInfo[0]['support_end_date'];
        }
	return $res;
	}

        public function savePassword($pass)
        {
        $clientvtiger=$this->getClientVtiger();        
        $email=Yii::app()->user->name;        
        if(!$clientvtiger) Yii::log('login failed');
        else{
            $recordInfo = $clientvtiger->doInvoke('changePassword',array('email'=>$email,'password'=>$pass));
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

	public function getDomId()
	{
		return md5($this->getId());
	}

}