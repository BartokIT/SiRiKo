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
			return new ReturnedPage("view.php");
//			return new ReturnedArea("public","default", "enter_game");
			break;

	}


?>
