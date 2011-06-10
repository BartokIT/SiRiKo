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
			$state_string = is_in_initiated_game();
			//Se il giocatore non è in banca dati allora visualizzo la home page
			if ($state_string == "not_present")
				return new ReturnedPage("home.php");
			elseif (($state_string == "game_not_set") || ($state_string == "game_not_initiated"))
			{
				return new ReturnedArea("public", "default", "enter_game");	
			}
			elseif ($state_string == "game_initiated")
			{
				return new ReturnedArea("game", "view");				
			}		
			break;
		case "check_nickname":
			$nickname = $_REQUEST["nickname"];

			if(check_nickname_existence($nickname))
			{
				return new ReturnedAjax(json_encode(array("present"=>true)));
			}
			else 
			{
				return new ReturnedAjax(json_encode(array("present"=>false)));				
			}
			break;
		case "enter":
			//@TODO : check validita e presenza nickname
			$nickname = $_REQUEST["nickname"];
			insert_unbounded_gamer($nickname);
			//return new ReturnedArea("public", "default", "enter_game");
			break;			
	}


?>
