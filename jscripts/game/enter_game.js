$(document).ready(function() {	

//	Creo il pulsante per la creazione della form
	$("#create").button().click(function()
	{
		var name=$("#name").val();
		$.ajax("",
				{
					data: {"action": "create_games", "game_name":name},
					dataType:"json",
					success: function(data, textStatus, jqXHR)
					{	
						if (!data.exists) taskProgressDraw();
					}
				});
		return false;
	});
	
	
	
	$("#gamer_table").dataTable(
	{
		"sDom": '<"H">t<"F">',
		"bJQueryUI": true,
		"bServerSide": true,
		"sAjaxSource": "?action=get_games",
		"sPaginationType": "full",
		"aoColumns": [
				{ "sWidth": "400px" },
				{ "sWidth": "300px" },
				{ "sWidth": "15%" }]
	});
	
	$(".join_button").live('click', function(event)
	{
		event.preventDefault();
		//Richiamo lo script per aggiungere il giocatore alla partita
		var id_game = $(this).attr('href');
		$.ajax("",
			{
				data : {"action":"add_to_game", "id_game": id_game},
				dataType: "json",
				success: function(data, xhr,response)
				{
					if (data.result)
						taskProgressDraw();
				}
			}
		);
	});

	$(".left_button").live('click', function(event)
	{
		event.preventDefault();
		//Richiamo lo script per aggiungere il giocatore alla partita
		var id_game = $(this).attr('href');
		$.ajax("",
			{
				data : {"action":"remove_from_game", "id_game": id_game},
				dataType: "json",
				success: function(data, xhr,response)
				{
					if (data.result)
						taskProgressDraw();
				}
			}
		);
	});

	$(".start_button").live('click', function(event)
	{
		event.preventDefault();
		//Richiamo lo script per aggiungere il giocatore alla partita
		var id_game = $(this).attr('href');
		$.ajax("",
			{
				data : {"action":"start_game", "id_game": id_game},
				dataType: "json",
				success: function(data, xhr,response)
				{
					if (data.result)
						location.reload();
				}
			}
		);
	});		
});

function taskProgressDraw()
{
	  $("#gamer_table").dataTable().fnDraw();
}

function checkStatusChanges()
{
		$.ajax("",
			{
				data : {"action":"check_status"},
				dataType: "json",
				success: function(data, xhr,response)
				{
					if (data.changed)
						location.reload();
				}
			}
		);	
}

setInterval('checkStatusChanges()', 5000);
setInterval('taskProgressDraw()', 10000);


