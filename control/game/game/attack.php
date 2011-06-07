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

			$player_order = get_gamer_order(session_id());
			$status = get_current_turn_and_action(session_id());
			$units= get_units_disposition($status["id_game"]);
			if ($player_order == $status["current_gamer"])
				$currently_playing = true;
			else
				$currently_playing = false;
		
			$json_data = array();
			
			$json_data["gamer_turn"]=$currently_playing;
			$json_data["gamer_order"]= (int) $player_order;
			$json_data["units"]= $units;
			$data  = unserialize($status["data"]);
			$json_data["attack"] =$data["attack"];
			$return = json_encode(array ('status'=>"game", "substatus"=>"attacking", "data"=>$json_data));
			return new ReturnedAjax($return);
			break;
		case "attackers_unit_choose":
			if (is_numeric($_REQUEST["units"]))
			{
				$status = get_current_turn_and_action(session_id());

				$json_data = array();
				$data  = unserialize($status["data"]);
				$data["attack"] =$data["attack"];
				$data["attack"]["attacker"]["choosen_units"] = $_REQUEST["units"];
				set_current_status($status["id_game"], "game", "defense", serialize($json_data));
				return new ReturnedArea("game", "game", "defense");
			}
			else
				return new ReturnedArea("game", "game", "attack");
			break;

	}


?>
