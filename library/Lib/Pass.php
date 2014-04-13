<?php

class Lib_Pass
{
    // RD - Encode un mot de passe en utilisant l'algorithme SHA_256.
	public static function encode($str)
	{
		return "\$s256$".hash("sha256", $str);
	}

    // RD - Encode un mot de passe en utilisant l'algorithme SHA_256.
	public static function newpass()
	{
        $pass = "";
        $chaine = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $longeurChaine = strlen($chaine);
        
        for($i=1;$i<=12;$i++)
        {
            $placeAleatoire = mt_rand(0,($longeurChaine-1));
            $pass .= $chaine[$placeAleatoire];
        }
        return $pass;    
	}
}