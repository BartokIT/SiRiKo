<?php
/*
$table_name_gamer_country = "gamer_country_info";
$table_name_participant="game_participants";
$table_name_country = "game_country";
$table_name_status = "game_status";
*/


function get_gamer_info()
{
	global $table_name_participant;
	$sql_string="SELECT p.porder, p.nickname FROM $table_name_participant p WHERE (p.user_session = \"". session_id() . "\")";	
	$gamer_info = array();

	$result = mysql_query($sql_string);
	if ($result)
	{
		if ($row=mysql_fetch_row($result))
		{
			$gamer_info["nickname"] = $row[1];
			$gamer_info["order"] = $row[0];			
		}
	}
	else
	{
		die("#1 - [get_gamer_info] impossibile ottenere infomrazioni sul giocatore " . mysql_error());
	}
	
	return $gamer_info;		
}
/**
* Questa funzione restituisce l'elenco dei partecipanti alla partita
*/
function get_gamers($id_game)
{
	$table_name="game_participants";
	$sql_string="SELECT s.user_session, s.porder FROM $table_name s WHERE (s.ext_game = $id_game)";
	$array_gamer= array();
	
	$result = mysql_query($sql_string);
	if ($result)
	{
		while ($row=mysql_fetch_row($result))
		{
			$array_gamer[$row[1]] =array("order"=> $row[1], "user_session"=>$row[0]);
		}
	}
	else
	{
		die("#1 - impossibile ottenere l'elenco dei partecipanti alla partita " . mysql_error());
	}
	
	return $array_gamer;	
}



/**
* Questa funzione restituisce l'elenco dei co-partecipanti alla partita
*/
function get_co_gamers()
{

	$user_session_id = mysql_escape_string(session_id());
	$table_name="game_participants";
	$sql_string="SELECT s.user_session, s.nickname, s.porder FROM $table_name t, $table_name s WHERE (t.ext_game = s.ext_game) AND (t.user_session=\"$user_session_id\")";
	$array_games= array();
	
	$result = mysql_query($sql_string);
	if ($result)
	{
		while ($row=mysql_fetch_row($result))
		{
			$array_games[$row[1]] = array("order"=> $row[2], "nickname"=> $row[1]);
		}
	}
	else
	{
		die("#1 - [get_co_gamers] impossibile ottenere l'elenco dei co-partecipanti alla partita " . mysql_error());
	}
	
	return $array_games;
}


/**
* Funzione che permette di sapere se l'utente corrente è quello con id più basso
* @return true|false
*/
function is_min_gamer()
{
	$result = null;
	
	$game_info = get_current_turn_and_action(session_id());
	
	if (count($game_info))
	{
		$min_gamer = get_first_gamer($game_info["id_game"]);
		if ($min_gamer["user_session"] == session_id())
			$result = true;
		else
			$result = false;
	}

	return $result;			
}

/**
* Funzione che permette di sapere se l'utente corrente è quello con id più alto nella partita corrente
* @return true|false
*/
function is_max_gamer()
{
	$result = null;
	
	$game_info = get_current_turn_and_action(session_id());
	
	if (count($game_info))
	{
		$min_gamer = get_last_gamer($game_info["id_game"]);
		if ($min_gamer["user_session"] == session_id())
			$result = true;
		else
			$result = false;
	}

	return $result;			
}


/** 
* Restituisce l'order ed il session id del primo giocatore specificando l'id della partita
* @return array porder - int, user_sessione - string
*/
function get_first_gamer($game_id)
{

	$gamer = array();
	$table_name_participant="game_participants";
	
	$sql_string="SELECT p.porder, p.user_session FROM $table_name_participant p WHERE (p.ext_game = $game_id) AND (p.porder = ( SELECT MIN(pmin.porder) FROM $table_name_participant pmin WHERE (pmin.ext_game = $game_id)))";
	
	
	$result = mysql_query($sql_string);

	if ($result)
	{
		if (mysql_num_rows($result))
		{
			$row=mysql_fetch_row($result);
			if ($row[0] != null)
			{
				$gamer["order"] = $row[0];
				$gamer["user_session"] = $row[1];
			}		
		}	
	}
	
	return $gamer;
}


/** 
* Restituisce l'order ed il session id dell'ultimo giocatore specificando l'id della partita
* @return array porder - int, user_sessione - string
*/
function get_last_gamer($game_id)
{

	$gamer = array();
	$table_name_participant="game_participants";
	
	$sql_string="SELECT p.porder, p.user_session FROM $table_name_participant p WHERE (p.ext_game = $game_id) AND (p.porder = ( SELECT MAX(pmin.porder) FROM $table_name_participant pmin))";
	$result = mysql_query($sql_string);

	if ($result)
	{
		if (mysql_num_rows($result))
		{
			$row=mysql_fetch_row($result);
			if ($row[0] != null)
			{
				$gamer["order"] = $row[0];
				$gamer["user_session"] = $row[1];
			}		
		}	
	}
	
	return $gamer;
}

/**
*
*
*/
function get_gamer_order($user_session)
{
	$gamer_order = -1;
	
	$table_name_participant="game_participants";
	
	$sql_string="SELECT p.porder FROM $table_name_participant p  WHERE (p.user_session =\"$user_session\")";
	
	$result = mysql_query($sql_string);
	if ($result)
	{
		if (mysql_num_rows($result))
		{
			$row=mysql_fetch_row($result);
			if ($row[0] != null)
				$gamer_order  = $row[0];
		}	
	}
	
	return $gamer_order;
}

/**
* Restituisce l'order del prossimo gamer. Se non c'è alcun prossimo allora restituisce -1;
* @return int
*/
function get_next_gamer($game_id, $user_order)
{
	$next_gamer = -1;
	$table_name_participant="game_participants";
	
	$sql_string="SELECT MIN(p.porder) FROM $table_name_participant p  WHERE (p.porder > $user_order)";
	
	$result = mysql_query($sql_string);
	if ($result)
	{
		if (mysql_num_rows($result))
		{
			$row=mysql_fetch_row($result);
			if ($row[0] != null)
				$next_gamer  = $row[0];			
		}	
	}
	else
		die("#1 - [get_next_gamer] impossibile ottenere il prossimo giocatore - " . mysql_error());
	
	return $next_gamer;
}

/**
* Restituisce l'order del prossimo gamer. Se non c'è alcun prossimo allora restituisce -1;
* @return int
*/
function set_next_gamer($game_id, $user_order)
{
	$next_gamer = -1;
	$current_status = array();
//	$table_name_status="game_status";
	global $table_name_status;
	
	$sql_string="UPDATE $table_name_status SET gamer = \"$user_order\"";
	$result = mysql_query($sql_string);
	if (!$result)
	{
		die("#1 - [set_next_gamer] Impossibile impostare prossimo gamer - " . mysql_error());	
	}
}


function get_current_gamer()
{

	$gamer_order = -1;
	global $table_name_status;
	//Prelevo lo stato corrente per ottenere l'id della partita
	$game_info=get_game_status_from_user();
	
	if (count($game_info) > 0)
	{
		$sql_string = "SELECT s.gamer FROM $table_name_status s WHERE ( s.id_game=" . $game_info["id_game"] . ")";
		$result = mysql_query($sql_string);
		
		if ($result)
		{
			if (mysql_num_rows($result))
			{
				$row=mysql_fetch_row($result);
				if ($row[0] != null)
					$gamer_order  = $row[0];			
			}	
		}
		else
			die("#1 - [get_current_gamer] Impossibile recuperare il giocatore corrente - " . mysql_error());	
	}
	
	return $gamer_order;
}
?>
