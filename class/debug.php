<?php

$debug=array();

function add_to_debug($label, $object)
{
	global $debug;
	$debug[]=array("name"=>$label, "value"=>$object);
}

function print_debug()
{
	global $debug;
	
	foreach ($debug as $to_print)
	{
		echo "<div>";
			echo "<strong>$to_print[name]</strong>";
			
			if (is_array($to_print["value"]) || is_object($to_print["value"]))
				{
					echo "<pre>";
					print_r($to_print["value"]);
					echo "</pre>";			
				}
			else
				echo "<pre>" . $to_print["value"] . "</pre>";
		echo "</div>";
	}
}
?>
