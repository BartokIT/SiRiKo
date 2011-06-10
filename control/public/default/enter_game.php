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
		case "get_games":
			$echo = $_REQUEST["sEcho"];
			$games = get_opened_games();
			$n_games = count($games);
			
			$json_string ="{";
			$json_string .="\"sEcho\":" . $echo . ", ";
			$json_string .="\"iTotalRecords\":" . $n_games . ", ";
			$json_string .="\"iTotalDisplayRecords\":" . $n_games . ", ";
			$json_string .="\"aaData\":[";
			
			
			$count = 1;
			foreach ($games as $game)
			{
				if ($n_games == $count)
					$json_string .= "[\"" . $game["name"]."\",2,\"\"]";
				else
					$json_string .= "[\"" . $game["name"]."\",2,\"\"],";
					
				$count++; 
			}
			$json_string .="]";
			$json_string .="}";
			
			return new ReturnedAjax($json_string);
			break;
		case "create_games":
			$game_name = $_REQUEST["game_name"];
			$exists = check_game_existance($game_name);
			
			if ($exists !== false)
			{
				$result = true;
			}
			else
			{
				insert_new_game($game_name);
				$result = false;	
			}
			
			return new ReturnedAjax(json_encode(array("exists"=>$result)));
			break;
			
	}


?>
