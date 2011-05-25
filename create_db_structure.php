<?php
require_once("abstraction/sql.php");
require_once("abstraction/sql_manager.php");

if (execute_sql_file($id_db_connection,$db_name, "abstraction/bse3_structure.sql"))
	echo "OK";
else
	echo "HELP";

?>


