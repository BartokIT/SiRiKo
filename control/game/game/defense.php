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
				if ($value >= $attacker_roll[$key])
				{	$attacker_units_delta--; }
				else
				{	$defender_units_delta--; }
			}
			
			//Vedo i vincitori ed i vinti e imposto i nuovi valori delle unità per gli stati
			echo "attacker loose " . $attacker_units_delta;
			print_r($attacker_roll);
			echo "defender loose " . $defender_units_delta;			
			print_r($defender_roll);			
			//Visualizzo il risultato
			return new ReturnedArea("game","game","defense");
			break;		
	}


?>
