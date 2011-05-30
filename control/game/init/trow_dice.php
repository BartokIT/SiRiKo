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
			//@TODO: scrivere una funzione che fa una sola chiamata al DB
			$player_order = get_gamer_order(session_id());
			$game_info = get_current_turn_and_action(session_id());
			
			if ($player_order == $game_info["current_participant"])
				$currently_playing = true;
			else
				$currently_playing = false;
			$return = json_encode(array ('status'=>"init", "substatus"=>"trow_dice", "data"=> array("gamer_turn"=>$currently_playing )));
			return new ReturnedAjax($return);
			break;
			
	}


?>
