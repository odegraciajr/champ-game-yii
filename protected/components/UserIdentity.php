<?php
class UserIdentity extends CUserIdentity
{
	private $_id;
	private $_user_id;
	
	public function __construct( $user_id ) {
		$this->_user_id = $user_id;
	}
	
    /* public function authenticate()
    {
        $user=User::model()->findByAttributes( array('username'=>$this->username ) );
        if($user===null)
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        else if($user->password !== md5( $this->password . Yii::app()->params["salt"] ) )
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        else
        {
			
            $this->_id=$user->id;

			$user=User::model()->findByPk($user->id);
			$lastLogin = $user->lastLoginTime;
			$current_time = date("Y-m-d");
			$user->lastLoginTime = "$current_time";
			$user->save();
			
			$this->setState( 'lastLoginTime', $lastLogin );
			$this->errorCode=self::ERROR_NONE;
        }
        return !$this->errorCode;
    } */
	
	public function authenticate()
    {
		$user = User::model()->findByPk( $this->_user_id );
		
		if( $user->id != $this->_user_id ):
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		else:
			$this->_id = $user->id;
			Yii::app()->session['headshot'] = $user->headshot;
			$this->errorCode = self::ERROR_NONE;
		endif;
		
		return $this->errorCode == self::ERROR_NONE;
	}
 
    public function getId()
    {
        return $this->_id;
    }
}