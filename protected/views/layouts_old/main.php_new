<?php /* @var $this Controller */ ?>
<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo CHtml::encode($this->pageTitle);?></title>
    <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/assets/fd/css/foundation.css" />
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/assets/fd/js/vendor/modernizr.js"></script>
  </head>
  <body>
  <nav class="top-bar" data-topbar>
    <ul class="title-area">
       
      <li class="name">
        <h1>
          <a href="/">
            <?php echo CHtml::encode(Yii::app()->name); ?>
          </a>
        </h1>
      </li>
      <li class="toggle-topbar menu-icon"><a href="#"><span>menu</span></a></li>
    </ul>
 
    <section class="top-bar-section">
      <ul class="right">
        <li class="divider"></li>
        <li class="has-dropdown">
          <a href="#">Main Item 1</a>
          <ul class="dropdown">
            <li><label>Section Name</label></li>
            <li class="has-dropdown">
              <a href="#" class="">Has Dropdown, Level 1</a>
              <ul class="dropdown">
                <li><a href="<?php echo Yii::app()->createUrl('auth/index');?>">Dropdown Options</a></li>
                <li><a href="<?php echo Yii::app()->createUrl('auth/index');?>">Dropdown Options</a></li>
                <li><a href="#">Level 2</a></li>
                <li><a href="#">Subdropdown Option</a></li>
                <li><a href="#">Subdropdown Option</a></li>
                <li><a href="#">Subdropdown Option</a></li>
              </ul>
            </li>
            <li><a href="#">Dropdown Option</a></li>
            <li><a href="#">Dropdown Option</a></li>
            <li class="divider"></li>
            <li><label>Section Name</label></li>
            <li><a href="#">Dropdown Option</a></li>
            <li><a href="#">Dropdown Option</a></li>
            <li><a href="#">Dropdown Option</a></li>
            <li class="divider"></li>
            <li><a href="#">See all →</a></li>
          </ul>
        </li>
        <li class="divider"></li>
        <li><a id="fblog" href="#">FB Login</a></li>
        <li class="divider"></li>
        <li class="has-dropdown">
          <a href="#">Main Item 3</a>
          <ul class="dropdown">
            <li><a href="#">Dropdown Option</a></li>
            <li><a href="#">Dropdown Option</a></li>
            <li><a href="#">Dropdown Option</a></li>
            <li class="divider"></li>
            <li><a href="#">See all →</a></li>
          </ul>
        </li>
      </ul>
    </section>
  </nav>

	<div class="row"></div>
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/assets/fd/js/vendor/jquery.js"></script>
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/assets/fd/js/foundation.min.js"></script>
	<script>
		$(document).foundation();
		$('#fblog').click(function(){
			var left  = ($(window).width()/2)-(900/2),
			top   = ($(window).height()/2)-(600/2),
			popup = window.open ("/auth/facebook", "popup", "width=900, height=600, top="+top+", left="+left);
		});
	</script>
  </body>
</html>