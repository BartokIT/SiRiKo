<?php


$table_name_gamer_country = "gamer_country_info";
$table_name_participant="game_participants";
$table_name_country = "game_country";
$table_name_status = "game_status";

/**
* Restituisce l'elenco delle partite in corso.
* @return array
*/
function get_id_games()
{
	global $table_name_status;
	$sql_string="SELECT id_game FROM $table_name_status";
	$array_games= array();
	
	$result = mysql_query($sql_string);
	if ($result)
	{
		while ($row=mysql_fetch_row($result))
		{
			$array_games[] = $row[0];
		}
	}
	else
	{
		die("#1 - impossibile ottenere l'elenco dei partecipanti  " . mysql_error());
	}
	
	return $array_games;
}
/**
 * Restituisce l'elenco delle partite ancora aperte per ammettere giocatori
 * @TODO: inserire controllo sul numero massimo di giocatori per partita 
 */
function get_opened_games()
{
	global $table_name_status;
	global $table_name_participant;
	
	//$sql_string="SELECT s.game_name FROM $table_name_status s, $table_name_participant p WHERE (s.status=\"init\") AND (s.substatus!=\"trow_dice\") AND (s.id_game=p.ext_game)";
	$sql_string="SELECT s.game_name FROM $table_name_status s WHERE (s.status=\"init\") AND (s.substatus!=\"trow_dice\")";	
	$array_games= array();
	
	$result = mysql_query($sql_string);
	if ($result)
	{
		while ($row=mysql_fetch_row($result))
		{
			$array_games[] = array("name" =>$row[0]);
		}
	}
	else
	{
		die("#1 - impossibile ottenere l'elenco delle partite disponibili  " . mysql_error());
	}
	
	return $array_games;
}

/**
 * 
 * Permette di controllare se un nickname è già nella banca dati
 * @param unknown_type $nickname
 */
function check_nickname_existence($nickname)
{
	global $table_name_participant;
	
	$found = false;
	
	$sql_string = "SELECT nickname FROM $table_name_participant WHERE (nickname LIKE \"$nickname\")";
	$result = mysql_query($sql_string);
	if ($result)
	{
		if (mysql_num_rows($result) != 0)
		{
			$found = true;		
		}	
	}
	else
	{
		die("#1 - impossibile ottenere l'elenco delle partite disponibili  " . mysql_error());
	}	
	
	return $found;
}

/**
 *
 * Inserisco un nuovo utente nella base dati con il nickname passato
 * @param $nickname
 */
function insert_unbounded_gamer($nickname)
{
	global $table_name_participant;
	
	$nickname= mysql_escape_string($nickname);
	$sql_string = "INSERT INTO $table_name_participant(nickname, user_session, ext_game, porder) VALUE (\"$nickname\",\"" . session_id() . "\",-1,-1)";
	
	$result = mysql_query($sql_string);
	if (!$result)
	{
		die("#1 - impossibile inserire l'utente nella base date  " . mysql_error());
	}			
}


/**
 * 
 * Controlla se un utente è già nella base dati, ovvero se la sua sessione è memorizzata
 * @return boolean 
 */
function is_in_base()
{
	$table_name="game_participants";
	$user_id = mysql_escape_string($id_participant);
	$sql_string="SELECT ext_game, user_session FROM $table_name WHERE ( user_session =\"" . session_id() . "\")";
	
	$result = mysql_query($sql_string);
	if ($result)
	{
		if (mysql_num_rows($result))
		{
			$row=mysql_fetch_row($result);
			return $row[0];
		}
		else
		{
			return false;	
		}
	}
	else
	{
		die("#1 [is_in_base] - impossibile ottenere l'elenco dei partecipanti  " . mysql_error());
	}	
}


/**
 * 
 * Controlla se un utente è presente nella base dati, se non fa parte di alcun gioco, se fa parte di un gioco iniziato oppure se fa parte di un gioco non iniziato
 * @return string
 */
function is_in_initiated_game()
{
	global $table_name_participant;
	global $table_name_status;
	
	$user_id = mysql_escape_string(session_id());
	$sql_string="SELECT s.id_game, s.status, s.substatus FROM $table_name_participant p LEFT JOIN $table_name_status s ON p.ext_game = s.id_game WHERE (user_session =\"" . $user_id . "\" )";
	
	$result = mysql_query($sql_string);
	if ($result)
	{
		if (mysql_num_rows($result))
		{
			$row=mysql_fetch_row($result);
			if (($row[0] == "") || ($row[0] == "-1") || ($row[0] == NULL) )
				return "game_not_set"; //Se non fa parte di alcun gioco
			else
			{
				if ($row[1] == "init")
				{
					if (($row[2] == "") || ($row[2] == NULL))
					{
						return "game_not_initiated"; //fa parte di un gioco non iniziato
					}
					else
						return "game_initiated"; //fa parte di un gioco iniziato
				}
				else 
					return "game_initiated"; //fa parte di un gioco iniziato
			}
		}
		else
		{
			return "not_present"; //Se il giocatore non è presente	
		}
	}
	else
	{
		die("#1 [is_in_initiated_game] - impossibile ottenere l'elenco dei partecipanti  " . mysql_error());
	}	
}
/**
 * 
 * Enter description here ...
 * @param unknown_type $game_name
 */
function check_game_existance($game_name)
{
	global $table_name_status;

	$sql_string = "SELECT id_game FROM $table_name_status WHERE (game_name LIKE \"" . $game_name ."\")";
	$result = mysql_query($sql_string);
	if ($result)
	{
		if (mysql_num_rows($result))
		{
			$row = mysql_fetch_row($result);
			return $row[0];
		}
		else
			return false;
	}
	else
	{
		die("#1 [check_game_existance] - impossibile ottenere l'elenco dei partecipanti  " . mysql_error());
	}	
}
/**
 * 
 * Enter description here ...
 * @param unknown_type $name
 */
function insert_new_game($game_name)
{
	
	global $table_name_status;
	
	$game_name = mysql_escape_string($game_name);
	$sql_string = "INSERT INTO $table_name_status(game_name, round, gamer, status) VALUE (\"" . $game_name ."\",-1,-1,\"init\")";
	$result = mysql_query($sql_string);
	if (!$result)
	{
		die("#1 [insert_new_game] - impossibile ottenere l'elenco dei partecipanti  " . mysql_error());
	}
}
?>