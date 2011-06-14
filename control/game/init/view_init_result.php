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
			//Invio al client lo stato corrente specificando se è quello con l'ordine più basso
			//@TODO: scrivere una funzione che fa una sola chiamata al DB3
			//$status_data= array("dice"=>array());
//			$player_order = get_gamer_order(session_id());
			$player_info = get_gamer_info();
			$player_order = $player_info["order"];
			$players_info = get_co_gamers();
			$game_info = get_current_turn_and_action(session_id());

			//Controllo se non sono in un nuovo 
			if ($game_info["status"] != "init")
			{
				return new ReturnedArea("game", $game_info["status"],$game_info["substatus"]);
			}
			
			if ($game_info["data"] != "")
			{
				$status_data = unserialize($game_info["data"]);
			}
			
			if ($player_order == $game_info["current_gamer"])
				$currently_playing = true;
			else
				$currently_playing = false;
			$json_data = array();
			

			$json_data["gamer_turn"]=$currently_playing;
			$json_data["gamer_order"]= (int) $player_order;
			$json_data["dice"]=$status_data["dice"];
			$json_data["players_info"]=$players_info;
			//$json_data["dice"]["gamer"] =array_keys($status_data["dice"]);
			//$json_data["dice"]["values"] =array_values($status_data["dice"]);
			$return = json_encode(array ('user_info'=> $player_info, 'status'=>"init", "substatus"=>"view_init_result", "data"=>$json_data));
			return new ReturnedAjax($return);
//				return new ReturnedArea("game", "view");			
			break;
			
		case "end_view":
			$status = get_current_turn_and_action(session_id());		
			set_current_status($status["id_game"], "game", "thinking", serialize(array()));
			//set_current_status($status["id_game"], "game", "thinking");
			return new ReturnedArea("game", "game","thinking");
			break;
	}


?>
