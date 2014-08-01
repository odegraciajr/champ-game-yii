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
		$this->cache_folder = '/home/odegraciajr/webapps/pinoyhumors/media/';
		$this->cache_folder_thumb = '/home/odegraciajr/webapps/pinoyhumors/media/thumb/';
		$this->remote_folder = $this->cache_folder . 'remote/';
	}
	
	public function load_fbapi()
	{
		require_once "/home/odegraciajr/yiifw/pinoyhumors/protected/data/fbapi/facebook.php";
		
		$this->FBAPI = new Facebook(array(
			'appId'  => Yii::app()->params['fb_app_id'],
			'secret' => Yii::app()->params['fb_app_secret']
		));
	}
	 
	public function post_to_fb( $link, $title, $token=NULL )
	{
		$this->load_fbapi();
		
		if( !$token ):
			$token = Yii::app()->user->get_user_token();
		endif;
		
		$this->FBAPI->setAccessToken( $token );
		
		return $this->FBAPI->api('/me/feed', 'POST',
					array(
					  'link' => $link,
					  'message' => $title . "\r\n" . $link
				 ));
	}
	
	public function like_fb_url( $url )
	{
		$this->load_fbapi();
		
		if( !$token ):
			$token = Yii::app()->user->get_user_token();
		endif;
		
		$this->FBAPI->setAccessToken( $token );
							
		if( !empty( $url ) ):
			$this->FBAPI->api('me/og.likes','POST',	array('object' => "$url"));
		endif;
				
	}
	
	public function post_photo_to_page( $post_id, $title, $raw_image ){
		$page_id = 304690342964901;
		$this->load_fbapi();
		
		if( !$token ):
			$token = Yii::app()->user->get_user_token();
		endif;
		
		$this->FBAPI->setAccessToken( $token );
		
		$this->FBAPI->setFileUploadSupport(true);
		
		$args = array(
			'access_token'  => Yii::app()->user->get_user_token(),
			'name' => $title . "\n" . $this->get_permalink( $post_id ),
			'image' => '@' . realpath("/home/odegraciajr/webapps/pinoyhumors" . $raw_image)
		);
		
		$res = $this->FBAPI->api("/$page_id/photos","post",$args);
		
		//echo "<pre>";
		//print_r($res);
		//echo "</pre>";
	}
	
	public function get_permalink( $id, $route = '/p/' )
	{
		if( $id )
			return Yii::app()->request->getBaseUrl(true) . $route . $id;
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
		
		$first = $this->process_token( $token );
		
		$url = "https://graph.facebook.com/oauth/access_token?";        
		$url .= "client_id=$app_id&";
		$url .= "client_secret=$secret&";
		$url .= "grant_type=fb_exchange_token&";
		$url .= "fb_exchange_token=" . $first['token'];
		
		return $this->process_token( $this->phCurl($url) );
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
		//$headers = curl_getinfo($ch);
		//$error_number = curl_errno($ch);
		//$error_message = curl_error($ch);

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
	
	public function uploaderAjax()
	{
		//require_once("/home/odegraciajr/yiifw/pinoyhumors/protected/data/upload.php");
		require_once("/home/odegraciajr/yiifw/pinoyhumors/protected/data/fineuploader.php");
		
		$allowedExtensions = array('jpeg', 'jpg', 'gif', 'png');
		
		$sizeLimit = 2097152;
		
		$inputName = 'qqfile';
		
		$up = new qqFileUploader($allowedExtensions, $sizeLimit, $inputName);

		return $up->handleUpload('/home/odegraciajr/mh_library/temp_ph/');
		
	}
	
	public function social_flat($post_id = 0, $title_raw = "", $thumb = "", $likes = 0)
	{
		$url = $this->get_permalink( $post_id );
		$url_encoded = urlencode( $url );
		$title = urlencode( $title_raw );
		$media = urlencode(Yii::app()->request->getBaseUrl(true) . $thumb );
		$subject = urlencode( 'Check out "'. $title_raw .'"' );
		$body = urlencode( "Funny post from Pinoy Humors" ) . "%0A" . $title . "%0A" . $url_encoded;
		$fb_link = "https://www.facebook.com/sharer/sharer.php?u=" . $url_encoded;
		$tw_link = "https://twitter.com/intent/tweet?original_referer=$url_encoded&source=tweetbutton&text=" . $title . "&url=" . $url_encoded . "&via=pinoyhumors";
		$gplus_link = "https://plus.google.com/share?url=$url_encoded";
		$pin_link = "http://pinterest.com/pin/create/button/?url=$url_encoded&media=$media&description=$title";
		$email_link = "mailto:?subject=$subject&body=$body";
		$likes = $likes ? $likes : 0;
		?>
			<a rel="nofollow" title="Share this on Facebook" data-share-url="<?php echo $fb_link;?>" href="#" id="fb_social" class="social fb"><i class="icon"></i><span>Share</span></a>
			<a rel="nofollow" title="Post this on Twitter" data-share-url="<?php echo $tw_link;?>" href="#" id="tweet_social" class="social tweet"><i class="icon"></i><span>Tweet</span></a>
			<!--<a rel="nofollow" title="Post this on Google+" data-share-url="<?php echo $gplus_link;?>" href="#" id="gplus_social" class="social gplus"><i class="icon"></i><span>Google+</span></a>-->
			<a rel="nofollow" title="Pin this on Pinterest" data-share-url="<?php echo $pin_link;?>" href="#" id="pin_social" class="social pin"><i class="icon"></i><span>Pin it</span></a>
			<a rel="nofollow" title="Email this post to a friend" href="<?php echo $email_link;?>" id="email_social" class="social email"><i class="icon"></i><span>Email</span></a>
			<a rel="nofollow" title="Like this!" href="#" id="like_social" data-postid="<?php echo $post_id;?>" class="social like"><i class="icon"></i><span>Like(<?php echo $likes;?>)</span></a>
			<span class="fb_like_official">
				<div class="fb-like" data-send="false" data-layout="button_count" data-width="60" data-show-faces="false" data-font="arial"></div>
			</span>
		<?php	
	}
	
	public function create_post_img()
	{
		
	}
	
	public function resize_img($imagePath,$opts=null){
	
		$imagePath = urldecode($imagePath);
		# start configuration
		$cacheFolder = $this->cache_folder;
		$remoteFolder = $this->remote_folder;
		$remote_file = null;

		$defaults = array('crop' => true, 'scale' => false, 'thumbnail' => false, 'maxOnly' => false, 
		   'canvas-color' => 'transparent', 'output-filename' => false, 
		   'cacheFolder' => $cacheFolder, 'remoteFolder' => $remoteFolder, 'quality' => 95, 'cache_http_minutes' => 5);

		$opts = array_merge($defaults, $opts);

		if( isset( $opts['thumb'] ) && $opts['thumb'] ):
			$cacheFolder = $this->cache_folder_thumb;
		else:
			$cacheFolder = $opts['cacheFolder'];
		endif;
		
		$remoteFolder = $opts['remoteFolder'];

		$path_to_convert = 'convert'; # this could be something like /usr/bin/convert or /opt/local/share/bin/convert
		
		## you shouldn't need to configure anything else beyond this point

		$purl = parse_url($imagePath);
		$finfo = pathinfo($imagePath);
		$ext = strtolower($finfo['extension']);

		# check for remote image..
		if(isset($purl['scheme']) && ($purl['scheme'] == 'http' || $purl['scheme'] == 'https')):
			# grab the image, and cache it so we have something to work with..
			list($filename) = explode('?',$finfo['basename']);
			$local_filepath = $remoteFolder.$filename;
			$remote_file = $remoteFolder.$filename;//just to make sure remote file will be deleted.
			$download_image = true;
			if(file_exists($local_filepath)):
				if(filemtime($local_filepath) < strtotime('+'.$opts['cache_http_minutes'].' minutes')):
					$download_image = false;
				endif;
			endif;
			if($download_image == true):
				$img = $this->phCurl($imagePath);
				file_put_contents($local_filepath,$img);
			endif;
			$imagePath = $local_filepath;
		endif;

		if(file_exists($imagePath) == false):
			$imagePath = $_SERVER['DOCUMENT_ROOT'].$imagePath;
			if(file_exists($imagePath) == false):
				return 'image not found';
			endif;
		endif;

		if(isset($opts['w'])): $w = $opts['w']; endif;
		if(isset($opts['h'])): $h = $opts['h']; endif;

		if( isset( $opts['custom_filename'] ) ):
			$filename = $opts['custom_filename'];
		else:
			$filename = md5_file($imagePath);
		endif;
		
		if( $ext == "png" && isset( $opts['convert_png'] ) ){
			$oldPath = $imagePath;
			$imagePath = $this->png_to_jpg($oldPath);
			$finfo = pathinfo($imagePath);
			$ext = $finfo['extension'];
			@unlink( $oldPath );
		}
			
		// If the user has requested an explicit output-filename, do not use the cache directory.
		if(false !== $opts['output-filename']) :
			$newPath = $opts['output-filename'];
		else:
			if(!empty($w) and !empty($h)):
				if( isset( $opts['custom_filename'] ) ):
					$newPath = $cacheFolder.$filename . '.' . $ext;	
				else:
					$newPath = $cacheFolder.$filename.'_w'.$w.'_h'.$h.(isset($opts['crop']) && $opts['crop'] == true ? "_cp" : "").(isset($opts['scale']) && $opts['scale'] == true ? "_sc" : "").'.'.$ext;
				endif;
			elseif(!empty($w)):
				if( isset( $opts['custom_filename'] ) ):
					$newPath = $cacheFolder.$filename . '.' . $ext;
				else:
					$newPath = $cacheFolder.$filename.'_w'.$w.'.'.$ext;
				endif;
			elseif(!empty($h)):
				$newPath = $cacheFolder.$filename.'_h'.$h.'.'.$ext;
			else:
				return false;
			endif;
		endif;

		$create = true;

		if(file_exists($newPath) == true):
			$create = false;
			$origFileTime = date("YmdHis",filemtime($imagePath));
			$newFileTime = date("YmdHis",filemtime($newPath));
			if($newFileTime < $origFileTime): # Not using $opts['expire-time'] ??
				$create = true;
			endif;
		endif;
		
		$gif_create = true;
		
		if( $ext == "gif" ):
			if( isset( $opts['thumb'] ) && $opts['thumb'] ):
				$gif_create = true;
			else:
				$gif_create = false;
			endif;
		endif;

		if( $create == true && $gif_create == true ):
			if(!empty($w) and !empty($h)):

				list($width,$height) = $this->getimagesizeV2($imagePath);
				$resize = $w;
			
				if($width > $height):
					$resize = $w;
					if(true === $opts['crop']):
						$resize = "x".$h;				
					endif;
				else:
					$resize = "x".$h;
					if(true === $opts['crop']):
						$resize = $w;
					endif;
				endif;

				if(true === $opts['scale']):
					$cmd = $path_to_convert ." ". escapeshellarg($imagePath) ." -resize ". escapeshellarg($resize) . 
					" -quality ". escapeshellarg($opts['quality']) . " " . escapeshellarg($newPath);
				else:
					$cmd = $path_to_convert." ". escapeshellarg($imagePath) ." -resize ". escapeshellarg($resize) . 
					" -size ". escapeshellarg($w ."x". $h) . 
					" xc:". escapeshellarg($opts['canvas-color']) .
					" +swap -gravity center -composite -quality ". escapeshellarg($opts['quality'])." ".escapeshellarg($newPath);
				endif;
							
			else:
				$cmd = $path_to_convert." " . escapeshellarg($imagePath) . 
				" -thumbnail ". (!empty($h) ? 'x':'') . $w ."". 
				(isset($opts['maxOnly']) && $opts['maxOnly'] == true ? "\>" : "") . 
				" -quality ". escapeshellarg($opts['quality']) ." ". escapeshellarg($newPath);
			endif;

			$c = exec($cmd, $output, $return_code);
			if($return_code != 0):
				error_log("Tried to execute : $cmd, return code: $return_code, output: " . print_r($output, true));
				if( $remote_file ) @unlink( $remote_file );
				
				return false;
			endif;
		else:
			@copy($imagePath, $newPath);
		endif;

		# return cache file path
		if( isset( $opts['more_infos'] ) && $opts['more_infos'] == true ):
			if( !$width || !$height ):
				list($width,$height) = $this->getimagesizeV2( $newPath );
			endif;
			if( $remote_file ) @unlink( $remote_file );
			
			return array( 'filepath' => str_replace($_SERVER['DOCUMENT_ROOT'],'',$newPath), 'local_filepath' => $newPath,
							'filename' => $filename, 'ext' => $ext, 'width' => $width, 'height'=> $height );
		else:
			if( $remote_file ) @unlink( $remote_file );
			
			return str_replace($_SERVER['DOCUMENT_ROOT'],'',$newPath);
		endif;
		
		
	}
	
	protected function png_to_jpg( $filePath, $quality = 100 ){
		$image = imagecreatefrompng($filePath);
		$bg = imagecreatetruecolor(imagesx($image), imagesy($image));
		imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
		imagealphablending($bg, TRUE);
		imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
		imagedestroy($image);
		imagejpeg($bg, $filePath . ".jpg", $quality);
		imagedestroy($bg);
		return $filePath . ".jpg";
	}
	
	protected function getimagesizeV2( $url ){
		$size = array();
		$image = ImageCreateFromString( file_get_contents( $url ) );
		$size[0] = ImageSX($image);
		$size[1] = ImageSY($image);

		return $size;
	}
	
	public function register_scrips( $param = NULL )
	{
		$user = Yii::app()->user;
		$baseURL = Yii::app()->request->getBaseUrl(true);
		$assetsPath = Yii::getPathOfAlias('bootstrap.assets');
		$BootAssetsUrl = $baseURL . Yii::app()->assetManager->publish($assetsPath, false, -1, YII_DEBUG);
			
		$cs = Yii::app()->getClientScript();
		
		if( $param == "show_sticky" ):
			$cs->registerScriptFile( 'http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.8.3.min.js', CClientScript::POS_HEAD);
			$cs->registerScriptFile( Yii::app()->request->getBaseUrl(true) .'/js/jquery.stickem.js', CClientScript::POS_HEAD);
		else:
			$cs->registerScriptFile( 'http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.8.3.min.js', CClientScript::POS_HEAD);
		endif;
		
		
		
		$cs->scriptMap['jquery.js'] = false;
		$cs->scriptMap['jquery.min.js'] = false;
		$cs->scriptMap['bootstrap.css']  = false;
		$cs->scriptMap['bootstrap.js']  = false;
		$cs->scriptMap['bootstrap.min.css'] = false;
		//$cs->scriptMap['bootstrap.min.js']  = false;
		$cs->scriptMap['bootstrap-yii.css'] = false;
		
		//$cs2 = Yii::app()->clientScript;
		$cs->registerMetaTag(Yii::app()->params['keywords'], 'keywords');
		//$cs->registerMetaTag('noindex,nofollow', 'robots');
		$cs->registerMetaTag(Yii::app()->params['fb_app_id'],null,null,array('property'=>'fb:app_id'));
		$cs->registerMetaTag('1577566262',null,null,array('property'=>'fb:admins'));
		
		$page = isset( Yii::app()->getRequest()->pathInfo ) ? Yii::app()->getRequest()->pathInfo : 0;
		
		if( $page == "login" ):
			$cs->registerCssFile( $baseURL . '/css/auth.css' );
		else:
			$cs->registerCssFile( $baseURL . '/css/common.css' );
			$cs->registerCssFile( $BootAssetsUrl . '/css/bs.mini.css' );
		endif;
		
		$popup_js = "\r" . "function PopupCenter(pageURL,title,w,h){var left=(screen.width/2)-(w/2);var top=(screen.height/2)-(h/2);var targetWin=window.open(pageURL,'','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);}";
		if( $user->isGuest ):
			$is_guest = "true";
		else:
			$is_guest = "false";
		endif;
		
		$popup_js .= "\r" . "var is_guest=" . $is_guest . ";";
		$cs->registerScript('custom_head', 'jQuery.noConflict();var $=jQuery;var site_url ="'. $baseURL .'";function get_rand_token(){return Math.random() * (Math.random() * 100000 * Math.random() );}' . $popup_js,CClientScript::POS_HEAD);
		//$cs->registerScriptFile( $baseURL .'/js/lazy.mini.js', CClientScript::POS_HEAD);
		$cs->registerScriptFile( $BootAssetsUrl.'/js/bootstrap.min.js', CClientScript::POS_END);

		if( $page == "upload" ):
			$cs->registerScriptFile( $baseURL.'/js/jquery.fineuploader-3.1.1.min.js', CClientScript::POS_HEAD);
			$cs->registerScriptFile( $baseURL.'/js/upload.js', CClientScript::POS_END);
		endif;
		
		//die($page);
		
		$cs->registerScriptFile( $baseURL.'/js/common.js', CClientScript::POS_END);
		
		$cs->registerLinkTag('icon','image/x-icon','/images/icon.ico');
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
	
	public function ph_sidebar( $type = "home" )
	{
		ob_start();
			switch ( $type ):
				case "home":
					$this->ads_home_300x250_sidebar();
					$this->fb_like_badge();
					$this->twit_follow_badge();
					$this->home_bottom_stick_hot();
				break;
				
				case "hot":
					$this->ads_home_300x250_sidebar();
					$this->fb_like_badge();
					$this->twit_follow_badge();
					$this->home_bottom_stick_latest();
				break;
				
				case "post":
					$this->ads_home_300x250_sidebar();
					$this->fb_like_badge();
					$this->twit_follow_badge();
					$this->home_bottom_stick_hot();
				break;
				
				case "upload":
					$this->upload_sidebar();
				break;
				default:
					//echo "i is not equal to 0, 1 or 2";
			endswitch;
		$ads = ob_get_contents();
		ob_end_clean();

		return $ads;
	}
	
	protected function ads_home_300x250_sidebar()
	{
		?>	<div class="sidebar_item ads_home_300x250_sidebar ">
				<?php $this->ads_300x250_code();?>
			</div>
		<?php
	}
	
	protected function ads_home_300x600_sidebar()
	{
		?>
			<div class="sidebar_item ads_home_300x600_sidebar">
				<?php $this->ads_300x250_code();?>
			</div>
		<?php
	}
	
	protected function fb_like_badge()
	{
		?>
			<div class="sidebar_item social_box_fb">
				<h3>Like Us on Facebook</h3>
				<div border_color="#0071FF" height="200" class="fb-like-box" data-href="http://www.facebook.com/pinoyhumors" data-width="300" data-show-faces="true" data-stream="false" data-header="false"></div>
			</div>
		<?php
	}
	
	protected function twit_follow_badge()
	{
		?>
			<div class="sidebar_item social_box_twit">
				<a href="https://twitter.com/pinoyhumors" class="twitter-follow-button" data-show-count="false" data-size="large">Follow @pinoyhumors</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			</div>
		<?php
	}
	protected function copyright_bottom()
	{
		?>
			<div class="copyright">
				<p class="copy">pinoyhumors &copy; 2013</p>
			</div>
		<?php
	}
	protected function home_bottom_stick_latest()
	{
		$post = Post::model()->get_latest_post();
		?>
			<div class="sidebar_item home_bottom_stick sticky">
				<div class="stick_ads_300x250">
					<div class="sidebar_item ads_home_300x250_sidebar ">
						<?php $this->ads_300x250_code();?>
					</div>
				</div>
				<div class="stick_ads_items_post">
					<h3><?php echo stripslashes( "What's Lastest?" );?></h3>
					<ul>
						<li>
							<table class="items_td">
								<tr>
									<?php for($i=0; $i<=2; $i++):?>
										<td class="img_td">
											<span class="img_thumb">
												<a class="img_link" title="<?php echo $post[$i]['title'];?>" href="<?php echo $this->get_permalink( $post[$i]['id'] );?>">
													<img alt="<?php echo $post[$i]['title'];?>" height="96.6667" width="96.6667" src="<?php echo $post[$i]['thumb_url'];?>"/>
													<span class="extra_details">
														<span class="text small"><?php echo $this->t_elapsed( $post[$i]['date_created'] );?> ago</span>
														<i class="icon-time icon-white"></i>
													</span>
												</a>
											</span>
										</td>
									<?php endfor;?>
								</tr>
							</table>
						</li>
						<li>
							<table class="items_td">
								<tr>
									<?php for($i=3; $i<=5; $i++):?>
										<td class="img_td">
											<span class="img_thumb">
												<a class="img_link" title="<?php echo $post[$i]['title'];?>" href="<?php echo $this->get_permalink( $post[$i]['id'] );?>">
													<img alt="<?php echo $post[$i]['title'];?>" height="96.6667" width="96.6667" src="<?php echo $post[$i]['thumb_url'];?>"/>
													<span class="extra_details">
														<span class="text small"><?php echo $this->t_elapsed( $post[$i]['date_created'] );?> ago</span>
														<i class="icon-time icon-white"></i>
													</span>
												</a>
											</span>
										</td>
									<?php endfor;?>
								</tr>
							</table>
						</li>
					</ul>
				</div>
				<?php $this->copyright_bottom();?>
			</div>
		<?php
	}
	
	protected function home_bottom_stick_hot()
	{
		$post = Post::model()->get_hotest_post();
		?>
			<div class="sidebar_item home_bottom_stick sticky">
				<div class="stick_ads_300x250">
					<div class="sidebar_item ads_home_300x250_sidebar ">
						<?php $this->ads_300x250_code();?>
					</div>
				</div>
				<div class="stick_ads_items_post">
					<h3><?php echo stripslashes( "What's Hot?" );?></h3>
					<ul>
						<li>
							<table class="items_td">
								<tr>
									<?php for($i=0; $i<=2; $i++):?>
										<td class="img_td">
											<span class="img_thumb">
												<a class="img_link" title="<?php echo $post[$i]['title'];?>" href="<?php echo $this->get_permalink( $post[$i]['id'] );?>">
													<img alt="<?php echo $post[$i]['title'];?>" height="96.6667" width="96.6667" src="<?php echo $post[$i]['thumb_url'];?>"/>
													<span class="extra_details">
														<span class="text"><?php echo $post[$i]['likes'];?></span>
														<i class="icon-heart icon-white"></i>
													</span>
												</a>
											</span>
										</td>
									<?php endfor;?>
								</tr>
							</table>
						</li>
						<li>
							<table class="items_td">
								<tr>
									<?php for($i=3; $i<=5; $i++):?>
										<td class="img_td">
											<span class="img_thumb">
												<a class="img_link" title="<?php echo $post[$i]['title'];?>" href="<?php echo $this->get_permalink( $post[$i]['id'] );?>">
													<img alt="<?php echo $post[$i]['title'];?>" height="96.6667" width="96.6667" src="<?php echo $post[$i]['thumb_url'];?>"/>
													<span class="extra_details">
														<span class="text"><?php echo $post[$i]['likes'];?></span>
														<i class="icon-heart icon-white"></i>
													</span>
												</a>
											</span>
										</td>
									<?php endfor;?>
								</tr>
							</table>
						</li>
					</ul>
				</div>
				<?php $this->copyright_bottom();?>
			</div>
		<?php
	}
	
	protected function upload_sidebar()
	{
		?>
			<div class="sidebar_item">
				<div class="stick_ads_items_post">
					<h3><?php echo stripslashes( "Uploading Rules" );?></h3>
					<div class="sidebar_body">
						<ul class="rules_ph">
							<li><strong>Exclusivity:</strong> PinoyHumors is dedicated to collect everything funny about Pinas, Pinoy, Noypi, Filipinos and Philippines. But, if you think you have something worth-sharing, feel free to do that.</li>
							<li><strong>Source:</strong> Unless you are so productive to create your own “Memes”, please put in the source!</li>
							<li><strong>Tag Line:</strong> If you wanna “trending” in all social media, why not use eye-catching titles from comedians, viral videos and movies. Example: “Amalayer Goes Global”, “You Don’t Do That To Me”, “Saging lang ang may Puso”.</li>
							<li><strong>NSFW:</strong> We know that you love to post this, just tick the box for NSFW and you will be free as a bird.</li>
						</ul>
					</div>
				</div>
				<?php $this->copyright_bottom();?>
			</div>
		<?php
	}
	
	protected function ads_300x250_code(){
		?>
			<script type="text/javascript"><!--
			google_ad_client = "ca-pub-9538427480425739";
			/* PH 300x250 home sidebar */
			google_ad_slot = "8617263628";
			google_ad_width = 300;
			google_ad_height = 250;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
		<?php
	}
}