<?php
require_once("abstraction/sql.php");
require_once("abstraction/sql_manager.php");

if (execute_sql_file($id_db_connection,$db_name, "siriko.sql"))
	echo "OK";
else
	echo "HELP";

?>


