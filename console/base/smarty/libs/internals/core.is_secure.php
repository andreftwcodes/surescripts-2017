<?php






function smarty_core_is_secure($params, &$smarty)
{
    if (!$smarty->security || $smarty->security_settings['INCLUDE_ANY']) {
        return true;
    }

    if ($params['resource_type'] == 'file') {
        $_rp = realpath($params['resource_name']);
        if (isset($params['resource_base_path'])) {
            foreach ((array)$params['resource_base_path'] as $curr_dir) {
                if ( ($_cd = realpath($curr_dir)) !== false &&
                     strncmp($_rp, $_cd, strlen($_cd)) == 0 &&
                     $_rp{strlen($_cd)} == DIRECTORY_SEPARATOR ) {
                    return true;
                }
            }
        }
        if (!empty($smarty->secure_dir)) {
            foreach ((array)$smarty->secure_dir as $curr_dir) {
                if ( ($_cd = realpath($curr_dir)) !== false &&
                     strncmp($_rp, $_cd, strlen($_cd)) == 0 &&
                     $_rp{strlen($_cd)} == DIRECTORY_SEPARATOR ) {
                    return true;
                }            
            }
        }
    } else {
        
        return call_user_func_array(
            $smarty->_plugins['resource'][$params['resource_type']][0][2],
            array($params['resource_name'], &$smarty));
    }

    return false;
}



?>
