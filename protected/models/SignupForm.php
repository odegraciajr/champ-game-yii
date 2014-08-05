<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class SignupForm extends CFormModel
{
	public $name;
	public $username;
	public $password;
	public $password2;
	public $email;
	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			array('name, email, password, password2', 'required'),
			array('email', 'email'),
            array('email', 'unique', 'className' => 'User', 'message' => 'This email is already in use.'),
			array('password', 'length' ,'min' => 6, 'message'=> 'Password minimum length is 6'),
			array('password', 'compare', 'compareAttribute'=>'password2'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'password2'=>'Confirm',
		);
	}

	
	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function signup()
	{
		
	}
}
