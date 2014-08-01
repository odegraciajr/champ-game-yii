<!DOCTYPE HTML>
<html>
	<head>
	<style type="text/css">
		body{
			background: #cecece !important;
			font-family: Arial,"Helvetica Neue",Helvetica,sans-serif;
			color:#fff;
			font-size: 14px;
		}
		.contacting{
			padding-top: 60px;
			color:#0071FF;
		}
	</style>
	</head>
<body>
<table width="100%" border="0">
  <tr>
    <td align="center" height="200" valign="middle">
		<img alt="Movie Hunger" width="215" height="73" src="/images/connect-logo.png"/>
	</td>
  </tr>
  <tr>
    <td align="center" height="19"><img alt="Loading..." width="220" height="19" src="/images/ajax-blue.gif"></td> 
  </tr>
  <tr>
    <td align="center"><p class="contacting">Contacting Facebook, please wait...</p></td> 
  </tr> 
</table>
<script> 
	setTimeout( function(){window.location.href = '<?php echo $redirect . "&display=popup";?>'}, 750 );
</script>
</body>
</html>