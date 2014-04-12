<?php
class Zend_View_Helper_Smartstripslashes
{

    function Smartstripslashes($str)
    {
        $str = stripslashes($str);
        $cd1 = substr_count($str, "\"");
        $cd2 = substr_count($str, "\\\"");
        $cs1 = substr_count($str, "'");
        $cs2 = substr_count($str, "\\'");
        $tmp = strtr($str, array("\\\"" => "", "\\'" => ""));
        $cb1 = substr_count($tmp, "\\");
        $cb2 = substr_count($tmp, "\\\\");

        if ($cd1 == $cd2 && $cs1 == $cs2 && $cb1 == 2 * $cb2)
        {
            return strtr($str, array("\\\"" => "\"", "\\'" => "'", "\\\\" => "\\"));
        }
        return $str;
    }
}