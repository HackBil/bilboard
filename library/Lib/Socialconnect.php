<?php

class Lib_Socialconnect
{
    // RD - Encode un mot de passe en utilisant l'algorithme SHA_256.
	public static function twitterDecode($connectionObject)
	{
		return Zend_Json::decode($connectionObject);
	}
}