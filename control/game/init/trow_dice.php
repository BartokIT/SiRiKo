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
			$status_data= array("dice"=>array());
			$player_order = get_gamer_order(session_id());
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
			//$json_data["dice"]["gamer"] =array_keys($status_data["dice"]);
			//$json_data["dice"]["values"] =array_values($status_data["dice"]);
			$return = json_encode(array ('status'=>"init", "substatus"=>"trow_dice", "data"=>$json_data));
			return new ReturnedAjax($return);
			break;
			
		case "launch_die":
			$roll = rand(1,6);
			$game_info = get_current_turn_and_action(session_id());
			
			if ($game_info["data"] == "")
			{
				$status_data = array("dice"=>array( get_gamer_order(session_id())=>$roll));
				set_current_status($game_info["id_game"], "init", "trow_dice", serialize($status_data));
			}
			else
			{
				$status_data = unserialize($game_info["data"]);
				$status_data["dice"][ get_gamer_order(session_id())] = $roll;
				set_current_status($game_info["id_game"], "init", "trow_dice", serialize($status_data));
			}

			//Controllo se sono l'ultimo giocatore ad effettuare il lancio del dado			
			if (is_max_gamer())
			{
				echo "max_gamer";
				//Ordino i giocatori in base al risultato dei lanci
				compute_gamer_order($game_info["id_game"], $status_data["dice"]);
				//Distribuisco le unita tra i giocatori
				assign_country_and_units($game_info["id_game"], "EU");
				$min_player = get_first_gamer($game_info["id_game"]);
				
				set_current_status($game_info["id_game"], "game", "thinking", serialize(array()),$min_player["order"], 0);
				echo "OK";
				return new ReturnedArea("game", "game","thinking");
				//return new ReturnedArea("game", "init", "trow_dice");
			}
			else
			{
				//Prendo il prossimo partecipante e gli passo lo stato attivo
				$next_gamer = get_next_gamer($game_info["id_game"], $game_info["current_gamer"]);
			
				set_next_gamer($game_info["id_game"], $next_gamer);
				return new ReturnedArea("game", "init", "trow_dice");
			}
			
			break;
	}


?>
