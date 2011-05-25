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
			return new ReturnedPage("book.php");		
			break;
		case "detail":
		if (@is_numeric($_REQUEST["bid"]))
		{
			$book_id = $_REQUEST["bid"];
			$_SESSION["book"]=get_book($book_id);
			if (isset($_REQUEST["page"]))
			{
				$_SESSION["sentence"]["page"]="page=".$_REQUEST["page"];
			}
			return new ReturnedPage("detail.php");	
		} else
		{
			return new ReturnedArea("biblio","book");
		}
			break;
		case "music":
			return new ReturnedArea("biblio","music");
			break;	
		case "search_book":
			$risultati_per_pagina = 10;
			//attenzione l'array deve contenere i nomi dei campi cercati e le variabili che contengono le frasi devono avere lo stesso nome dei campi
			$campi = array("titles_book","authors_book","subjects_book");		
			$titles_book=trim($_REQUEST["titolo"]);
			$authors_book=trim($_REQUEST["autore"]);
			$subjects_book=trim($_REQUEST["soggetto"]);
			
			//controllo se tutte le richieste sono vuote
			if(empty($titles_book) && empty($authors_book) && empty($subjects_book))
				return new ReturnedArea("biblio","book");
			
//			insert_search_in_log("[Titolo:"  .  $titles_book . "][Autore:".  $authors_book ."][Soggetto:". $subjects_book . "]", "b");
			Statistiche::add_to_statistics("[Titolo:"  .  $titles_book . "][Autore:".  $authors_book ."][Soggetto:". $subjects_book . "]", "b");
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
										"autore"=>"autore=". urlencode($_REQUEST["autore"]),
										"soggetto"=>"soggetto=". urlencode($_REQUEST["soggetto"]));
										
			if (isset($_REQUEST["page"]))
			{
				$_SESSION["books"]= get_books($risultato, $_REQUEST["page"] , $risultati_per_pagina);									
				$_SESSION["sentence"]["page"]="page=".$_REQUEST["page"];
				$_SESSION["pagine"]=array("record_totali"=>count($risultato),
											"totali"=>ceil(count($risultato) / $risultati_per_pagina),
											"corrente"=>$_REQUEST["page"],
											"ris_per_pagina"=> $risultati_per_pagina
											);					
			}
			else
				$_SESSION["books"]= get_books($risultato);
				
			return new ReturnedPage("result.php");
			break;
	}


?>
