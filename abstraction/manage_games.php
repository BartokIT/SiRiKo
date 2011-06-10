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

function get_opened_games()
{
	global $table_name_status;
	$sql_string="SELECT id_game FROM $table_name_status WHERE (status=\"init\") AND (substatus!=\"trow_dice\")";
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
		die("#1 - impossibile ottenere l'elenco delle partite disponibili  " . mysql_error());
	}
	
	return $array_games;
}
?>
