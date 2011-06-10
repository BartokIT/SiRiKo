<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
  <title>SiRiKo Home Page</title>
		<link href="presentation/style/home.css" rel="stylesheet" type="text/css" />
		<link href="presentation/style/jquery-ui-1.8.13.custom.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="jscripts/jquery/jquery.min.js"></script>  
		<script type="text/javascript" src="jscripts/jquery/jquery-ui-1.8.13.custom.min.js"></script>  
		<script type="text/javascript" src="jscripts/game/default.js"></script>		
</head>
<body>
  <div id="games">
  Elenco delle partite disponibili:
  <?php
  	print_r($p);	
  ?>
  <ul>
  	
  </ul>
  </div>
</body>
</html>
