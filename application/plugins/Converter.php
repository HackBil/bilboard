<?php

class Plugin_Converter
{
	public function __construct()
	{
	}
	public function convert_accents($str)
	{
		$accents = array (
							"�"		=>	"&agrave;",
							"�"		=>	"&acirc;",
							"�"		=>	"&eacute;",
							"�"		=>	"&ecirc;",
							"�"		=>	"&egrave;",
							"�"		=>	"&euml;",
							"�"		=>	"&icirc;",
							"�"		=>	"&iuml;",
							"�"		=>	"&ocirc;",
							"�"		=>	"&ouml;",
							"�"		=>	"&oelig;",
							"�"		=>	"&ugrave;",
							"�"		=>	"&uuml;",
							"�"		=>	"&ccedil;",
							"�"		=>	"&szlig;",
							"�"		=>	"&sect;",
							"\'"	=>	"'"
							);
						
		return str_replace(array_keys($accents), array_values($accents), $str);
	}
}