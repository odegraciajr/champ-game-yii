<?php

class AuthController extends Controller
{
	public $adapter;
	public $loginURL = "";
	public $scope;
	public $callback_url, $fb_id, $fb_secret;
	private $loginDuration;
	public $fb_token;
	
	public function actionIndex()
	{
		$this->render('index');
	}
	public function actionFacebook()
	{
		$this->layout = "fbconnect";
		$this->render('authview',array('redirect' => $this->getLoginUrl() ) );
	}
	public function getLoginUrl()
	{
		$app_id = Yii::app()->params['fb_app_id'];
		$secret = Yii::app()->params['fb_app_secret'];
		$link = "https://www.facebook.com/dialog/oauth?client_id=$app_id&redirect_uri={$this->callback_url}&scope=". $this->scope;
		return  $link;
	}

	public function filters()
	{
		$this->callback_url = urlencode( Yii::app()->request->getBaseUrl(true) . '/auth/process' );
		$this->fb_id = Yii::app()->params['fb_app_id'];
		$this->fb_secret = Yii::app()->params['fb_app_secret'];
		$this->scope = Yii::app()->params['fb_scope'];
		$this->loginDuration = 0;
		$this->fb_token = NULL;
	}
	//https://graph.facebook.com/me/?access_token=AQA-yBMoVQ9rEMKIblFEJHIezjNbJeKBlswpJZvKs7PkrpkZ_ss6NnMO1K_IKuOnUFhtx3pqZ1X4kc1WPPHIbQbfXaEZ3HtKjKQ_WamdH-56oqgjbpGZC_bXrhUoOD1MmDA6qX_fj16iU3FLajUTOPwwFvLF1M7t8bxFlgBTryirXOv5rJL_ZytbGw9_isbWIurRbwaRDwOYMrhPUjVlQB8R3nIvuTgnTx45otoSvUIO6S7omadb7hvaxUjNIbaGAHBU167DlhsgCHKLR-XKxrD0YLTfiFDHN-eggvJ9SZm_WvWLJSbAkT93bviLsFm8dVI
	public function actionProcess()
	{
		$this->layout = "fbconnect";
		if( !empty( $_GET['code'] ) && ( empty( $_GET['error'] ) && empty( $_GET['error_code'] ) && empty( $_GET['error_msg'] ) ) ):
		
			$code = $_GET['code'];

			$process_url = 'https://graph.facebook.com/oauth/access_token?client_id='. $this->fb_id;
			$process_url .= '&redirect_uri='. $this->callback_url;
			$process_url .= '&client_secret='. $this->fb_secret;
			$process_url .= '&code='. $code;

			//$data = $this->phCurl( $process_url );
			
			//$access_token = $this->fb_endpoint( $data );
			
			echo "<code>";
			echo($process_url);
			echo "</code>";
		endif;
	}
	
	
	//TOOLS
	
	public function phCurl( $url ,$json = false){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);

		$results = curl_exec($ch);

		curl_close($ch);
		
		if( $json ):
			return json_decode( $results );
		else:
			return $results;
		endif;
	}
	
	public function process_token( $token )
	{
		$at = array();
		$t = $token;
		$access = explode( "&", str_replace( "access_token=", "", $t ) );
		$at['token'] = $access[0];
		$at['expiry'] = intval( str_replace( "expires=", "", $access[1] ) );
		
		return $at;
	}
	
	public function fb_endpoint( $token )
	{
		$app_id = Yii::app()->params['fb_app_id'];
		$secret = Yii::app()->params['fb_app_secret'];
		
		//$first = $this->process_token( $token );
		$url = "https://graph.facebook.com/oauth/access_token?";        
		$url .= "client_id=$app_id&";
		$url .= "client_secret=$secret&";
		$url .= "grant_type=fb_exchange_token&";
		$url .= "fb_exchange_token=CAADwv8KRg6kBABr4IyG9jg8ZAOCZCOZBWn7acY6mmAsswR5ZAowcud7CbIEJrOlQwa3Isfaut08pupmvRCl6wZCnhZAtZBtPTQoamxyVpasN3QMHAffXS1ZCuuA69gMSt5HCWScLgroHlwiWyF8cfQgkDG15IcHsVzLA1QUCrng6CFWJnuQuyyC2yrAtdXcQLJTIkLBgcHBUdZA8ggo8svo0c";
		
		//$url = "https://graph.facebook.com/oauth/access_token?";        
		//$url .= "client_id={$this->fb_id}&";
		//$url .= "client_secret={$this->secret}&";
		//$url .= "grant_type=fb_exchange_token&";
		//$url .= "fb_exchange_token="; //. //$first['token'];
		return $url;
		//return $this->process_token( $this->phCurl($url) );
		
		//CAADwv8KRg6kBABr4IyG9jg8ZAOCZCOZBWn7acY6mmAsswR5ZAowcud7CbIEJrOlQwa3Isfaut08pupmvRCl6wZCnhZAtZBtPTQoamxyVpasN3QMHAffXS1ZCuuA69gMSt5HCWScLgroHlwiWyF8cfQgkDG15IcHsVzLA1QUCrng6CFWJnuQuyyC2yrAtdXcQLJTIkLBgcHBUdZA8ggo8svo0c
	}
	
	
	
}