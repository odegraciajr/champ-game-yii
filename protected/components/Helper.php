<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Helper
{
	public $cache_folder;
	public $remote_folder;
	public $FBAPI;
	
	public function init()
	{
		//$this->cache_folder = '/home/odegraciajr/webapps/pinoyhumors/media/';
		//$this->cache_folder_thumb = '/home/odegraciajr/webapps/pinoyhumors/media/thumb/';
		//$this->remote_folder = $this->cache_folder . 'remote/';
	}
	
	public function manage_assets( $filename, $type, $version=NULL )
	{
		$base = Yii::app()->request->baseUrl;
		$assets_folder = "/assets-dev/";
		return $base . $assets_folder . $type . "/" . $filename;
	}
	
	public function str_to_link($str)
	{
		if( strlen( trim( $str ) ) == 0 )
			return "";
			
		if($str !== mb_convert_encoding( mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32') )
			$str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str));
		$str = htmlentities($str, ENT_NOQUOTES, 'UTF-8');
		$str = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\\1', $str);
		$str = html_entity_decode($str, ENT_NOQUOTES, 'UTF-8');
		$str = preg_replace(array('`[^a-z0-9]`i','`[-]+`'), '-', $str);
		$str = strtolower( trim($str, '-') );
		return $str;
	}
	
	public function sanitize_title( $string )
	{
		$title = filter_var( utf8_encode( $string ) , FILTER_SANITIZE_STRING );
		if( 0 == strlen( trim( $title ) ) ):
			return "Untitled entry";
		endif;
		return addslashes( $title );
	}
	
	public function ph_htmldecode( $string ){
		$string = mb_convert_encoding($string, "utf-8", "HTML-ENTITIES" );
		$string = htmlspecialchars_decode( utf8_encode( $string) );
		return ( stripslashes( $string ) );
	}
	
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
	
	public function _to_json( $result, $htmldecode = true )
	{
		header("Content-type: application/json");
		if( $htmldecode ):
			echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
		else:
			echo json_encode($result);
		endif;
		
	}
	
	public function check_remote_file_exist( $url, $filter = array("gif","png","jpg","jpeg") )
	{
		$check_url = $this->clean_urls( $url );
		
		if ( in_array( end( explode( ".", strtolower( $check_url ) ) ) , $filter ) ):
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$check_url);
			curl_setopt($ch, CURLOPT_NOBODY, 1);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$lastUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
			
			if( curl_exec($ch)!==FALSE && $check_url == $lastUrl ):
				return true;
			else:
				return false;
			endif;
		else:
			return false;
		endif;
		
	}
	
	public function clean_urls( $url )
	{
		$url = trim( $url );
		$parts = explode( '?', $url );
		return $parts[0];
	}
	
	public function register_scrips( $param = NULL )
	{
		
	}
	
	public function t_elapsed( $ptime ){
	
		if( !ctype_digit( $ptime ) ):
			$ptime = date('U', strtotime( $ptime ) );
		endif;
		
		$etime = time() - $ptime;
		
		if ($etime < 1) {
			return '0 seconds';
		}
		
		$a = array( 12 * 30 * 24 * 60 * 60  =>  'year',
					30 * 24 * 60 * 60       =>  'month',
					24 * 60 * 60            =>  'day',
					60 * 60                 =>  'hour',
					60                      =>  'minute',
					1                       =>  'second'
					);
		
		foreach ($a as $secs => $str) {
			$d = $etime / $secs;
			if ($d >= 1) {
				$r = round($d);
				return $r . ' ' . $str . ($r > 1 ? 's' : '');
			}
		}
	}
	
	public function build_from_id_to_elem( $elem_string, $ids=NULL, $delimeter="," ){
	
		if( is_array( $ids ) && count( $ids ) > 0 ){
			$elem = "";
			foreach( $ids as $id ){
				$elem .= $elem_string . $id . $delimeter;
			}
			$elem = rtrim( $elem, $delimeter);
			return $elem;
		}
		return "";
	}
	
	public function get_mod_info( $user_id ){
		$mods = array(
			1 => array( "name" => "Oscar De Gracia Jr.", "role" => "Super Duper Admin"  ),
			8 => array( "name" => "Cha Reyes", "role" => "Admin"  ),
		);
		return $mods[$user_id];
	}
	
	public function prepare_password( $password )
	{
		$options = [
			'salt' => $this->generate_salt_iv(22),
			'cost' => 12
		];
		
		return password_hash($password, PASSWORD_DEFAULT, $options);
	}
	
	protected function generate_salt_iv( $length = 22 ){
		return mcrypt_create_iv( $length, MCRYPT_DEV_RANDOM );
	}
}