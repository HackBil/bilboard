<?php
class Zend_View_Helper_Extrait
{


    function Extrait($string, $start = 0, $end = 20)
    {
        $arrString = explode(' ', $string);
        $count = 0;
        $strSubString = '';
        //print_r($arrString);

        foreach($arrString as $item)
        {
            $count = $count + strlen($item);
            //echo $count  . ' ';
            if($count<$end)
            {
                $strSubString .= ' ' . $item;
            }
            else
            {
                break;
            }
        }

        return $strSubString;

        //return substr($string, 0, $end);
//        $start = $start + 10;
//        $extrait = substr($string,0,$start);
//        $extrait = substr($string,0,strrpos($extrait," "));
//        $extrait2 = strstr(substr($string, -$end,$end)," ");
//
//        $totalExtrait = $extrait." ".$extrait2;
//
//        if(strlen($totalExtrait)==1)
//        {
//            return iconv_substr($string, 0, $start, 'UTF-8');
//        }
//        else
//        {
//            return $totalExtrait;
//        }
    }

    

}