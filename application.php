<?php

/**
* Funzione di snodo del flusso per l'applicazione - a parte l'inizializzazione, conviene inglobarla nel flusso
*/
function nodo_principale($site_view, $area, $sub_area, $action, $flow)
{

	//vedo se è stata inizializzata l'area e la siteview
	if ( (strcmp($site_view, "none")==0) && (strcmp($area, "none")==0) )
	{
		//echo "inizializza $site_view, $area";
		return inizializza($flow);
	//	return new ReturnedArea("public","default");
	}

	//includo il file di controllo con lo stesso nome della site_view nella stessa cartella
	add_to_debug("Area controllata" ,$site_view . "/" . $area . "/" . $sub_area );
	
	
	
	 if (file_exists("control/" . $site_view . "/" . $area . "/" .  $sub_area . ".php"))
	 	{
	 		add_to_debug("Nome file controllo" ,"control/" . $site_view . "/" . $area . "/" . $sub_area . ".php" );
	 		return require("control/" . $site_view . "/" . $area . "/" . $sub_area . ".php" );
	 		
	 	}
	 else
	 	{
	 		//se non esiste provo a ri-inizializzare il sistema
	 		$init = inizializza($flow);
	 		//print_r($init);
	 		if ( ($init->site_view == $site_view) AND ($init->area == $area) AND ($init->sub_area == $sub_area))
	 			die ("NON ESISTE siteview " .$site_view ." area " .  $area  . " - subarea " . $sub_area);
	 		else	
				return inizializza($flow);
	 	}

	//se l'elaborazione è arrivata fin quì c'è stato un errore di programmazione
	return FALSE; 

}
?>
