<?php


//caricamento condizionale di index_manager e xml_manager
if (extension_loaded('mbstring') && function_exists("mb_ereg_replace") )
{
	//il set dell'engoding è già fatto dalla libreria utf8

	class generalized
	{

		public static function ereg_replace($pattern, $replace, $string)
		{
			return mb_ereg_replace($pattern, $replace, $string);
		}
	
		public static function split($pattern, $string)
		{
			return mb_split($pattern, $string);		
		}
	
		public static function ereg($pattern, $string)
		{
			return mb_ereg($pattern, $string);
		}

	}
}
else 
if (extension_loaded('iconv'))
{
	iconv_set_encoding("input_encoding","UTF-8");
	iconv_set_encoding("internal_encoding","UTF-8");
	iconv_set_encoding("output_encoding","UTF-8");		
	
	class generalized
	{

		public static function ereg_replace($pattern, $replace, $string)
		{
			return ereg_replace($pattern, $replace, $string);
		}
	
		public static function split($pattern, $string)
		{
			return split($pattern, $string);		
		}
	
		public static function ereg($pattern, $string)
		{
			return ereg($pattern, $string);
		}

	}	
}
else
	die("Impossibile trovare alcuna libreria per il supporto charset");


?>
