<?php
 
if (isset($_REQUEST["chart_stat"]))
{
 
	switch ($_REQUEST["chart_stat"])
	{
		default:
//ini_set('error_reporting',E_ALL);
//ini_set('display_errors', 'On');
//ini_set('display_startup_errors','On');
			include("../abstraction/sql.php");				
			$statistics=Statistiche::get_daily_access_stat();
			//$keys=array_keys($statistics["access"]);

			include("pChart/pData.class");
			include("pChart/pChart.class");		


			$month = "11";
			$year = "2010";
			$giorni = date("t",strtotime("01/$month/$year"));
		 	$access = array();		
		 	$query = array();
		 		
		 						
			for ($i=1; $i <= $giorni;$i++)
			{
				$g =  date("Y-m-d",strtotime("$year-$month-$i"));
				if (array_key_exists($g, $statistics["access"]))
				{

					$access[$i]=$statistics["access"][$g];
					$query[$i]=$statistics["query_for_graph"][$g];				
				}
				else
				{
					$access[$i]=0;
					$query[$i]=0;
				}
				$keys[$i]="$i/$month/$year";
			}
//			print_r($access);
//			print_r($statistics["access"]);


			$DataSet = new pData;
			$DataSet->AddPoint($access,"Serie1");
			$DataSet->AddPoint($query,"Serie2");
			//$DataSet->AddPoint($statistics["access"],"Serie1");
			//$DataSet->AddPoint($statistics["query_for_graph"],"Serie2");	
			$DataSet->AddPoint($keys,"Serie3");
			$DataSet->AddAllSeries();
			$DataSet->RemoveSerie("Serie3");
			$DataSet->SetAbsciseLabelSerie("Serie3");
			$DataSet->SetSerieName("Accessi giornalieri","Serie1");
			$DataSet->SetSerieName("Query giornaliere","Serie2");
			$DataSet->SetYAxisName("Richieste");
			$DataSet->SetXAxisName("Giorni");

			 // Initialise the graph
			 $Test = new pChart(900,350);
			// $Test->setImageMap(TRUE,$MapID);
			 $Test->drawGraphAreaGradient(132,153,172,50,TARGET_BACKGROUND);
			 $Test->setFontProperties("../presentation/fonts/tahoma.ttf",9);
			$Test->setGraphArea(120,20,850,290);  //definisce l'area di grandezza del grafico
			$Test->drawGraphArea(213,217,221,TRUE);
			$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_ADDALL,213,217,221,TRUE,45,2,TRUE);
			 $Test->drawGraphAreaGradient(162,183,202,50);
			 $Test->drawGrid(4,TRUE,330,330,330,20);

			 // Draw the bar chart
			 $Test->drawStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),70);
			//$Test->drawBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),30);
			// Draw the title
			$Title = "Accessi al sito e ricerche effettuate\r\n  durante";
			$Test->drawTextBox(0,0,50,330,$Title,90,255,255,255,ALIGN_BOTTOM_CENTER,TRUE,0,0,0,30);

			 // Draw the legend
			 $Test->setFontProperties("../presentation/fonts/tahoma.ttf",8);
			 $Test->drawLegend(610,10,$DataSet->GetDataDescription(),236,238,240,52,58,82);

			 // Render the picture
			 $Test->Stroke();
		 		
	}
	
}

class Statistiche
{
	/**
	* Metodo statico che richiama tutte le funzioni per le statistiche
	*
	*/
	public static function add_to_statistics($frase, $type)
	{
	
		$first_access = false;
		
		#Conto il numero di accessi per l'utente nel merito
		if (!isset($_SESSION["statistics"]))
		{
			$_SESSION["statistics"]=array("count_search"=>1);
			$first_access = true;
		}
		else
		{
			$_SESSION["statistics"]["count_search"]= $_SESSION["statistics"]["count_search"] + 1;		
		}
		
		//vedo se la ricerca non è stata fatta nella sessione corrente
		if (!isset($_SESSION["statistics"]["session_log"]))
		{
			$_SESSION["statistics"]["session_log"]=array("b"=>array(), "m"=>array(), "r"=>array());
		}	
			
		self::add_to_daily_access($frase, $type, $first_access);
		self::add_browser_stat($first_access);
		self::add_operating_system_stat($first_access);
		self::add_query_string_stat($frase, $type);
	}

	public static function add_query_string_stat($frase, $type)
	{


		//TODO: inserire limitazione sul numero di righe nella tabella;
		$frase =  utf8_strtolower(trim($frase));
	
		//elimino gli spazi duplicati
		$parole_a = generalized::split('[ ]+',$frase );
		$frase_implode = implode(" ", $parole_a);
		$frase_escaped = mysql_real_escape_string($frase_implode);	
	
	
		//vedo se la ricerca non è stata fatta nella sessione corrente
		if (!isset($_SESSION["session_log"]))
		{
			$_SESSION["session_log"]=array("b"=>array(), "m"=>array(), "r"=>array());
		}
	
		if (!in_array($frase_implode, $_SESSION["session_log"][$type]))
		{
			//aggiungo la frase cercata nella sessione
			$_SESSION["session_log"][$type][]=$frase_implode;
		
			//Reperisco il mese corrente
			$now =getdate();
			$date_s = $now["year"] . "-" . $now["mon"] . "-01"; //la statistica la prendo mensile
					
			
			//cerco se la frase non si già presente
			$sql_string1 = "SELECT id, occorrenze FROM stats_query_monthly WHERE ( type=\"$type\")  AND (frase = \"$frase_escaped\") AND (date = '" . $date_s . "')";
	
			$result1= mysql_query($sql_string1);

			if (!$result1)
			{
				add_to_debug("Sql Error", "Impossibile cercare la frase nel log");
				return 1; //errore
			}
			else
				if (mysql_num_rows($result1) > 0)
				{

					$row1 = mysql_fetch_row($result1);
							 	 
					$sql_string2 = "UPDATE stats_query_monthly SET occorrenze = ". ($row1[1] + 1 ) .  " WHERE (id=". $row1[0].")";
					$result2 = mysql_query($sql_string2);
					if (!$result2)
						{
							add_to_debug("Sql Error", "Impossibile aggiornare dati nelle statistiche delle stringhe" . mysql_error() );
							return 2; //errore			
						}
				}
				else
				{
					$sql_string3 = "INSERT INTO stats_query_monthly (frase, occorrenze, type, date) VALUES (\"$frase_escaped\",1,\"$type\",\"$date_s\")";
					$result3 = mysql_query($sql_string3);
	
					if (!$result3)
					{
						add_to_debug("Sql Error", "Impossibile inserire dati nelle statistiche delle stringhe" .mysql_errno() . " " . mysql_error());
						return 3; //errore
					}
					return 0;
				}
		}
		else
			return 0;
	}
	
	
	/***************************************************************
	* Si occupa di aggiornare il database per le statistiche sugli accessi giornalieri
	* @input  frase - prende la frase che è stata ricercata
	*	  type - contiene b,m oppure r per denotare su quale archivio si e' effettuata la ricerca
	*	  first_access - contiene true solo se questa e' una sessione nuova
	*/	
	public static function add_to_daily_access($frase, $type, $first_access)
	{
	

		$now =getdate();
		$date_s = $now["year"] . "-" . $now["mon"] . "-" . $now["mday"];
		$retrieve_string= "SELECT access_num, query_num FROM stats_daily_access WHERE date='$date_s'";
		$result1=mysql_query($retrieve_string);

		if (!$result1)
		{
			add_to_debug("Sql Error", "Impossibile inserire dati nelle statistiche - " . mysql_errno() . " " . mysql_error());
			return 1; //errore		
		}	

		//controllo quanti accessi ci sono già stati
		if (mysql_num_rows($result1) > 0)
		{

			$row1 = mysql_fetch_row($result1);	
			if ($first_access)	
				$access_num = $row1[0] + 1;
			else
				$access_num = $row1[0];
				
			$query_num = $row1[1] + 1;	
			$sql_string2 = "UPDATE stats_daily_access SET query_num = ". ($query_num ) .  ", access_num =" . ($access_num) ." WHERE (date='". $date_s ."')";
			$result2 = mysql_query($sql_string2);
			if (!$result2)
				{
					add_to_debug("Sql Error", "Impossibile aggiornare i dati statistici 'stats_daily_access' log - " . mysql_error() );
					return 2; //errore			
				}
		}
		else
		{
			$sql_string3 = "INSERT INTO stats_daily_access (date, access_num, query_num) VALUES ('$date_s',1,1)";			
			$result3 = mysql_query($sql_string3);
			if (!$result3)
			{
				add_to_debug("Sql Error", "Impossibile inserire i dati statistici 'stats_daily_access' log - " . mysql_error() );
				return 3; //errore			
			}			
		}

				
		//print($retrieve_string);

		return 0;
		
	}
	
	/***************************************************************
	* Si occupa di restituire un array delle statistiche sugli accessi giornalieri
	* @input  frase - prende la frase che è stata ricercata
	*	  type - contiene b,m oppure r per denotare su quale archivio si e' effettuata la ricerca
	*	  first_access - contiene true solo se questa e' una sessione nuova
	*/	
	public static function get_daily_access_stat()
	{
	
		$access=array();
		$query=array();
		$query_for_graph=array();
		
		$now =getdate();
		$date_s = $now["year"] . "-" . $now["mon"] . "-" . $now["mday"];
		$retrieve_string= "SELECT access_num, query_num, date FROM stats_daily_access";
		$result1=mysql_query($retrieve_string);

		if (!$result1)
		{
			add_to_debug("Sql Error", "Impossibile inserire dati nelle statistiche - " . mysql_errno() . " " . mysql_error());
			return 1; //errore		
		}	

		//controllo quanti accessi ci sono già stati
		if (mysql_num_rows($result1) > 0)
		{

			while($row1 = mysql_fetch_row($result1))
			{
				$access[$row1[2]]=$row1[0];
				$query[$row1[2]]=$row1[1];
				$query_for_graph[$row1[2]]=  $row1[1]  -$row1[0];

			}	

		}
				
		//print($retrieve_string);

		return array("access"=>$access,"query"=>$query,"query_for_graph"=>$query_for_graph);
		
	}	
	
	public static function add_browser_stat($first_access)
	{
		
		$browser = mysql_real_escape_string(GetBrowser());
		if (strcmp($browser,"Other") == 0)
		{
			if (is_bot($browser))
			{		$now =getdate();
		$date_s = $now["year"] . "-" . $now["mon"] . "-01"; //la statistica la prendo mensile
		$retrieve_string= "SELECT count FROM stats_browser_monthly WHERE date='$date_s' AND browser='$browser'";
				$browser = "Bot";
			}
		}
		
		$now =getdate();
		$date_s = $now["year"] . "-" . $now["mon"] . "-01"; //la statistica la prendo mensile
		$retrieve_string= "SELECT count FROM stats_browser_monthly WHERE date='$date_s' AND browser='$browser'";
		$result1 = mysql_query($retrieve_string);

		if (!$result1)
		{
			add_to_debug("Sql Error", "Impossibile trovare dati dalle statistiche - " . mysql_errno() . " " . mysql_error());
			return 1; //errore
		}

		//controllo se è già presente nel database o meno
		if (mysql_num_rows($result1) > 0)
		{

			$row1 = mysql_fetch_row($result1);	
			$browser_count = $row1[0] + 1;	
			$sql_string2 = "UPDATE stats_browser_monthly SET count =" . ($browser_count) ." WHERE (date='". $date_s ."') AND (browser='$browser')";
			$result2 = mysql_query($sql_string2);
			if (!$result2)
				{
					add_to_debug("Sql Error", "Impossibile aggiornare i dati statistici 'stats_browser_monthly' - "  . mysql_errno() . " " . mysql_error());
					return 2; //errore			
				}
		}
		else
		{

			$sql_string3 = "INSERT INTO stats_browser_monthly (date, count, browser) VALUES ('$date_s',1,'$browser')";			
			$result3 = mysql_query($sql_string3);
			if (!$result3)
			{
				add_to_debug("Sql Error", "Impossibile inserire i dati statistici 'stats_browser_monthly'  - "  . mysql_errno() . " " . mysql_error());
				return 3; //errore			
			}			
		}		
		
		return 0;
	}
	
	public static function add_operating_system_stat($first_access)
	{
	
	//	if (!$first_access)
	//		return 0;
			
		$os = mysql_real_escape_string(GetSistemaOperativo());
		
		$now =getdate();
		$date_s = $now["year"] . "-" . $now["mon"] . "-01"; //la statistica la prendo mensile
		$retrieve_string= "SELECT count FROM stats_os_monthly WHERE date='$date_s' AND os='$os'";
		$result1 = mysql_query($retrieve_string);

		if (!$result1)
		{
			add_to_debug("Sql Error", "Impossibile trovare dati dalle statistiche - ". mysql_errno() . " " . mysql_error());
			return 1; //errore
		}

		//controllo se è già presente nel database o meno
		if (mysql_num_rows($result1) > 0)
		{

			$row1 = mysql_fetch_row($result1);	
			$os_count = $row1[0] + 1;	
			$sql_string2 = "UPDATE stats_os_monthly SET count =" . ($os_count) ." WHERE (date='". $date_s ."') AND (os='$os')";
			$result2 = mysql_query($sql_string2);
			if (!$result2)
				{
					add_to_debug("Sql Error", "Impossibile aggiornare i dati statistici 'stats_os_monthly' - "  . mysql_errno() . " " . mysql_error());
					return 2; //errore			
				}
		}
		else
		{
			$sql_string3 = "INSERT INTO stats_os_monthly (date, count, os) VALUES ('$date_s',1,'$os')";			
			$result3 = mysql_query($sql_string3);
			if (!$result3)
			{
				add_to_debug("Sql Error", "Impossibile inserire i dati statistici 'stats_os_monthly'  - "  . mysql_errno() . " " . mysql_error());
				return 3; //errore			
			}			
		}		
		
		return 0;
	}	
}

function is_bot($user_agent)
{
	  //if no user agent is supplied then assume it's a bot
	  if($user_agent == "")
	    return 1;

	  //array of bot strings to check for
	  $bot_strings = Array(  "google",     "bot",
		    "yahoo",     "spider",
		    "archiver",   "curl",
		    "python",     "nambu",
		    "twitt",     "perl",
		    "sphere",     "PEAR",
		    "java",     "wordpress",
		    "radian",     "crawl",
		    "yandex",     "eventbox",
		    "monitor",   "mechanize",
		    "facebookexternal", "msnbot"
		  );
		  
	  foreach($bot_strings as $bot)
	  {
	    if(strpos($user_agent,$bot) !== false)
	    {
	    	return 1;

	    }
	  }
	  
	  return 0;
}

/***
* Funzione che restituisce il browser usato
*/
function GetBrowser()
{
	$browser = array(
	'Lynx'      => 'Lynx',
	'Opera'     => 'Opera',
	'WebTV'     => 'WebTV',
	'Konqueror' => 'Konqueror',
	'MSIE'      => 'Internet Explorer',
	'Firefox'   => 'FireFox',
	'Nav'       => 'Netscape',
	'Gold'      => 'Netscape',
	'x11'       => 'Netscape',
	'Netscape'  => 'Netscape'
	);
	
	foreach($browser as $chiave => $valore)
	{
		if(strpos($_SERVER['HTTP_USER_AGENT'], $chiave ))
		{
			return $valore;
		}
	}
	
	return "Other";
}

/***
* Funzione che restituisce il sistema operativo usato
*/
function GetSistemaOperativo()
{
	$os = array(
	'Mac'             => 'Mac',
	'PPC'             => 'Mac',
	'Linux'           => 'Linux',
	'Windows NT 6.1'  => 'Windows 7',
	'Windows NT 5.1'  => 'Windows XP',
	'Windows NT 5.0'  => 'Windows 2000',
	'Windows NT 4.90' => 'Windows ME',
	'Win95'           => 'Windows 95',
	'Win98'           => 'Windows 98',
	'Windows NT 5.2'  => 'Windows NET',
	'WinNT4.0'        => 'Windows NT',
	'FreeBSD'         => 'FreeBSD',
	'SunOS'           => 'SunOS',
	'Irix'            => 'Irix',
	'BeOS'            => 'BeOS',
	'OS/2'            => 'OS/2',
	'AIX'             => 'AIX',
	'Windows'	  => 'Other Windows System'
	);

	foreach($os as $chiave => $valore)
	{
		if(strpos($_SERVER['HTTP_USER_AGENT'], $chiave))
		{
			return $valore;
		}
	}

	return 'Other';
}

?>
