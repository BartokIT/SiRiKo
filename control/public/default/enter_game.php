<?php
/**
* $action variabile che contiene il nome dell'area corrente
*
*/

	add_to_debug("Azione",  $action);
	switch ($action)
	{
		default:
		case "":
			$games = get_opened_games();
			return new ReturnedPage("enter_game.php", array("games"=>$games));		
//			return new ReturnedArea("public", "default");
			break;
			
	}


?>
