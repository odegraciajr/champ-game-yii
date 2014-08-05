<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//common/main';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	
	public function beforeRender( $view ){
		$baseURL = Yii::app()->request->getBaseUrl(true);
		
		Yii::app()->clientScript->registerMetaTag(Yii::app()->params['site_name'],null,null,array('property'=>'og:site_name'));
		
		$cs = Yii::app()->getClientScript();
		$cs->scriptMap['jquery.js'] = false;
		$cs->scriptMap['jquery.min.js'] = false;
		$cs->scriptMap['bootstrap.css']  = false;
		$cs->scriptMap['bootstrap.js']  = false;
		$cs->scriptMap['bootstrap.min.css'] = false;
		$cs->scriptMap['bootstrap-yii.css'] = false;
		
		$cs->registerCssFile( Yii::app()->helper->manage_assets("normalize.css", "css") );
		$cs->registerCssFile( Yii::app()->helper->manage_assets("main.css", "css") );
		$cs->registerScriptFile( 'https://code.jquery.com/jquery-1.11.1.min.js', CClientScript::POS_HEAD);
		$cs->registerScriptFile( Yii::app()->helper->manage_assets("modernizr.js", "js"), CClientScript::POS_HEAD);
		$popup_js = "\r" . "function PopupCenter(pageURL,title,w,h){var left=(screen.width/2)-(w/2);var top=(screen.height/2)-(h/2);var targetWin=window.open(pageURL,'','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);}";
		if( Yii::app()->user->isGuest ):
			$is_guest = "true";
		else:
			$is_guest = "false";
		endif;
		
		$popup_js .= "\r" . "var is_guest=" . $is_guest . ";";
		
		$cs->registerScript('custom_head', 'jQuery.noConflict();var $=jQuery;var site_url ="'. $baseURL .'";function get_rand_token(){return Math.random() * (Math.random() * 100000 * Math.random() );}' . $popup_js,CClientScript::POS_HEAD);
		
		$cs->registerScriptFile( Yii::app()->helper->manage_assets("common.js", "js"), CClientScript::POS_END);
		
		return true;
	}
}