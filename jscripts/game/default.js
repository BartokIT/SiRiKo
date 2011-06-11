function initialize() {

    var chicago = new google.maps.LatLng(41.850033, 60.6500523);
	var style =                                           
[ { featureType: "transit.station", elementType: "all", stylers: [ { visibility: "off" } ] },{ featureType: "transit.line", elementType: "all", stylers: [ { visibility: "off" } ] },{ featureType: "administrative.province", elementType: "all", stylers: [ { visibility: "off" } ] },{ featureType: "administrative.locality", elementType: "all", stylers: [ { visibility: "off" } ] },{ featureType: "road.highway", elementType: "all", stylers: [ { visibility: "off" } ] },{ featureType: "road.arterial", elementType: "all", stylers: [ { visibility: "off" } ] },{ featureType: "road.local", elementType: "all", stylers: [ { visibility: "off" } ] },{ featureType: "administrative.land_parcel", elementType: "all", stylers: [ { visibility: "off" } ] },{ featureType: "all", elementType: "all", stylers: [ ] } ];


	var sirikoMapType = new google.maps.StyledMapType(
      style, {name:"siriko"});

      
    map = new google.maps.Map(document.getElementById('map_canvas'), {
      center: chicago,
      zoom: 3,
      mapTypeId: 'terrain'
    });

	map.mapTypes.set('terrain', sirikoMapType);

     
    layer = new google.maps.FusionTablesLayer({
      query: {
        select: 'kml_4326',
        from: '931868',        
		where: "Continent = 'EU'"
      },   
  styles: [{
    polygonOptions: {
		fillColor: "#AA0022",
		fillOpacity: 0.3,
		strokeWeight: 1.0,
		strokeColor: "#000000"
    }
  }],
  suppressInfoWindows: true
});

layer.setMap(map);

/*
google.maps.event.addListener(layer, 'click', function(event) {
	var country_iso_code = event.row["iso_a3"].value;
	var web_service = "http://api.geonames.org/neighboursJSON?formatted=true&username=shadow_silver&country=" + country_iso_code;
	console.log(web_service);
	$.ajax({cache: false,
		url : web_service,
		dataType: "json",
		success: get_neighbors
	});
	var marker = new google.maps.Marker({
        position: event.latLng,
      	title:"Hello World!"
  });
  	marker.setMap(map);
});
*/

function get_neighbors(response, textStatus, jqXHR)
{
	var neighbors = "";
	$.each(response.geonames, function(index, value)
	{
		neighbors += value.name + "; ";
	});
	console.log(neighbors);
}; 
  

  $('#map_canvas').append('<div id="result" style="margin:auto; z-index: 13; position: absolute; cursor: pointer; background-color:white;width:250px;height:2500px"></div>');
 $('#result').css({top:'50%',left:'50%',margin:'-'+($('#myDiv').height() / 2)+'px 0 0 -'+($('#myDiv').width() / 2)+'px'});
	window.setInterval(getServerStatus,5000);
}

function getServerStatus()
{
		$.ajax({cache: false,
		url : "index.php",
		data: {"game_logic":"1"},
		dataType: "json",
		success: logica_gioco
		});
}
	

jQuery.fn.compareArray = function(t) {
	
    if (this.length != t.length) { return false; }

    
    var a = this.sort(),
        b = t.sort();
    for (var i = 0; t[i]; i++) {
        if (a[i] !== b[i]) { 
                return false;
        }
    }
    return true;
};

function check_same_status(previous_status, current_status)
{
	var return_value = true;
	$.each(current_status, function(key, value)
	{
		//Se non esiste la chiave restituisco diversi
		if (!previous_status.hasOwnProperty(key))
		{
				console.log("no-key " + key + ":" + previous_status[key]);
				return_value = false;
				return false;			
		}

		if ($.isPlainObject(value))
		{
			if (!check_same_status(previous_status[key], value))
			{
				console.log("Object " + key + ":" + previous_status[key]);
				return_value = false;
				return false;
			}
			
		}
		else if ($.isArray(value))
		{
			if ( !$(value).compareArray(previous_status[key]) )
			{
				console.log("Array " + key + ":" + previous_status[key] + ":" + value);
				return_value = false;
				return false;				
			}
		}
		else	
		{
			
			if (current_status[key] != value)
			{
				return_value = false;
				return false;
			}
		}
	});

	if (return_value)
	$.each(previous_status, function(key, value)
	{
		//Se non esiste la chiave restituisco diversi
		if (!current_status.hasOwnProperty(key))
		{
				console.log("no-key " + key + ":" + current_status[key]);
				return_value = false;
				return false;			
		}

		if ($.isPlainObject(value))
		{
			if (!check_same_status(current_status[key], value))
			{
				console.log("Object " + key + ":" + current_status[key]);
				return_value = false;
				return false;
			}
			
		}
		else if ($.isArray(value))
		{
			if ( !$(value).compareArray(current_status[key]) )
			{
				console.log("Array " + key + ":" + current_status[key] + ":" + value);
				return_value = false;
				return false;				
			}
		}
		else	
		{
			
			if (previous_status[key] != value)
			{
				return_value = false;
				return false;
			}
		}
	});	
	//console.log("Restituito " + return_value);	
	return return_value;
}

var previous_response ={};
var units_maker= new Array();

function drawMarkers(response, all_false)
{
	var geocoder = new google.maps.Geocoder();
	var units = response.data.units;
	//Ciclo tutte le unità ottenute
	$.each(units, function(player, nations)
	{	
		if(!nations.lenght)
		{
			//Prelevo i dati della nazione
			$.each(nations, function(iso_code, info )
			{
				geocoder.geocode( { 'address': info.country, 'language': 'en'}, function(results, status) {
				  if (status == google.maps.GeocoderStatus.OK)
				  {
					console.log("Richiamato geocoder per " + info.country );
					//map.setCenter(results[0].geometry.location);
					units_maker[iso_code] = new Array();
					units_maker[iso_code]["player"] = player;
					units_maker[iso_code]["original_position"] = results[0].geometry.location;
					units_maker[iso_code]["marker"] = new google.maps.Marker({
						icon: 'presentation/image/marker_player_' + player + '.png',
						map: map,
						position: results[0].geometry.location,
						title: "Unità stanziate: " + info.units												
					});
				
					//Imposto draggabili solamente i marker per i giocatore corrente e per le sue nazioni
					if ((player == response.data.gamer_order) && response.data.gamer_turn && !all_false)
					{ units_maker[iso_code]["marker"].setDraggable(true); }
				
					//Nel momento in cui è rilasciato viene riportato alla posizione originale
					google.maps.event.addListener(units_maker[iso_code]["marker"],'dragend', function(event)
					{
						//Cerco la nazione sulla quale è stato rilasciato il marker
						units_maker[iso_code]["marker"].setPosition(units_maker[iso_code]["original_position"]);
						geocoder.geocode({'latLng': event.latLng, 'language': 'en'}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
						
							if (results[0]) {
								$.each(results[1].address_components,function(index, value)
								{
									console.log(value.long_name);														
									$.each(value.types, function(index2, address_type)
									{

										if (address_type == 'country')
										{
													//Una volta rilasciato il marker eseguo l'azione di attacco
													$.ajax({cache: false,
													url : "index.php?action=attack",
													data: {'attacker_iso_country' : iso_code, 'defender_country': value.long_name},
													dataType: "json",
													success: function() { }
													});
										}
									}
									);
								});
							}
						} else {
							alert("Geocoder failed due to: " + status);
						}
						});												
					});
				
				  } else { alert("Geocode was not successful for the following reason: " + status); }
				});
			});
		}
	});

}

function logica_gioco(response, textStatus, jqXHR)
{	
	if ( !check_same_status(previous_response,response))
	{

		$('#result').empty();
		switch (response.status)
		{
			case "init":
				//Se il sottostato è null devo iniziare la parte di lancio dei dati
				if (response.substatus == null)
				{
					var geocoder = new google.maps.Geocoder();
					geocoder.geocode( { 'address': "Italy"}, function(results, status) {
					  if (status == google.maps.GeocoderStatus.OK) {
						map.setCenter(results[0].geometry.location);
					  } else {
						alert("Geocode was not successful for the following reason: " + status);
					  }
					  });
					//Solamente il player che è entrato per primo può iniziare a lanciare il dado
					if (response.data.min_player)
					{
						//Creo il pulsante per iniziare il gioco
						var inizia_ordine_gioco_button = '<a id="init_order_gamer" href="#">Inizia ordinamento gioco</a>';
						$('#result').append(inizia_ordine_gioco_button);
						$('#init_order_gamer').click(function(event) {
							event.preventDefault();
							$.ajax("index.php?action=init_dice_launch");
							getServerStatus();
						});
						}
				} //Altrimenti se sono nel sottostato trow_dice ho iniziato la procedura di lancio dei dadi
				else if (response.substatus == "throw_dice")
				{
					//Con questa visualizzo i dadi appena lanciati
					if (response.data.dice)
					{
						$.each(response.data.dice, function(index,value)
						{
							$('#result').append('<div>Giocatore ' + index + ': ' + value + '</div>');
						});
					}
					//Se è il turno di questo giocatore allora visualizzo il pulsante di lancio del dado			
					if (response.data.gamer_turn)
					{
						var tira_dado_button = '<a id="launch_die" href="#">Lancia dado</a>';
						$('#result').append(tira_dado_button);	
						$('#launch_die').click(function(event) {
								event.preventDefault();
								$.ajax("index.php?action=launch_die");
								getServerStatus();
							});
					}
					else
					{
						var gamer_status = "";
						var gamer_order = response.data.gamer_order;
						//console.log(response.data);
	
						if (response.data.dice[gamer_order])
						{
							gamer_status = "Attendi che tirino gli altri giocatori";
						}
						else
							gamer_status = 'In attesa di lanciare il dado';
						$('#result').append(gamer_status);	
					}				
				}
				break;
			case "game":
				
				switch (response.substatus)
				{
					case "thinking":
							drawMarkers(response,false);
		
							break;
					case "attacking":					
						//Imposto i marker a non draggabili		
						var count = 0;
						if (units_maker.length == 0)
						{
							console.log("Redraw");
							drawMarkers(response,true);
						}
						/*console.log("Redrawed " + units_maker.length + ' marker');
						$.each(units_maker, function(index,value) {
							value["marker"].setDraggable(false);
							console.log(count++);
						})*/;
						
						//Aggiungo all'interfaccia la scelta del numero di unità da attaccare
						$('#result').empty();
						if (response.data.gamer_order == response.data.attack.attacker.player )
						{
							var choose_units_button = 'Scegli con quante unità attaccare<form action="#">';
							for ( i=1; (i <=  response.data.attack.attacker.available_units) && (i<=3);i++)
								choose_units_button += '<input  type="radio" name="choosen_units" value="'+ i + '" /> ' + i + '<br />';
							choose_units_button +='<input id="choose_units"type="submit" value="Scegli"/></form>';
						


							$('#result').append(choose_units_button);	
							$('#choose_units').click(function(event) {
									var choosen_units = $("input[name='choosen_units']:checked").val();								
									event.preventDefault();
									$.ajax({cache: false,
									url : "index.php",
									data: {'action':'attackers_unit_choose','choosen_units' : choosen_units },
									dataType: "json",
									success: function() { }
									});
									getServerStatus();
							});
						} else if (response.data.gamer_order == response.data.attack.defender.player )
						{
							var choose_units_button = 'Il giocatore ';
							choose_units_button += response.data.attack.attacker.player + ' ti sta attaccando';
							$('#result').append(choose_units_button);	
						
						} else {
							var choose_units_button = 'Il giocatore ';
							choose_units_button += response.data.attack.attacker.player + ' sta attaccando il giocatore ' + response.data.attack.defender.player ;
							$('#result').append(choose_units_button);	
						}
						break;
					case 'defense':
					//Imposto i marker a non draggabili		
						if (units_maker.length == 0)
						{
							console.log("Redraw");
							drawMarkers(response,true);
						}
						
						//Aggiungo all'interfaccia la scelta del numero di unità da attaccare
						$('#result').empty();
						
						if (response.data.gamer_order == response.data.attack.defender.player )
						{
							var choose_units_button = 'Ti difenderai con ';
							if ( response.data.attack.attacker.choosen_units <= response.data.attack.defender.available_units)
								choose_units_button += response.data.attack.attacker.choosen_units + " unita <br/>";
							else
								choose_units_button += response.data.attack.defender.available_units+ " unita <br/>";							
								choose_units_button +='<a id="choose_units" href="#">Procedi</a>';
			
							$('#result').append(choose_units_button);	
							$('#choose_units').click(function(event) {
									event.preventDefault();
									$.ajax({cache: false,
									url : "index.php",
									data: {'action':'roll_dice' },
									dataType: "json",
									success: function() { }
									});
									getServerStatus();
							});
						} else if (response.data.gamer_order == response.data.attack.attacker.player )
						{
							var choose_units_button = 'Il giocatore ';
							choose_units_button += response.data.attack.defender.player + ' si sta difendendo dal tuo attacco';
							$('#result').append(choose_units_button);	
						
						} else {
							var choose_units_button = 'Il giocatore ';
							choose_units_button += response.data.attack.defender.player + ' si sta difendendo dal giocatore ' + response.data.attack.attacker.player ;
							$('#result').append(choose_units_button);	
						}						
				}
			
		}
	}
	else
		console.log("No status update");	
	//Salvo lo stato per il confronto
	previous_response = response;
}


