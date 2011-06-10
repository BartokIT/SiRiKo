$(document).ready(function() {
	
	$( "#dialog:ui-dialog" ).dialog( "destroy" );

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
		height: 300,
		width: 350,
		modal: true,
		buttons: {
		"OK": function() 
		{
			var bValid = true;
			allFields.removeClass( "ui-state-error" );
			bValid = bValid && checkLength( name, "nickname", 3, 16 );
			bValid = bValid && checkRegexp( name, /^[a-z]([0-9a-z_])+$/i, "Il nickname deve essere costituito dai caratteri a-z, 0-9, underscore ed iniziare con una lettera." );
			if (bValid)
			{
				$.ajax("",
				{
					data: {"action": "check_nickname", "nickname":name.val()},
					dataType: "json",
					success: function(data, textStatus, jqXHR)
					{
						if(data.present)
						{
							updateTips( "Nickname gia' utilizzato" );
						}
						else
						{
							$( this ).dialog( "close" );
							$.ajax("",
							{
								data: {"action": "enter", "nickname":name.val()},
								complete: function()
								{	location.reload();	}
							});
						}
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

//	Creo il pulsante per la creazione della form
	$("#enter")
	.button()
	.click(function() {
		$( "#dialog-form" ).dialog( "open" );
		return false;
	});

});