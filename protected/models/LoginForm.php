<?php
/*
 * Chive - web based MySQL database management
 * Copyright (C) 2010 Fusonic GmbH
 *
 * This file is part of Chive.
 *
 * Chive is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * Chive is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public
 * License along with this library. If not, see <http://www.gnu.org/licenses/>.
 */


class LoginForm extends CFormModel
{
	
	public $username;
	public $password;
	public $rememberMe;
	public $host = 'localhost';

	/**
	 * @see		CFormModel::rules();
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('username,password', 'required'),
			// password needs to be authenticated
			array('password', 'authenticate'),
                        array('username', 'email'),
		);
	}

	/**
	 * @see		CFormModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return array(
			'username'=>Yii::t('core','Contact Email'),
			'password'=>Yii::t('core','password'),
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$identity = new UserIdentity($this->username,$this->password);
			$authResult=$identity->authenticate();
			if($authResult==$identity::ERROR_NONE)
			{
				Yii::app()->user->login($identity);
			}
			else
			{
				switch ($authResult) {
					case $identity::ERROR_EXPIRED_SUPPORT:
						$errMsg=Yii::t('core','errExpiredSupport');
						break;
					case $identity::ERROR_PASSWORD_INVALID:
						$errMsg=Yii::t('core','errCheckCredentials');
						break;
					case $identity::ERROR_UNKNOWN_IDENTITY:
						$errMsg=Yii::t('core','errCheckCredentials');
						break;
					case $identity::ERROR_USERNAME_INVALID:
						$errMsg=Yii::t('core','errUserNotFound');
						break;
					default:
						$errMsg=Yii::t('core','errCheckCredentials');
				}
				$this->addError('username', "($authResult) $errMsg");
	}
			}
		}
		
}