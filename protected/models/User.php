<?php

/**
 * This is the model class for table "{{users}}".
 *
 * The followings are the available columns in table '{{users}}':
 * @property string $id
 * @property string $username
 * @property string $password
 * @property string $name
 * @property string $email
 * @property integer $gender
 * @property string $bio
 * @property string $headshot
 * @property integer $status
 * @property integer $role
 * @property string $birth_date
 * @property string $join_date
 * @property string $last_login
 */
class User extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{users}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email, password', 'required'),
			array('gender, status, role', 'numerical', 'integerOnly'=>true),
			array('username, name, email', 'length', 'max'=>100),
			array('password', 'length', 'max'=>60),
			array('bio', 'length', 'max'=>255),
			array('headshot, birth_date, join_date, last_login', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, username, password, name, email, gender, bio, headshot, status, role, birth_date, join_date, last_login', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'username' => 'Username',
			'password' => 'Password',
			'name' => 'Name',
			'email' => 'Email',
			'gender' => 'Gender',
			'bio' => 'Bio',
			'headshot' => 'Headshot',
			'status' => 'Status',
			'role' => 'Role',
			'birth_date' => 'Birth Date',
			'join_date' => 'Join Date',
			'last_login' => 'Last Login',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('gender',$this->gender);
		$criteria->compare('bio',$this->bio,true);
		$criteria->compare('headshot',$this->headshot,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('role',$this->role);
		$criteria->compare('birth_date',$this->birth_date,true);
		$criteria->compare('join_date',$this->join_date,true);
		$criteria->compare('last_login',$this->last_login,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function is_field_unique( $search = NULL, $field = 'email' )
	{
		return Yii::app()->db->createCommand("SELECT id FROM {{users}} WHERE $field='$search' LIMIT 1")->queryScalar();
	}
	
	public function create_user( $email, $password, $name ){
		
		if( $email && $password && $name ):
			
			$details = array(
				'name' => trim( $name ),
				'email' => trim( $email ),
				'password' => Yii::app()->helper->prepare_password( $password ),
			);
		
			$db = Yii::app()->db->createCommand();
			$db->insert( $this->tableName() , $details );
			
			return Yii::app()->db->getLastInsertId();
		
		else:
		
			return array('success' => false, 'error' => 'Please check your details.' );
		endif;
	}
}
