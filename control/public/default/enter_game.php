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
		
		$game_id=get_user_id_game();
		$count = 1;
		foreach ($games as $game)
		{
			if (($game_id === false) || ($game_id != $game["id"]))
				$left_join_button = "<a class='join_button ui-button ui-widget ui-state-default ui-corner-all' href='". $game ["id"]."'>Join game</a>";
			else
				{
					$first_gamer_info=get_first_gamer($game["id"]);
					/*if (count($first_gamer_info))
					{
						$left_join_button = "<a class='left_button ui-button ui-widget ui-state-default ui-corner-all' href='". $game ["id"]."'>Left game</a>";
					}
					else*/
				//	print_r($first_gamer_info);
					if ($first_gamer_info["user_session"] == session_id())
						$left_join_button = "<a class='start_button ui-button ui-widget ui-state-default ui-corner-all' href='". $game ["id"]."'>Start game</a>";
					else											
						$left_join_button = "<a class='left_button ui-button ui-widget ui-state-default ui-corner-all' href='". $game ["id"]."'>Left game</a>";
				}
				
			if ($n_games == $count)
				$json_string .= "[\"" . $game["name"]."\",".$game["players"] .",\"$left_join_button\"]";
			else
				$json_string .= "[\"" . $game["name"]."\",".$game["players"] .",\"$left_join_button\"],";
				
			$count++; 
		}
		$json_string .="]}";

		
		return new ReturnedAjax($json_string);
		break;
		
	case "start_game":
		$game_info = get_game_status_from_user();
		$user_info = get_first_gamer($game_info["id_game"]);
		set_current_status($game_info["id_game"], "init", "throw_dice", null, $user_info["order"]);
		global $flusso;
				
		$flusso->set_site_view("game");
		$flusso->set_area("view");
		$flusso->set_sub_area("view");			
		

		return new ReturnedAjax(json_encode(array("result"=>true)));				
//		return new ReturnedArea("game","view");
		break;		
			
	case "add_to_game":
		$id_game = $_REQUEST["id_game"];
		insert_user_in_game($id_game);
		return new ReturnedAjax(json_encode(array("result"=>"true")));
		break;		
		
	case "remove_from_game":
		$id_game = $_REQUEST["id_game"];
		remove_from_game($id_game);
		return new ReturnedAjax(json_encode(array("result"=>"true")));
		break;		
					
	case "create_games":
		//@TODO: controllare che non ci siano più di 10 partite
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
	
	case "check_status":
		$changed = false;
//		$game_info = get_current_turn_and_action(session_id());
		$game_info = get_game_status_from_user();
		//Devo sia cambiare lo stato che restituire una stringa
		global $flusso;
				
		if ($game_info["substatus"] == "throw_dice")
		{
			$changed = true;
			$flusso->set_site_view("game");
			$flusso->set_area("view");
			$flusso->set_sub_area("view");			
		}

		return new ReturnedAjax(json_encode(array("changed"=>$changed, "status"=>$game_info["status"], "substatus"=>$game_info["substatus"])));		
		break;
}


?>
