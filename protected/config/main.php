<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Championology',
	'defaultController' => 'site',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'janus52687',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'class' => 'WebUser',
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		'helper'=>array(
			// enable cookie-based authentication
			'class' => 'Helper',
		),
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName' => false,
			'rules' => array(
				'' => 'site/index',
				'<action:(login|logout|signup)>' => 'site/<action>',
				'site/login' => '/login',
				'site/logout' => '/logout',
				'site/signup' => '/signup',
			),
		),
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=champ_db',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'tablePrefix' => 'chp_',
			'charset' => 'utf8',
		),
		'authManager'=>array(
            'class'=>'CDbAuthManager',
            'connectionID'=>'db',
        ),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'odegraciajr@gmail.com',
		'salt' => 'uQsuVHsL9kmHJ4y',
		'hybrid' => '',
		'fb_app_id' => '264706393736105',
		'fb_app_secret' => '6f317f1bce69beeb1c880b66bdca4595',
		'fb_scope' => 'email,user_about_me,user_birthday,read_stream,publish_stream,read_friendlists,publish_actions',
		'site_name' => 'Championology',
		'ph_fb_page_id' => 0,
	),
);