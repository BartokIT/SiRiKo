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
			$data  = unserialize($status["data"]);
			
			if ( $status["substatus"] != "attack")
			{
				return new ReturnedArea("game", "game", $status["substatus"]);	
			}
			
			$units= get_units_disposition($status["id_game"]);
			
			if ($player_order == $data["attack"]["attacker"]["player"])
				$currently_playing = true;
			else
				$currently_playing = false;
		
			$json_data = array();
			
			$json_data["gamer_turn"]=$currently_playing;
			$json_data["gamer_order"]= (int) $player_order;
			$json_data["units"]= $units;
			$json_data["attack"] =$data["attack"];
			$return = json_encode(array ('status'=>"game", "substatus"=>"attacking", "data"=>$json_data));
			return new ReturnedAjax($return);
			break;
		case "attackers_unit_choose":
			if (is_numeric($_REQUEST["choosen_units"]))
			{
				$status = get_current_turn_and_action(session_id());
				$data  = unserialize($status["data"]);
				$data["attack"]["attacker"]["choosen_units"] = $_REQUEST["choosen_units"];
				set_current_status($status["id_game"], "game", "defense", serialize($data));
				return new ReturnedArea("game", "game", "defense");
			}
			else
				return new ReturnedArea("game", "game", "attack");
			break;

	}


?>
