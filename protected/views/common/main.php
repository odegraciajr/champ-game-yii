<?php /* @var $this Controller */ ?>
<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo CHtml::encode($this->pageTitle);?></title>
	<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <!--<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/assets/fd/css/foundation.css" />-->
	<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/normalize.css" />
	<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<!--<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>-->
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/assets/fd/js/vendor/modernizr.js"></script>
  </head>
  <body>
	<div class="main">
		<?php echo $content;?>
	</div>
	<script>
		jQuery.fn.center = function () {
			this.css("position","absolute");
			this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + 
												$(window).scrollTop()) + "px");
			this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + 
												$(window).scrollLeft()) + "px");
			return this;
		}
		$('#signup').center();
	</script>
  </body>
</html>