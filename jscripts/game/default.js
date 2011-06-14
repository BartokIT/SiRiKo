var previous_response ={};
var units_maker= new Array();
var SiRiKo = {};

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

	var console = $('<div id="consoleContainer"></div>');
	var leftConsoleBorder = $('<div></div>');
	var rigthConsoleBorder = $('<div></div>');
	
	rigthConsoleBorder.css({'width': '100%', 'height': '100%', 'background':'url("presentation/image/console_rxborder.png") no-repeat center right'});
	leftConsoleBorder.css({'width': '100%', 'height': '100%', 'background':'url("presentation/image/console_sxborder.png") no-repeat center left'});
	
	console.append(rigthConsoleBorder);
	rigthConsoleBorder.append(leftConsoleBorder);
	leftConsoleBorder.append('<div id="console"><div id="user_info"></div></div>');

	//$('#map_canvas').append('<div id="result" style="margin:auto; z-index: 13; position: absolute; cursor: pointer; background-color:white;width:250px;height:2500px"></div>');
	$('#map_canvas').append(console);
	
	console.position({
				of: $( "#map_canvas" ),
				my: 'center bottom',
				at: 'center bottom',
				offset: '0 0 0 50'
			});
			
	console.css({'background':'url("presentation/image/riskparchement.png") repeat top left'});
	//Memorizzo i riferimenti alla parti principali della console di gioco
	SiRiKo.console = {console: $("#console" )};

	
//	$('#result').css({top:'50%',left:'50%',margin:'-'+($('#myDiv').height() / 2)+'px 0 0 -'+($('#myDiv').width() / 2)+'px'});
	window.setInterval(getServerStatus,5000);
}
/***** END INITIALIZE FUNCTION *****/

function getServerStatus()
{
		$.ajax({cache: false,
		url : "",
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
		if (previous_status == null)
		{
			
			return_value = false;
			return false;
		}
		
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
			
			if (previous_status[key] != value)
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
			if (current_status[key] != value)
			{
				return_value = false;
				return false;
			}
		}
	});	
	//console.log("Restituito " + return_value);	
	return return_value;
}


function manageMarker(response, draggable)
{
	//Creo il geocoder e le informazioni unità
	var geocoder = new google.maps.Geocoder();
	var units_info = response.data.units;
	
	//Ciclo sulle unità messe a disposizioen per ciascun giocatore
	$.each(units_info, function (player, nations) {
			$.each(nations, function(iso_code, country_info )
			{
				//Creo la variabile che deve contenere i markers
				if (!SiRiKo.markers)
				{
					SiRiKo.markers = {};
				}
				
				//Controllo se esiste il marker in questione se non esiste lo creo
				if (!SiRiKo.markers.hasOwnProperty(iso_code))
				{
					
					//Richiamo il geocoder per avere informazioni sulla posizione delle nazioni
					console.log('Recall geocoder service to get country center for ' + country_info.country );
					geocoder.geocode( { 'address': country_info.country,
									'language': 'en'},
									function(results, status)
									{

									 if (status == google.maps.GeocoderStatus.OK)
					  				 { 
					  				 	console.log('Creo marker');
					  				 	
					  				   //Se il geocoder è ok posso creare il marker
					  					SiRiKo.markers[iso_code] = {};
					  					SiRiKo.markers[iso_code]['player'] = player;
										var position = results[0].geometry.location;
										SiRiKo.markers[iso_code]['position']=position;
										var marker =  new google.maps.Marker({
											icon: 'presentation/image/marker_player_' + player + '.png',
											map: map,
											position: position,
											title: "Unità stanziate: " + country_info.units
										});
										SiRiKo.markers[iso_code]['marker']=marker;
										SiRiKo.markers[iso_code]['units']=country_info.units;
										
										//Imposto se deve essere draggabile o meno
										setDraggableMarker(player, response.data.gamer_order, response.data.gamer_turn, draggable, iso_code);

										//Ora aggiungo il listener per la fine del drag
										google.maps.event.addListener(marker,'dragend', function(event)
										{
											//Prima di tutto riporto il marker al centro della nazione
											marker.setPosition(position);
											
											//Richiamo il geocoder per sapere su quale nazione è stato rilasciato
											geocoder.geocode({'latLng': event.latLng, 'language': 'en'}, function(results, status) {
											if (status == google.maps.GeocoderStatus.OK) {
												//Reperisco il nome della nazione
												var countryName = getCountryNameFromPosition(results);												
												console.log('Marker released on ' + countryName);
												
												//Ordino alla logica di gioco di attaccare la nazione in questione
												$.ajax({cache: false,
													url : "index.php",
													data: {"game_logic":"1",
															'attacker_iso_country' : iso_code,
															'defender_country': countryName,
															"action":"attack"},
													dataType: "json",
													success: function() { getServerStatus();}
													});
											} else {
												alert("Geocoder failed due to: " + status);
											}
											});

										
										});
					  				 }
					  				 else
					  				 {
					  					alert('Problemi nel richiamare il geocoder');
					  						//@TODO: Controllare il tipo di errore e prendere l'azione necessaria
					  				 }	
									});					
				}
				else
				{
					//Se esiste devo controllare che non ci sia stato un cambiamento di proprietà
					if (player != SiRiKo.markers[iso_code]['player'])
					{
						//Aggiorno il proprietario
						SiRiKo.markers[iso_code]['player'] = player;
						
						//Aggiorno il colore della bandiera
						SiRiKo.markers[iso_code]['marker'].setIcon('presentation/image/marker_player_' + player + '.png');
					}
					
					//Devo anche aggiornare il numero delle unità disponibili
					if (country_info.units != SiRiKo.markers[iso_code]['units'])
					{
						SiRiKo.markers[iso_code]['units']=country_info.units;
						SiRiKo.markers[iso_code]['marker'].setTitle("Unità stanziate: " + country_info.units);
					}
					
					//Se esiste devo controllare se devo renderlo draggabile o meno
					setDraggableMarker(player, response.data.gamer_order, response.data.gamer_turn, draggable, iso_code);
					

				}
			
			});
	});
}

function setDraggableMarker(marker_owner, current_player, player_turn, draggable, iso_code)
{
				if ((marker_owner == current_player) && player_turn && draggable)
				{
					SiRiKo.markers[iso_code]['marker'].setDraggable(true);
				}
				else
				{
					SiRiKo.markers[iso_code]['marker'].setDraggable(false);
				}
}

function getCountryNameFromPosition(results)
{
	var countryName='';
	if (results[0]) {
			$.each(results[1].address_components,function(index, value)
			{
				
				$.each(value.types, function(index2, address_type)
				{
					if (address_type == 'country')
						{
							countryName= value.long_name;
							return;
						}
				});
			});
		}
	 else {
		alert("Geocoder failed due to: " + status);
	}

	return countryName;
}

function drawInitialRollDice(response, throw_dice, textMessage, view_result)
{
	if (view_result == undefined)
	{
	
		var initialRoll = $('<div id="initialRoll"></div>');
		var upperDiv = $('<div></div>');
		var lowerDiv = $('<div>' + textMessage + '</div>');
		
		var rollImage = $('<img width="50px" src="presentation/image/die_' + response.user_info.order + '_big2.png"/>');
		upperDiv.css({'height': '80px', 'width':'100%'});
		rollImage.css('cursor', 'pointer');	
	
		lowerDiv.css({'width':'100%',
		 'font-variant': 'small-caps',
		 'text-align': 'center',
		 'font-size': '1.2em',
		 'margin': '0'
		 });	
		 
		upperDiv.append(rollImage);

		initialRoll.append(upperDiv);	
		initialRoll.append(lowerDiv);
		SiRiKo.console.console.append(initialRoll);
		rollImage.position({
					of: upperDiv,
					my: 'center',
					at: 'center',
					offset: '0 0 0 0'
				});	
		SiRiKo.console.initialRoll = initialRoll;
		//Counce per attirare l'attenzione
		if (throw_dice)
		{
			rollImage.effect("bounce", { times: 5, distance: 40 }, 400);
			upperDiv.click(function(event) {
				event.preventDefault();
				$.ajax("index.php",
				{ 
					data: {'action': 'launch_die', "game_logic":"1" }
				});
				getServerStatus();
			});
		}
	}
		//Con questa visualizzo i dadi appena lanciati
	if ((response.data.dice) && ($(response.data.dice).size() > 0))
	{
		var i=0;
		var resultTable = '<div>Esito lancio dadi per l\'ordine di gioco:<table><tr>';
		
		$.each(response.data.dice, function(index,value)
		{

			resultTable +='<td>' + response.data.players_info[index].nickname + ': <img style="vertical-align: middle;" width="30px" src="presentation/image/die_' + value + '_big2.png"/></td>';

			i++;
		});

		resultTable += '</tr></table></div>';
		var resultDiv = $(resultTable);
				
		resultDiv.css({
			 'float': 'left',
			 'font-variant': 'small-caps',
			 'text-align': 'center',
			 'font-size': '1.4em',
			 'margin': '15px'
			 });	
		SiRiKo.console.console.append(resultDiv);
		SiRiKo.console.diceResult = resultDiv;
	}
}

/*
* Cancello tutti i contenitori che dipendono dallo stato
*/
function clearAllRecipient()
{
	//Rimuovo la parte per il click dei dadi
	if (SiRiKo.console.hasOwnProperty('initialRoll'))
		SiRiKo.console.initialRoll.remove();

	//Rimuovo la parte di visualizzazione dei dadi
	if (SiRiKo.console.hasOwnProperty('diceResult'))
		SiRiKo.console.diceResult.remove();		
}

/**
* Routine che si occupa di disegnare l'interfaccia utente
**/
function drawUserInterface(response)
{
	clearAllRecipient();
	if (!SiRiKo.console.hasOwnProperty('user_info'))
	{  	
	drawUserInfo(response);
	}
	
	if (response.status == 'game')
	{
		var response = $('<div id="result"></div>');
		response.css({
			 'float': 'left',
			 'font-variant': 'small-caps',
			 'text-align': 'center',
			 'font-size': '1.4em',
			 'margin': '15px',
			 'margin-top': '20px'
			 });	
		SiRiKo.console.console.append(response);
	}
}

/**
* Routine che disegna le informazioni sull'utente
*/
function drawUserInfo(response, force_redraw)
{
	

		$('#user_info').empty();
		console.log("Redrawed user info");
		SiRiKo.console.user_info = $('#user_info');
		
//		var markerImage = '<img src="presentation/image/marker_player_' + response.user_info.order + '.png" />';

		var userMarker = $('<div id="userMarker"></div>');

		if (response.status == 'game')
		{
			//Imposto il marker solo se è in gioco
			SiRiKo.console.userMarker = userMarker;		
			userMarker.css({
		'background': 'url("presentation/image/marker_player_' + response.user_info.order + '.png") no-repeat center bottom' });
		}
		var userNickname = $('<div>' + response.user_info.nickname +'</div>');
		userNickname.css({'width':'100%',
		 'font-variant': 'small-caps',
		 'text-align': 'center',
		 'font-weight': 'bold',
		 'font-size': '1.3em',
		 'margin': '0'
		 });
		
		SiRiKo.console.user_info.append(userMarker);
		SiRiKo.console.user_info.append(userNickname);
	
}

function logica_gioco(response, textStatus, jqXHR)
{	
	if ( !check_same_status(previous_response,response))
	{
		//Disegno la console
		drawUserInterface(response);
		$('#result').empty();
		switch (response.status)
		{
			case "init":
				//Se il sottostato è null devo iniziare la parte di lancio dei dati
				/*if (response.substatus == null)
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
							$.ajax("index.php",
							{
								data: {'action':'init_dice_launch',"game_logic":"1"}
							});
							getServerStatus();
						});
						}
				} //Altrimenti se sono nel sottostato trow_dice ho iniziato la procedura di lancio dei dadi
				else*/
				if (response.substatus == "throw_dice")
				{

					//Se è il turno di questo giocatore allora visualizzo il pulsante di lancio del dado			
					if (response.data.gamer_turn)
					{
						var messages = 'Clicca sul dado per lanciarlo';
						drawInitialRollDice(response, true, messages);
						/*var tira_dado_button = '<a id="launch_die" href="#">Lancia dado</a>';
						$('#result').append(tira_dado_button);	
						$('#launch_die').click(function(event) {
								event.preventDefault();
								$.ajax("index.php",
								{ 
									data: {'action': 'launch_die', "game_logic":"1" }
								});
								getServerStatus();
							});*/
					}
					else
					{
						var gamer_status = "";
						var gamer_order = response.data.gamer_order;
						//console.log(response.data);
	
						if (response.data.dice[gamer_order])
						{
							var messages = 'Attendi che tirino gli altri giocatori';
							//gamer_status = "Attendi che tirino gli altri giocatori";
								drawInitialRollDice(response, false, messages);
						}
						else
							{
								var messages = 'Attendi per poter lanciare il dado';
								drawInitialRollDice(response, false, messages);
//								gamer_status = 'In attesa di lanciare il dado';
							}
						//$('#result').append(gamer_status);	
					}				
				}				
				else if (response.substatus == "view_init_result")
				{
					
					drawInitialRollDice(response, false, '',true);

					window.setTimeout(function()
					{
						$.ajax({cache: false,
							url : "index.php",
							data: {'action':'end_view',
							"game_logic":"1"},
							dataType: "json",
							success : function(response, textStatus, jqXHR)
							{
								drawUserInfo(response, true);
							}
							});
							
					}, 5000 );
				}
				
				break;
			case "game":
				
				switch (response.substatus)
				{
					case "thinking":
							manageMarker(response, true);
							if (response.data.gamer_turn)
							{
								$('#result').empty();
								var passTurnButton = $('<a id="pass_button" href="#">Passa il turno</button>').button({
										    icons: {
									        secondary: "ui-icon-circle-arrow-e"
									    }
									}).click( function () {
									
										$.ajax({cache: false,
											url : "index.php",
											data: {'action':'pass_turn',
												"game_logic":"1"},
											dataType: "json",
											success: function()
											{
												getServerStatus();
											}
											});
									});		
								 
								 $('#result').append(passTurnButton);					
							 }
							break;
					case "attacking":					
						//Imposto i marker a non draggabili		
						manageMarker(response, false);
						

						//Aggiungo all'interfaccia la scelta del numero di unità da attaccare
						$('#result').empty();
						if (response.data.gamer_order == response.data.attack.attacker.player )
						{
							var choose_units_button = 'Scegli con quante unità attaccare<form action="#"><div id="choosen_units">';
							for ( i=1; (i <=  response.data.attack.attacker.available_units) && (i<=3);i++)
								choose_units_button += '<input id="choosen_units'+ i + '" type="radio" name="choosen_units" value="'+ i + '" /> <label for="choosen_units'+ i + '">' + i + '</label>';
							choose_units_button +='<br/><input id="choose_units"type="submit" value="Scegli"/></div></form>';
						


							$('#result').append(choose_units_button);	
							$('#choosen_units').buttonset();							
							$('#choose_units').button({
									}).click(function(event) {
									var choosen_units = $("input[name='choosen_units']:checked").val();								
									event.preventDefault();
									$.ajax({cache: false,
									url : "index.php",
									data: {'action':'attackers_unit_choose','choosen_units' : choosen_units,"game_logic":"1"},
									dataType: "json",
									success: function()
									{
										
									}
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
						manageMarker(response, false);	

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
							$('#choose_units').button().click(function(event) {
									event.preventDefault();
									$.ajax({cache: false,
									url : "index.php",
									data: {'action':'roll_dice',"game_logic":"1"},
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
						break;
					case 'attack/view_attack_result':
							
							
							var view_result = '';
							manageMarker(response, false);
							$('#result').empty();
							
							if (response.data.attack.attacker.result < 0)
							{
								console.log('View attacker result');
							
								view_result += "<div>L'attaccante ha perso n. " + (-1*response.data.attack.attacker.result) + " unita'</div>";
							}

							
							if (response.data.attack.defender.result < 0)
							{
								console.log('View defender result');
								view_result += "<div>Il difensore ha perso n. " + (-1*response.data.attack.defender.result) + " unita'</div>";
							}
							
							$('#result').append(view_result);	
							window.setTimeout(function()
							{

								$.ajax({cache: false,
									url : "index.php",
									data: {'action':'end_view',
									"game_logic":"1"},
									dataType: "json"
									});
									
							}, 5000 );
						break;
					
					case 'endgame':
							$('#result').empty();
							manageMarker(response, false);
							if (response.data.result == 'winner')
								var view_result = 'Fine della partita - Hai vinto';
							else if(response.data.result == 'loser')
								var view_result = 'Sei stato sconfitto';
							
							$('#result').append(view_result);								
						break;
					case 'move_units':
						
							$('#result').empty();
							manageMarker(response, false);
						if (response.data.gamer_turn)
						{
							var fromCountry = $('<div><span>Numero di unità per '+ response.data.move.from.name + '</span>&nbsp;<span class="units_num" id="from_number">' + response.data.move.from.units + '</span></div>');

							var toCountry = $('<div><span>Numero di unità per  '+ response.data.move.to.name + '</span>&nbsp;<span class="units_num" id="to_number">' + response.data.move.to.units + '</span></div>');
							var moveUnitsPlus = $('<a id="plus_button" href="#"></button>').button({
								        icons: {
							            primary: "ui-icon-plus"
							        },
							        text: false
							    }).click(function () {
							    	var from_number = parseInt($('#from_number').text());
							    	var to_number = parseInt($('#to_number').text());	
							    	if (from_number > 1)
							    	{
							    		from_number = from_number - 1;
							    		$('#from_number').text(from_number);
							    		
							    		to_number = to_number + 1;
							    		$('#to_number').text(to_number);	
							    	}
							    });
							    
							var moveUnitsMinus = $('<a id="minus_button" href="#"></button>').button({
								        icons: {
							            primary: "ui-icon-minus"
							        },
							        text: false
							    }).click(function () {
							    	var from_number = parseInt($('#from_number').text());
							    	var to_number = parseInt($('#to_number').text());	
							    	if (to_number > 1)
							    	{
							    		from_number = from_number + 1;
							    		$('#from_number').text(from_number);
							    		
							    		to_number = to_number - 1;
							    		$('#to_number').text(to_number);	
							    	}
							    });			
							
							var okButton = $('<a id="ok_button" href="#">Conferma</button>').button({
								        icons: {
							            primary: "ui-icon-circle-check"
							        }
							    }).click( function () {

							    	var from_number = parseInt($('#from_number').text());
							    	var to_number = parseInt($('#to_number').text());	
								
									$.ajax({cache: false,
										url : "index.php",
										data: {'action':'confirm',
											"game_logic":"1",
											'from':from_number,
											'to': to_number },
											dataType: "json",
										success: function()
										{
											getServerStatus();
										}
										});
							    });
							    
							var cancelButton = $('<a id="cancel_button" href="#">Annulla</button>').button({
								        icons: {
							            primary: "ui-icon-circle-close"
							        }
							    }).click( function () {
							    
									$.ajax({cache: false,
										url : "index.php",
										data: {'action':'cancel',
											"game_logic":"1"},
										dataType: "json",
										success: function()
										{
											getServerStatus();
										}
										});
							    });
							//moveUnits.addClass('plus-button');
							$('#result').append(fromCountry);$('#result').append(toCountry);
							$('#result').append(moveUnitsMinus);$('#result').append(moveUnitsPlus);
							$('#result').append(okButton); $('#result').append(cancelButton);	
						}
						else
						{
							$('#result').append('<div>Il player x sta muovendo le unità</div>');
						}					
						break;
				}
			
		}
	}
	else
		console.log("No status update");	
	//Salvo lo stato per il confronto
	previous_response = response;
}


