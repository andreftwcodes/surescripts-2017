<?php




function smarty_make_timestamp($string)
{
    if(empty($string)) {
        $string = "now";
    }
    $time = strtotime($string);
    if (is_numeric($time) && $time != -1)
        return $time;

    
    if (preg_match('/^\d{14}$/', $string)) {
        $time = mktime(substr($string,8,2),substr($string,10,2),substr($string,12,2),
               substr($string,4,2),substr($string,6,2),substr($string,0,4));

        return $time;
    }

    
    $time = (int) $string;
    if ($time > 0)
        return $time;
    else
        return time();
}



?>
