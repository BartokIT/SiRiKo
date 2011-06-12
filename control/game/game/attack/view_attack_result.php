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
			
			if ($player_order == $data["attack"]["defender"]["player"])
				$currently_playing = true;
			else
				$currently_playing = false;
		
			$json_data = array();
			
			$json_data["gamer_turn"]=$currently_playing;
			$json_data["gamer_order"]= (int) $player_order;
			$json_data["units"]= $units;
			$json_data["attack"] =$data["attack"];
			
			
			//Se c'è solo un indice allora sono il vincitore
			if (count($units) == 1)
			{
				if (isset($units[$player_order]))
				{
					$json_data["result"]="winner";
					$return = json_encode(array ('status'=>"game", "substatus"=>"endgame", "data"=>$json_data));
					return new ReturnedAjax($return);
					break;
				}	
			}
			
			//Controllo se ho ancora nazioni 
			if (!isset($units[$player_order]))
			{
				$json_data["result"]="loser";
				$return = json_encode(array ('status'=>"game", "substatus"=>"endgame", "data"=>$json_data));
				return new ReturnedAjax($return);
				break;
			}
			
			$return = json_encode(array ('status'=>"game", "substatus"=>"attack/view_attack_result", "data"=>$json_data));
			return new ReturnedAjax($return);
			break;
		case "end_view":
			$status = get_current_turn_and_action(session_id());		
			set_current_status($status["id_game"], "game", "thinking");
			return new ReturnedArea("game", "game", "thinking");
			break;	
	}


?>
