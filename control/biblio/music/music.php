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
			return new ReturnedPage("music.php");		
			break;
		case "detail":

			if(@is_numeric($_REQUEST["atmid"]))
			{
				$music_id = $_REQUEST["atmid"];
				$_SESSION["music"]=get_author_title_music($music_id);
			if (isset($_REQUEST["page"]))
			{
				$_SESSION["sentence"]["page"]="page=".$_REQUEST["page"];
			}
				return new ReturnedPage("detail.php");	
			} else
			{
				return new ReturnedArea("biblio","music");;
			}
			break;	
		case "book":
			return new ReturnedArea("biblio","book");
			break;				
		case "search_music":
		
			$risultati_per_pagina=10;
			//@XXX: attenzione l'array deve contenere i nomi dei campi cercati
			// e le variabili che contengono le frasi devono avere lo stesso nome dei campi
			$campi = array("titles_music","authors_music");		
			$titles_music=trim($_REQUEST["titolo"]);
			$authors_music=trim($_REQUEST["autore"]);
			
			//controllo se tutte le richieste sono vuote
			if(empty($titles_music) && empty($authors_music) )
				return new ReturnedArea("biblio","music");
			
			insert_search_in_log("[Titolo_uniforme:"  .  $titles_music . "][Autore:".  $authors_music ."]", "m");
			$risultato=array();
			$i=0;
			
			//faccio l'intersezione di tutti i risultati solo se il campo cercato non è vuoto
			foreach($campi as $campo)
			{
		
				//se il campo richiest non è vuoto
				if(!empty($$campo))
				{
					//ricerco la frase contenuta nella variabile con lo stesso nome del campo
					$tmp =search_sentece($$campo,$campo);
					//echo $i . " . ";
					//print_r($tmp);
					if ($i==0)
						$risultato=$tmp;					
					else
						$risultato = array_intersect($risultato,$tmp);

					//tengo conto di quanti campi sono richiesti
					$i++;
				}
			}

			
			
			
			//print_r($risultato);
			$_SESSION["sentence"]=array("titolo"=>"titolo=". urlencode($_REQUEST["titolo"]),
										"autore"=>"autore=". urlencode($_REQUEST["autore"]));
										
		 	if (isset($_REQUEST["page"]))
				{
					$_SESSION["musics"]= get_author_title_musics($risultato, $_REQUEST["page"] ,  $risultati_per_pagina);									
					$_SESSION["sentence"]["page"]="page=".$_REQUEST["page"];
					$_SESSION["pagine"]=array("record_totali"=>count($risultato),
													"totali"=>ceil(count($risultato) / $risultati_per_pagina),
													"corrente"=>$_REQUEST["page"],
													"ris_per_pagina"=> $risultati_per_pagina
													);					
				}
			else
				$_SESSION["musics"]= get_author_title_musics($risultato);						
							
		
			return new ReturnedPage("result.php");
			break;
	}


?>
