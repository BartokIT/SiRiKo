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
			$status = get_current_turn_and_action(session_id());
			
			if ( $status["substatus"] != "thinking")
				return new ReturnedArea("game", "game", $status["substatus"]);	
				
			$player_order = get_gamer_order(session_id());
			$units= get_units_disposition($status["id_game"]);

			if ($player_order == $status["current_gamer"])
				$currently_playing = true;
			else
				$currently_playing = false;
		
			$json_data = array();
			
			$json_data["gamer_turn"]=$currently_playing;
			$json_data["gamer_order"]= (int) $player_order;
			$json_data["units"]= $units;
			$return = json_encode(array ('status'=>"game", "substatus"=>"thinking", "data"=>$json_data));
			return new ReturnedAjax($return);
			break;
		case "attack":
			//@TODO: verificare che l'id del giocatore sia quello corrente e verificare che abbia almento due unità sul territorio
			$player_order = get_gamer_order(session_id());
			$status = get_current_turn_and_action(session_id());

			//Controllo di essere effettivamente il giocatore corrente
			if ($status["current_gamer"] != $player_order)
			{
				return new ReturnedArea("game", "game");
				break;			
			}
			
			$attacker_iso_code =  $_REQUEST["attacker_iso_country"];
			$defender_country_name =  $_REQUEST["defender_country"];
			
			//Prelevo i codici delle nazioni vicine all'attaccante e le informazioni sul difensore
			$attacker_neighbors = get_country_neighbors_from_iso_code($attacker_iso_code);
			$defender_country = get_country_code_and_owner($status["id_game"], $defender_country_name);
			
		
			//Verifico se la nazione chiamata è effettivamente vicina
			$found = false;
			foreach ($attacker_neighbors as $neighbor)
			{
			
				if ($neighbor == $defender_country["iso_code"])
				{
		
					$found = true;
					break;
				}
			}
			
			//In caso negativo ritorno allo stato di default
			if (!$found)
			{
				return new ReturnedArea("game", "game");
				break;
			}
			
			//Prelevo il le informazioni sulla nazione dell'attaccante
			$attacker_country =get_country_units_and_owner($status["id_game"], $attacker_iso_code);
			//Controllo se ho almeno due unità a disposizione per effettuare l'attacco
			if ($attacker_country["units"] <= 1)
			{
				return new ReturnedArea("game", "game");
				break;				
			}
			
			//echo "OK";
			//Aggiungo una voce ai dati riguardanti l'attacco che si sta compiendo
			$status["data"]=array();
			$status["data"]["attack"]= array();
			$status["data"]["attack"]["attacker"] = array("player"=> $player_order,"available_units"=>($attacker_country["units"] - 1), "country"=>array("iso_code"=> $attacker_iso_code, "name"=>$attacker_country["name"]));
			$status["data"]["attack"]["defender"] = array("player"=> $defender_country["owner"],"available_units"=>$defender_country["units"], "country"=>array("iso_code"=> $defender_country["iso_code"], "name"=>$defender_country_name));
			
			set_current_status($status["id_game"], "game", "attack", serialize($status["data"]));
			return new ReturnedArea("game", "game", "attack");
			break;
		case "get_neighbors":
			$country_name = $_REQUEST["country_name"];
			$status = get_current_turn_and_action(session_id());
			$country = get_country_neighbors($country_name);
			$info = get_countries_units_and_owner($status["id_game"], $country);
			$json_data=array();
			$json_data["neighbors"]= $info;
			
			$return = json_encode(array ('status'=>"game", "substatus"=>"thinking", "data"=>$json_data));
			return new ReturnedAjax($return);
			break;
		

	}


?>
