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
		<script type="text/javascript">
			$(document).ready(function() {


			
		//$( "#dialog:ui-dialog" ).dialog( "destroy" );
		
		var name = $( "#name" ),	allFields = $( [] ).add( name ), tips = $( ".validateTips" );

		function updateTips( t ) {
			tips
				.text( t )
				.addClass( "ui-state-highlight" );
			setTimeout(function() {
				tips.removeClass( "ui-state-highlight", 1500 );
			}, 500 );
		}

		function checkLength( o, n, min, max ) {
			if ( o.val().length > max || o.val().length < min ) {
				o.addClass( "ui-state-error" );
				updateTips( "La lunghezza del" + n + " deve essere tra i " +
					min + " ed i " + max + " caratteri." );
				return false;
			} else {
				return true;
			}
		}

		function checkRegexp( o, regexp, n ) {
			if ( !( regexp.test( o.val() ) ) ) {
				o.addClass( "ui-state-error" );
				updateTips( n );
				return false;
			} else {
				return true;
			}
		}
		
		$( "#dialog-form" ).dialog({
			autoOpen: false,
			height: 250,
			width: 350,
			modal: true,
			buttons: {
				"OK": function() {
					var bValid = true;
					allFields.removeClass( "ui-state-error" );
					bValid = bValid && checkLength( name, "nickname", 3, 16 );
					bValid = bValid && checkRegexp( name, /^[a-z]([0-9a-z_])+$/i, "Il nickname deve essere costituito dai caratteri a-z, 0-9, underscore ed iniziare con una lettera." );
					if (bValid)
					{
						$.ajax("<?php echo INDEX . "?action=enter"?>",
						{
						complete: function()
							{
								location.reload();
							}
						});

					}
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
		});

		//Creo il pulsante per la creazione della form
		$("#enter")
			.button()
			.click(function() {
				$( "#dialog-form" ).dialog( "open" );
				return false;
			});
				
			});
		</script>		
</head>
<body>
  <div id="background">&nbsp;</div>
  <div id="logo">&nbsp;</div>
  <div id="join"><div><a id="enter" href="?action=enter">Entra!</a></div></div>

<div id="dialog-form" title="Inserimento nickname">
	<p class="validateTips"></p>
	<form>
	<fieldset>
		<label for="name">Nickname</label>
		<input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" />
<!--		<label for="email">Email</label>
		<input type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all" />		-->
	</fieldset>
	</form>
</div>

</body>
</html>
