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
			$game_info = get_current_turn_and_action(session_id());
			
			if ($game_info["status"] != "init")
			{
				return new ReturnedArea("game", $game_info["status"],$game_info["substatus"]);
			}
						
			if ($game_info["substatus"] != "" || $game_info["substatus"] != null)
			{
				return new ReturnedArea("game", "init", $game_info["substatus"]);
			}
			
			$min_player = false;
			$min = is_min_gamer();
			if ($min)
				$min_player = true;
			
			$return = json_encode(array ('status'=>"init", "substatus"=>null, "data"=> array("min_player"=>$min_player)));
			return new ReturnedAjax($return);
			break;
		case "init_dice_launch":
			$game_info = get_current_turn_and_action(session_id());
			set_current_status($game_info["id_game"], "init", "trow_dice");
			return new ReturnedArea("game", "init", "trow_dice");
			
	}


?>
