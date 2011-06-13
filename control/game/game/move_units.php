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

			if ( $status["substatus"] != "move_units")
				return new ReturnedArea("game", "game", $status["substatus"]);	
			
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
		case "cancel":

			$player_order = get_gamer_order(session_id());
			$status = get_current_turn_and_action(session_id());
			
			
			//Controllo l'utente come meccanismo di sicurezza
			if ($player_order != $status["current_gamer"])
			{
				return new ReturnedArea("game", "game","move_units");
				break;			
			}	
					
			set_current_status($status["id_game"], "game", "thinking", serialize(array()));
			return new ReturnedArea("game", "game","thinking");
			break;
			
		case "confirm":
			$player_order = get_gamer_order(session_id());
			$status = get_current_turn_and_action(session_id());
			$data = unserialize($status["data"]);
			
			//Controllo l'utente come meccanismo di sicurezza
			if ($player_order != $status["current_gamer"])
			{
				return new ReturnedArea("game", "game","move_units");
				break;			
			}
			
			$from_units = $_REQUEST["from"];
			$to_units = $_REQUEST["to"];
			
			$delta = (int)$from_units + (int)$to_units - (int)$data["move"]["from"]["units"] - (int)$data["move"]["to"]["units"];

			//Controllo che l'utente non abbia barato
			if ($delta == 0)
			{
				//Levo una unità in più all'attaccante per metterla sul nuovo territorio 
				set_units($status["id_game"], $data["move"]["from"]["iso_code"], $from_units);
				set_units($status["id_game"], $data["move"]["to"]["iso_code"], $to_units);		
			}
			
			set_current_status($status["id_game"], "game", "thinking", serialize(array()));
			return new ReturnedArea("game", "game","thinking");
			break;					
		
	}


?>
