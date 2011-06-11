<?php
/**
* Funzione per inizializzare l'area e la site view
*/

if (isset($_REQUEST["game_logic"]))
{
	function inizializza()
	{
		return new ReturnedArea("game","init", "throw_dice");	
	}

	define("INDEX", basename($_SERVER['SCRIPT_FILENAME']));
	define("TOTAL_UNITS", 50);
	include("support.php");

	//inclusione libreria, nel caso in cui è installata l'estensione mb_string è semplicemente un wrapper
	setlocale(LC_COLLATE, 'C');	

	//$stringa = 'Iñtër  nâtiônàl\'izætiøn Haendel and also Hàndel dell\'orto';
	$table_prefix = "idx_";


	//genero nuovo flusso di esecuzione
	$flusso = new Flusso("siriko_logic","nodo_principale");
	$flusso->elaborates();
}
else
{
	function inizializza()
	{
		return new ReturnedArea("public","default");	
	}

	define("INDEX", basename($_SERVER['SCRIPT_FILENAME']));
	define("TOTAL_UNITS", 50);
	include("support.php");

	//inclusione libreria, nel caso in cui è installata l'estensione mb_string è semplicemente un wrapper
	setlocale(LC_COLLATE, 'C');	

	//$stringa = 'Iñtër  nâtiônàl\'izætiøn Haendel and also Hàndel dell\'orto';
	$table_prefix = "idx_";


	//genero nuovo flusso di esecuzione
	$flusso = new Flusso("siriko","nodo_principale");
	$flusso->elaborates();
}
/*

	echo "<pre>";
	echo "SESSION ";
	print_r($_SESSION);

	echo "new ";
	print_r($new);

	print_debug();
	
	print_r($_REQUEST);
	
	
	echo "</pre>";
*/
//include($nome_file);

?>
