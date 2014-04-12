<?php

class Plugin_Converter
{
	public function __construct()
	{
	}
	public function convert_accents($str)
	{
		$accents = array (
							"à"		=>	"&agrave;",
							"â"		=>	"&acirc;",
							"é"		=>	"&eacute;",
							"ê"		=>	"&ecirc;",
							"è"		=>	"&egrave;",
							"ë"		=>	"&euml;",
							"î"		=>	"&icirc;",
							"ï"		=>	"&iuml;",
							"ô"		=>	"&ocirc;",
							"ö"		=>	"&ouml;",
							"œ"		=>	"&oelig;",
							"ù"		=>	"&ugrave;",
							"ü"		=>	"&uuml;",
							"ç"		=>	"&ccedil;",
							"ß"		=>	"&szlig;",
							"§"		=>	"&sect;",
							"\'"	=>	"'"
							);
						
		return str_replace(array_keys($accents), array_values($accents), $str);
	}
}