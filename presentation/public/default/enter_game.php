<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
  <title>SiRiKo Home Page - Enter game</title>
		<link href="presentation/style/home.css" rel="stylesheet" type="text/css" />
		<link href="presentation/style/jquery-ui-1.8.13.custom.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="jscripts/jquery/jquery.min.js"></script>  
		<script type="text/javascript" src="jscripts/jquery/jquery-ui-1.8.13.custom.min.js"></script>
		<script type="text/javascript" src="jscripts/jquery/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="jscripts/game/enter_game.js"></script>		
		
</head>
<body>
<div>
	  <div id="background">&nbsp;</div>
	<div id="logo">&nbsp;</div>
	<div class="ui-overlay">
	<div class="ui-widget-overlay"></div>
	<div style="position: absolute; left: 50px; top: 30px;" class="ui-widget-shadow ui-corner-all"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-corner-all container">
				<div class="ui-widget">
					<form>
					<!-- <fieldset> -->
						<label for="name">Nuova partita</label>
						<input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" />
						<input type="submit"  id="create" value="Crea partita" />		
					<!-- </fieldset> -->
					</form>
				</div>
				<div id="games">
				  Elenco delle partite disponibili:
				  <table id="gamer_table" style="margine:auto">
				  	<thead>
				  		<tr>
				  			<th>Nome</th>
				  			<th>Partecipanti</th>
				  			<th>Partecipa</th>
				  		</tr>
				  	</thead>
				  	<tbody>
					<?php
					  	foreach($p["games"] as $i=>$value)
					  	{
					  		echo "<tr><td>". $value["name"] . "</td><td></td><td></td></tr>";	
					  	}
					  ?>  		  	
				  	</tbody>
				  </table>
				  		
				</div>
	
	</div>		
</div>
	

</body>
</html>
