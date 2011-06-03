<?php

$db_host="localhost";
$db_name="siriko";
$db_user="root";
$db_pass="";

global $id_db_connection;
$id_db_connection = @mysql_connect($db_host, $db_user, $db_pass);

if (!$id_db_connection)
{
	echo <<<EOF
	<div style="text-align:center">
		<div style="margin:auto; border: 3px dashed red;width:20em;height:5em;line-height:5em;">
			Impossibile connettersi al server database
		</div>	
	</div>
EOF;
	die();
}

if (!mysql_select_db($db_name,$id_db_connection))
{
	echo <<<EOF
	<div style="text-align:center">
		<div style="margin:auto; border: 3px dashed red;width:20em;height:5em;line-height:5em;">
			Impossibile connettersi al database
		</div>	
	</div>
EOF;
	die();
}
mysql_query("SET NAMES utf8");
?>
