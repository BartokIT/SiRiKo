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
						if (!data.exists)
							location.reload();
					}
				});
		return false;
	});
	
	$("#gamer_table").dataTable(
	{
		"sDom": '<"H">t<"F">',
		"bJQueryUI": true,
		/*"bProcessing": true,*/
		"bServerSide": true,
		"sAjaxSource": "?action=get_games"
	});
});

function taskProgressDraw() {
	  $("#gamer_table").dataTable().fnDraw();
	}

setInterval('taskProgressDraw()', 2000);
