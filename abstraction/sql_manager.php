<?php

function execute_sql_file($id_sql_connection, $database_name,$sql_file)
{
	$select_result = mysql_select_db($database_name,$id_sql_connection);
	$install_path = dirname($_SERVER['SCRIPT_FILENAME']) . "/";

	if ($select_result == FALSE)
	{
		return FALSE;
	}
	else
	{
		$file_array= file($install_path . $sql_file);

		if ($file_array == FALSE)
		{
			//echo $install_path . $sql_file;
			return FALSE;
		}
		else
		{
			$no_problem = TRUE;
			$sql_string_commands = "";

			//scorro le righe per vedere quelle che iniziano con un commento

			foreach($file_array as $row)
			{
				$trim_row = trim($row);

				//se inizia con un commento le ignoro altrimenti le inserisco in un array
				if (!preg_match("/^--.*/", $trim_row))
				{
					$sql_string_commands .= " " . $trim_row;
				}
			}


			//splitto i vari comandi sql che sono divisi da un ";"
			$sql_commands = explode(";",$sql_string_commands);

			foreach ($sql_commands as $sql_query)
			{
				//echo $sql_query . "<br/>";
				//echo htmlspecialchars($content);

				$create_tables_result = mysql_query($sql_query);

				if (!$create_tables_result)
				{
					switch( mysql_errno())
					{
						//ERRORI GRAVI
						default: //errore sconosciuto
							echo mysql_error() . " " . mysql_errno() ." <br/>";
						case 1005: //errore di impossibilità creazione tabella - GRAVE
							$no_problem = $no_problem && FALSE;
							echo mysql_error() . " " . mysql_errno() ." <br/>";
							break;

							//ERRORI NON GRAVI
						case 1065: //query vuota
						case 1062: //chiave già esistente	
						case 1050: //tabella già esistente
					}
				}
					
			}

			return $no_problem;
		}
	}
}

?>
