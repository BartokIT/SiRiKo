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
			
			if ($status["substatus"] != "defense")
					return new ReturnedArea("game","game","attack/view_attack_result");
					
			if ($player_order == $data["attack"]["defender"]["player"])
				$currently_playing = true;
			else
				$currently_playing = false;
		
			$json_data = array();
			
			$json_data["gamer_turn"]=$currently_playing;
			$json_data["gamer_order"]= (int) $player_order;
			$json_data["units"]= $units;
			$json_data["attack"] =$data["attack"];
			$return = json_encode(array ('status'=>"game", "substatus"=>"defense", "data"=>$json_data));
			return new ReturnedAjax($return);
			break;
			
		case "roll_dice":
			$player_order = get_gamer_order(session_id());
			$status = get_current_turn_and_action(session_id());
			//Prelevo le unità usate per difendere ed attacare
			$data  = unserialize($status["data"]);
			$attack_units = $data["attack"]["attacker"]["choosen_units"];
			if ($attack_units <= $data["attack"]["defender"]["available_units"])
				$defender_units = $attack_units;
			else
				$defender_units = $data["attack"]["defender"]["available_units"];
			
			$defender_roll = array();
			$attacker_roll = array();
			
			//Tiro i dadi per l'attaccante
			for ($i = 0; $i < $attack_units; $i++)
			{
				$attacker_roll[$i] = rand(1,6);
			}
			
			for ($i = 0; $i < $defender_units; $i++)
			{
				$defender_roll[$i] = rand(1,6);
			}
			
			//Ordino gli array
			rsort($attacker_roll);
			rsort($defender_roll);			
			
			//Scorro l'array dei risultati del difensore che è sicuramente il più piccolo o uguale per il bilancio dell'attacco
			$attacker_units_delta = 0;
			$defender_units_delta = 0;
			foreach ($defender_roll as $key=>$value)
			{
				//Per test
				$defender_units_delta--;
				
				/*if ($value >= $attacker_roll[$key])
				{	$attacker_units_delta--; }
				else
				{	$defender_units_delta--; }*/
			}
			
			//Vedo i vincitori ed i vinti e imposto i nuovi valori delle unità per gli stati		
			//Visualizzo l'esito dell'attacco
			$data["attack"]["attacker"]["result"] = $attacker_units_delta;
			$data["attack"]["defender"]["result"] = $defender_units_delta;
			set_current_status($status["id_game"], $status["status"], $status["substatus"],serialize($data));
			//Recupero le informazioni sulle nazioni coinvolte
			$attacker_country=get_country_units_and_owner($status["id_game"],$data["attack"]["attacker"]["country"]["iso_code"]);
			$defender_country=get_country_units_and_owner($status["id_game"],$data["attack"]["defender"]["country"]["iso_code"]);
			
			//Imposto le unità per la nazione del difensore
			set_units($status["id_game"], $data["attack"]["defender"]["country"]["iso_code"], $defender_country["units"] + $defender_units_delta);			
			
			//Recupero il nuovo valore per le unita
			$defender_country=get_country_units_and_owner($status["id_game"],$data["attack"]["defender"]["country"]["iso_code"]);
			
			//Se non ci sono più unità ho conquistato la nazione
			if ($defender_country["units"] == 0)
			{
				//Levo una unità in più all'attaccante per metterla sul nuovo territorio 
				set_units($status["id_game"], $data["attack"]["attacker"]["country"]["iso_code"], $attacker_country["units"] + $attacker_units_delta - 1);
				
				//Imposto l'attaccante come proprietario della nazione che era del difensore
				assign_country($status["id_game"], $data["attack"]["attacker"]["player"], $data["attack"]["defender"]["country"]["iso_code"],1 );
			}
			else
			{
				set_units($status["id_game"], $data["attack"]["attacker"]["country"]["iso_code"], $attacker_country["units"] + $attacker_units_delta);
			}
			
			set_current_status($status["id_game"], "game", "attack/view_attack_result");
			//Visualizzo il risultato
			return new ReturnedArea("game","game","attack/view_attack_result");
			break;		
	}


?>
