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
        from: '882248'
      },   
  styles: [{
    polygonOptions: {
      fillColor: "#000000",
      fillOpacity: 0.2
    }
  }, {
    where: "Number = 1",
    polygonOptions: {
      fillColor: "#0000FF"
    }
  }, {
    where: "Number = 2",
    polygonOptions: {
	  fillColor: "#00FFFF"
    }
  }, {
    where: "Number = 3",
    polygonOptions: {
	  fillColor: "#F0F0F0"
    }
  }, {
    where: "Number = 4",
    polygonOptions: {
	  fillColor: "#AA2233"
    }
  }
  ]
});
  
  layer.setMap(map);
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

function logica_gioco(response, textStatus, jqXHR)
{	
	$('#result').empty();
	switch (response.status)
	{
		case "init":
			if (response.substatus == null)
			{
				if (response.data.min_player)
					{
						var inizia_ordine_gioco_button = '<a id="init_order_gamer" href="#">Inizia ordinamento gioco</a>';
						$('#result').append(inizia_ordine_gioco_button);
						$('#init_order_gamer').click(function(event) {
							event.preventDefault();
							$.ajax("index.php?action=init_dice_launch");
						});
					}
			}
			else if (response.substatus == "trow_dice")
			{
				if (response.data.gamer_turn)
				{
					var tira_dado_button = '<a href="index.php?init_dice_launch">Lancia dado</a>';
				}
				else
				{
					var tira_dado_button = '<a href="index.php?init_dice_launch">In attesa di lanciare il dado</a>';
				}
				$('#result').append(tira_dado_button);	
			}
	}
	
}
