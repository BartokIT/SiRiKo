<?php

/**
* Funzione che assegna per un certo gioco le nazioni in maniera uniforme ai giocatori, filtrando il gioco per continente
*/
function assign_country_and_units($id_game, $continent)
{
	//----- TABLES_NAME ----
	global $table_name_participant;
	global $table_name_country;

	
	$gamers = array();
	$countries = array();
	
	$sql_string_gamer = "SELECT porder, user_session FROM $table_name_participant WHERE (ext_game = $id_game )";
	$sql_string_country = "SELECT iso_code FROM $table_name_country WHERE (continent = \"$continent\") ORDER BY color";
		
	$result = mysql_query($sql_string_gamer);
	
	//Prelevo tutti quanti i giocatori
	if ($result)
	{
		while ($row=mysql_fetch_row($result))
			$gamers[$row[0]] = $row[1];
	}
	else
	{
		die("#1 - [assign_country_and_units] impossibile ottenere l'elenco dei partecipanti alla partita " . mysql_error());
	}
	
	//Prelevo tutte le nazioni disponibili per il continente selezionato
	$result = mysql_query($sql_string_country);
		
	if ($result)
	{
		while ($row=mysql_fetch_row($result))
			$countries[$row[0]] = $row[0];

	}
	else
	{
		die("#2 - [assign_country_and_units] impossibile ottenere l'elenco delle nazioni del continente EU " . mysql_error());
	}
	
	$gamers_order = array_keys($gamers);
	$num_gamers = count($gamers_order);
	$num_country =count($countries);
	$num_units_for_country = (int) floor(TOTAL_UNITS / $num_country);
	
	$nth_player = 0;
	foreach ($countries as $country)
	{
		//assegno ad ogni giocatore una nazione ed un numreo uguale di unità
		assign_country($id_game, $gamers_order[$nth_player], $country,$num_units_for_country );
		$nth_player = ($nth_player + 1) % $num_gamers;
	}
	
}

/**
* Funzione che assegna una determinata nazione ad un giocatore con un certo numero di unità
*/
function assign_country($id_game, $player, $country_code,$units )
{
	global $table_name_gamer_country;
	$sql_string_check = "SELECT porder FROM $table_name_gamer_country WHERE (ext_id_game=$id_game) AND (ext_iso_country = \"$country_code\")";
	
	$result = mysql_query($sql_string_check);
	$country_owner = -1;
	if ($result)
	{
		if (mysql_num_rows($result))
		{
			$row=mysql_fetch_row($result);
			if ($row[0] != null)
				$country_owner   = $row[0];			
		}	
	}
	else
		die("#1 - [assign_country] impossibile controllare l'appartenenza dello stato - " . mysql_error());
	
	//Nel caso in cui non sia in possesso di nessuno inserisco una nuova riga
	if ($country_owner == -1)
	{
		$sql_string_insert = "INSERT INTO $table_name_gamer_country(ext_id_game, ext_iso_country, porder, number_units) VALUE ($id_game, \"$country_code\", $player, $units)";
		$result = mysql_query($sql_string_insert);
		
		if (!$result)
				die("#2 - [assign_country] impossibile inserire una assegnazione per lo stato $country_code - " . mysql_error());
	}
	else
	{
		$sql_string_update = "UPDATE  $table_name_gamer_country SET porder=$player, number_units= $units WHERE (ext_id_game = $id_game) AND ( ext_iso_country = \"$country_code\")";
		$result = mysql_query($sql_string_update);
		
		if (!$result)
				die("#3 - [assign_country] impossibile aggiornare una assegnazione per lo stato $country_code - " . mysql_error());
	}	
}
/**
* Funzione che restituisce le unità disposte su ciascun territorio, suddivise prima per giocatore e poi per nazione
*/
function get_units_disposition($id_game)
{
	global $table_name_gamer_country;
	global $table_name_country;
	$sql_string_check = "SELECT g.porder, g.ext_iso_country, g.number_units, c.name FROM $table_name_gamer_country g, $table_name_country c WHERE (c.iso_code = g.ext_iso_country) AND (ext_id_game=$id_game)";
	
	$result = mysql_query($sql_string_check);
	$units_dispos = array();
	
	if ($result)
	{
		while ($row=mysql_fetch_row($result))
		{
			//Suddivido per player
			if (!isset($units_dispos[(int)$row[0]]))
				$units_dispos[(int)$row[0]] = array();	
			$country = explode(" ",$row[3] );
			$units_dispos[(int)$row[0]][$row[1]]=array("country"=>$country[0], "units"=>$row[2]);
		}
	}
	else
	{
		die("#1 - [get_units_disposition] impossibile ottenere l'elenco dei partecipanti alla partita " . mysql_error());
	}
	
	return $units_dispos;
}
/*
* Restituisce l'insieme dei codici  delle nazioni vicine a quella passata
**/
function get_country_neighbors($country_name)
{
	global $table_name_gamer_country;
	global $table_name_country;
	
	$sql_string = "SELECT neighbors FROM $table_name_country WHERE (name = \"$country_name\")";
	$result = mysql_query($sql_string);
	
	$countries_string = "";
	$countries = array();
	if ($result)
	{
		if ($row = mysql_fetch_row($result))
		{
			$countries_string = $row[0];
		}
		
		$countries = explode(";",$countries_string);
	}
	else
	{
		die("#1 - [get_country_neighbors] impossibile ottenere l'elenco dei vicini alla nazione $country_name " . mysql_error());	
	}
	
	return $countries;
}

/*
* Restituisce l'insieme dei codici  delle nazioni vicine a quella passata
**/
function get_country_neighbors_from_iso_code($country_code)
{
	global $table_name_gamer_country;
	global $table_name_country;
	
	$sql_string = "SELECT neighbors FROM $table_name_country WHERE (iso_code = \"$country_code\")";
	$result = mysql_query($sql_string);
	
	$countries_string = "";
	$countries = array();
	if ($result)
	{
		if ($row = mysql_fetch_row($result))
		{
			$countries_string = $row[0];
		}
		
		$countries = explode(";",$countries_string);
	}
	else
	{
		die("#1 - [get_country_neighbors] impossibile ottenere l'elenco dei vicini alla nazione $country_name " . mysql_error());	
	}
	
	return $countries;
}

/*
* Restituisce i nomi lunghi delle nazioni insieme al possessore e alle unità
**/
function get_country_units_and_owner($game_id, $country_code)
{
	$country_array = array($country_code);
	$result =  get_countries_units_and_owner($game_id, $country_array);
	if (count($result))
		return array_pop($result);
	else
		return array();
}

/*
* Restituisce i nomi lunghi delle nazioni insieme al possessore e alle unità
**/
function get_countries_units_and_owner($game_id, $country_codes)
{
	global $table_name_gamer_country;
	global $table_name_country;
	
	$info =array();
	
	if (count($country_codes) == 0)
		return $info;
		
	$in_string = implode("\",\"",$country_codes);

	$sql_string = "SELECT c.iso_code ,c.name, i.porder, i.number_units  FROM $table_name_gamer_country i, $table_name_country c WHERE (c.iso_code IN (\"" . $in_string . "\")) AND (c.iso_code = i.ext_iso_country) AND (i.ext_id_game = $game_id)";

	$result = mysql_query($sql_string);
	
	if ($result)
	{
		while ($row = mysql_fetch_row($result))
		{
			$info[$row[0]] = array("name" => $row[1], "gamer"=>$row[2], "units"=>$row[3]);
		}
	}
	else
	{
		die("#1 - [get_country_units_and_owner] impossibile ottenere l'elenco dei vicini alla nazione $country_codes " . mysql_error());	
	}	
	
	return $info;
}
/**
* Dato il codice iso restituisce il full name
*/
function get_country_name($iso_code)
{
		global $table_name_country;
		$country_name = "";
		
		$sql_string = "SELECT name FROM $table_name_country WHERE (iso_code = \"$iso_code\")";
		
		$result = mysql_query($sql_string);
		
		if ($result)
		{
			if ($row = mysql_fetch_row($result))
				$country_name = $row[0];
		}
		else
		{
			die("#1 - [get_country_name] impossibile ottenere il nome lungo di $iso_code " . mysql_error());	
		}		
		
		return $country_name;
}

function get_country_code_and_owner($game_id, $country_name)
{
	global $table_name_gamer_country;
	global $table_name_country;

	$country_info = array();
	$sql_string = "SELECT c.iso_code ,c.name, i.porder, i.number_units  FROM $table_name_gamer_country i, $table_name_country c WHERE (c.name LIKE\"%$country_name%\") AND (c.iso_code = i.ext_iso_country) AND (i.ext_id_game = $game_id)";	
	
	$result = mysql_query($sql_string);
	
	if ($result)
	{

		if ($row = mysql_fetch_row($result))
		{
			$country_info["iso_code"] = $row[0];
			$country_info["owner"] = $row[2];
			$country_info["units"] = $row[3];			
		}
	}
	else
	{
		die("#1 - [get_country_code_and_owner] impossibile ottenere informazioni sulla nazione $country_name " . mysql_error());	
	}
	return $country_info;
	
}
?>
