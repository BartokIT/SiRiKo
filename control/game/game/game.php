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

			$return = json_encode(array ('status'=>"game", "substatus"=>null, "data"=>$json_data));
			return new ReturnedAjax($return);
			break;

	}


?>
