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
			
			$return = json_encode(array ('status'=>"init", "substatus"=>"trow_dice", "data"=> array()));
			return new ReturnedAjax($return);
			break;
			
	}


?>
