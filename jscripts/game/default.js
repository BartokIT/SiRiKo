function initialize() {

    var chicago = new google.maps.LatLng(41.850033, -87.6500523);

    map = new google.maps.Map(document.getElementById('map_canvas'), {
      center: chicago,
      zoom: 3,
      mapTypeId: 'roadmap'
    });
 
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
  }
  ],
  suppressInfoWindows: true
});

layer.setMap(map);

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
	window.setInterval(function () 
	{
		$.ajax({cache: false,
		url : "index.php",
		dataType: "json",
		success: logica_gioco
		});

	},5000);
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
		if (!previous_status[key])
		{
				return_value = false;
				return false;			
		}
		
		if ($.isPlainObject(value))
		{
			if (check_same_status(previous_status[key], value))
			{
				return_value = false;
				return false;
			}
			
		}
		else if ($.isArray(value))
		{
			if ( $(value).compareArray(previous_status[key]) )
			{
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
	
	return return_value;
}
var previous_response ={};

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
					//Solamente il player che è entrato per primo può iniziare a lanciare il dado
					if (response.data.min_player)
					{
						//Creo il pulsante per iniziare il gioco
						var inizia_ordine_gioco_button = '<a id="init_order_gamer" href="#">Inizia ordinamento gioco</a>';
						$('#result').append(inizia_ordine_gioco_button);
						$('#init_order_gamer').click(function(event) {
							event.preventDefault();
							$.ajax("index.php?action=init_dice_launch");
						});
						}
				} //Altrimenti se sono nel sottostato trow_dice ho iniziato la procedura di lancio dei dadi
				else if (response.substatus == "trow_dice")
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
							
							var geocoder = new google.maps.Geocoder();
							var units = response.data.units;
							//Ciclo tutte le unità ottenute
							$.each(units, function(player, nations)
							{	
								if(!nations.lenght)
								{
									$.each(nations, function(iso_code, info )
									{
										geocoder.geocode( { 'address': info.country}, function(results, status) {
										  if (status == google.maps.GeocoderStatus.OK) {
											console.log("Richiamato geocoder");
											map.setCenter(results[0].geometry.location);
											var marker = new google.maps.Marker({
												map: map,
												position: results[0].geometry.location,
												title: info.units
											});
										  } else {
											alert("Geocode was not successful for the following reason: " + status);
										  }
										});
	
									});
								}
							});
				}
			
		}
	}
		
	//Salvo lo stato per il confronto
	previous_response = response;
}
