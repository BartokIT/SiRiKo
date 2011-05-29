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
			$min_player = false;
			$min = is_min_gamer();
			if ($min)
				$min_player = true;
			
			$return = json_encode(array ('status'=>"init", "substatus"=>null, "data"=> array("min_player"=>$min_player)));
			return new ReturnedAjax($return);
			break;
		case "init_dice_launch":
			
			return new ReturnedArea("game", "init", "trow_dice");
			
	}


?>
