<?php




function smarty_modifier_regex_replace($string, $search, $replace)
{
    if (preg_match('!\W(\w+)$!s', $search, $match) && (strpos($match[1], 'e') !== false)) {
        
        $search = substr($search, 0, -strlen($match[1])) . str_replace('e', '', $match[1]);
    }
    return preg_replace($search, $replace, $string);
}



?>
