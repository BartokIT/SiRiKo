<?php
$table_name_gamer_country = "gamer_country_info";
$table_name_participant="game_participants";
$table_name_country = "game_country";
$table_name_status = "game_status";

require_once("manage_country.php");
require_once("manage_games.php");
require_once("manage_gamer.php");





/**
* Restituisce lo stato corrente della partita
* @deprecated @XXX: cercare di sostituire con get_game_status_from_user()
*/
function get_current_turn_and_action($user_id)
{
	$current_status = array();
	$table_name_participant="game_participants";
	$table_name_status="game_status";
	
	$sql_string="SELECT s.round, s.gamer, s.status, i.user_session, s.id_game, s.substatus, s.data FROM $table_name_status s, $table_name_participant p, $table_name_participant i  WHERE (p.ext_game = s.id_game) AND (p.user_session =\"$user_id\") AND (i.porder = s.gamer)";

	$result = mysql_query($sql_string);
	if ($result)
	{

		if (mysql_num_rows($result))
		{
			$row=mysql_fetch_row($result);

			$current_status["round"] = $row[0];
			$current_status["current_gamer"] = $row[1];
			$current_status["status"] = $row[2];
			$current_status["substatus"] = $row[5];
			$current_status["user"] = $row[3];
			$current_status["id_game"] = $row[4];
			$current_status["data"] = $row[6];
		}
	}
	else
	{
		die("#1 - [get_current_turn_and_action] impossibile ottenere l'elenco dei partecipanti  " . mysql_error());
	}
	
	return $current_status;		
}

/**
* Imposta uno stato particolare di gioco
* @TODO: aggiungere impostazione azione e dati
*/
function set_current_status($id_game, $status, $substatus = null, $data=null, $gamer = null, $round = null)
{
	$table_name_status="game_status";
	
	$sql_string_assignments= "";

	if ($substatus == null)
		$substatus = "";
	else
		$substatus = mysql_escape_string($substatus);
		
	if ($gamer !== null)
		$sql_string_assignments	.= " , gamer= $gamer";

	if ($round !== null)
		$sql_string_assignments	.= " , round= $round";
	
	if ($data !== null)
	{
		$data = mysql_escape_string($data);
		$sql_string_assignments	.= " , data=\"$data\"";
	}
		

	$sql_string="UPDATE $table_name_status SET status = \"$status\" $sql_string_assignments , substatus=\"$substatus\" WHERE (id_game = $id_game)";

	$result = mysql_query($sql_string);
	if (!$result)
	{
		die("#1 - [set_current_status] impossibile ottenere l'elenco dei partecipanti  " . mysql_error());
	}	
}

/**
* Ordina i giocatori secondo il risultato dei dadi
*/
function compute_gamer_order($id_game, $dice_array)
{
	$table_name_participant="game_participants";
	
	//ordino l'array
	asort($dice_array);
	
	$participants = get_gamers($id_game);

	$count_order = 1;
	foreach ($dice_array as $player_order=>$value )
	{
		$sql_string ="UPDATE $table_name_participant SET porder=$count_order WHERE (user_session=\"" . $participants[$player_order]["user_session"] ."\")";
		$result = mysql_query($sql_string);
		if (!$result)
			die("#1 - [compute_gamer_order] impossibile variare l'ordine dei giocatori " . mysql_error());

		$count_order++;
	}
}

?>
