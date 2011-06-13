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
			$data  = unserialize($status["data"]);
			
			if ($player_order == $status["current_gamer"])
				$currently_playing = true;
			else
				$currently_playing = false;
		
			$json_data = array();
			$json_data["gamer_turn"]=$currently_playing;
			$json_data["gamer_order"]= (int) $player_order;
			$json_data["units"]= $units;
			$json_data["move"] =$data["move"];
			
	/*	set_current_status($status["id_game"], "game", "thinking", serialize(array()));
			return new ReturnedArea("game", "game","thinking");
				break;				*/
			$return = json_encode(array ('status'=>"game", "substatus"=>"move_units", "data"=>$json_data));
			return new ReturnedAjax($return);
			break;
	}


?>
