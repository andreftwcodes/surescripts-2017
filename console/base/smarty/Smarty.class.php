<?php






define('DIR_SEP', DIRECTORY_SEPARATOR);


if (!defined('SMARTY_DIR')) {
    define('SMARTY_DIR', dirname(__FILE__) . DIR_SEP);
}

define('SMARTY_TEMPLATE', "");

define('SMARTY_PHP_PASSTHRU',   0);
define('SMARTY_PHP_QUOTE',      1);
define('SMARTY_PHP_REMOVE',     2);
define('SMARTY_PHP_ALLOW',      3);


class Smarty
{
    

    
    var $template_dir    =  'templates';

    
    var $compile_dir     =  'templates_c';

    
    var $config_dir      =  'configs';

    
    var $plugins_dir     =  array('plugins');

    
    var $debugging       =  false;

    
    var $debug_tpl       =  '';

    
    var $debugging_ctrl  =  'NONE';

    
    var $compile_check   =  true;

    
    var $force_compile   =  false;

    
    var $caching         =  1;

    
    var $cache_dir       =  '/www/httpd/html/';

    
    var $cache_lifetime  =  3600;

    
    var $cache_modified_check = false;

    
    var $php_handling    =  SMARTY_PHP_PASSTHRU;

    
    var $security       =   false;

    
    var $secure_dir     =   array();

    
    var $security_settings  = array(
                                    'PHP_HANDLING'    => false,
                                    'IF_FUNCS'        => array('array', 'list',
                                                               'isset', 'empty',
                                                               'count', 'sizeof',
                                                               'in_array', 'is_array',
															   'true','false'),
                                    'INCLUDE_ANY'     => false,
                                    'PHP_TAGS'        => false,
                                    'MODIFIER_FUNCS'  => array('count'),
                                    'ALLOW_CONSTANTS' => false
                                   );

    
    var $trusted_dir        = array();

    
    var $left_delimiter  =  '{';

    
    var $right_delimiter =  '}';

    
    var $request_vars_order    = "EGPCS"; 

    
    var $compile_id            = null;

    
    var $use_sub_dirs          = true;

    
    var $default_modifiers        = array();

    
    var $cache_handler_func   = null;
     
    
    var $global_assign   =  array('HTTP_SERVER_VARS' => array('SCRIPT_NAME'));

    
    var $undefined       =  null;

    
    var $autoload_filters = array();

         
    
    var $config_overwrite = true;

    
    var $config_booleanize = true;

    
    var $config_read_hidden = false;

    
    var $config_fix_newlines = true;
    
     
    
    var $default_template_handler_func = '';

    
    var $compiler_file        =    'Smarty_Compiler.class.php';

    
    var $compiler_class        =   'Smarty_Compiler';

    
    var $config_class          =   'Config_File';


    
    var $_error_msg            = false;

    
    var $_tpl_vars             = array();

    
    var $_smarty_vars          = null;

    
    var $_sections             = array();

    
    var $_foreach              = array();

    
    var $_tag_stack            = array();

    
    var $_conf_obj             = null;

    
    var $_config               = array(array('vars'  => array(), 'files' => array()));

    
    var $_smarty_md5           = 'f8d698aea36fcbead2b9d5359ffca76f';

    
    var $_version              = '2.5.0';

    
    var $_inclusion_depth      = 0;

    
    var $_compile_id           = null;

    
    var $_smarty_debug_id      = 'SMARTY_DEBUG';

    
    var $_smarty_debug_info    = array();

    
    var $_cache_info           = array();

    
    var $_file_perms           = 0644;

    
    var $_dir_perms               = 0771;

    
    var $_reg_objects           = array();

    
    var $_plugins              = array(
                                       'modifier'      => array(),
                                       'function'      => array(),
                                       'block'         => array(),
                                       'compiler'      => array(),
                                       'prefilter'     => array(),
                                       'postfilter'    => array(),
                                       'outputfilter'  => array(),
                                       'resource'      => array(),
                                       'insert'        => array());

    
    
    function Smarty()
    {
        foreach ($this->global_assign as $key => $var_name) {
            if (is_array($var_name)) {
                foreach ($var_name as $var) {
                    if (isset($GLOBALS[$key][$var])) {
                        $this->assign($var, $GLOBALS[$key][$var]);
                    } else {
                        $this->assign($var, $this->undefined);
                    }
                }
            } else {
                if (isset($GLOBALS[$var_name])) {
                    $this->assign($var_name, $GLOBALS[$var_name]);
                } else {
                    $this->assign($var_name, $this->undefined);
                }
            }
        }
    }


    
    function assign($tpl_var, $value = null)
    {
        if (is_array($tpl_var)){
            foreach ($tpl_var as $key => $val) {
                if ($key != '') {
                    $this->_tpl_vars[$key] = $val;
                }
            }
        } else {
            if ($tpl_var != '')
                $this->_tpl_vars[$tpl_var] = $value;
        }
    }

        
    function assign_by_ref($tpl_var, &$value)
    {
        if ($tpl_var != '')
            $this->_tpl_vars[$tpl_var] = &$value;
    }
    
        
    function append($tpl_var, $value=null, $merge=false)
    {
        if (is_array($tpl_var)) {
			
            foreach ($tpl_var as $_key => $_val) {
                if ($_key != '') {
					if(!@is_array($this->_tpl_vars[$_key])) {
						settype($this->_tpl_vars[$_key],'array');
					}
					if($merge && is_array($_val)) {
						foreach($_val as $_mkey => $_mval) {
							$this->_tpl_vars[$_key][$_mkey] = $_mval;
						}
					} else {
						$this->_tpl_vars[$_key][] = $_val;
					}
                }
            }
        } else {
            if ($tpl_var != '' && isset($value)) {
				if(!@is_array($this->_tpl_vars[$tpl_var])) {
					settype($this->_tpl_vars[$tpl_var],'array');
				}
				if($merge && is_array($value)) {
					foreach($value as $_mkey => $_mval) {
						$this->_tpl_vars[$tpl_var][$_mkey] = $_mval;
					}
				} else {
					$this->_tpl_vars[$tpl_var][] = $value;
				}
            }
        }
    }

        
    function append_by_ref($tpl_var, &$value, $merge=false)
    {
        if ($tpl_var != '' && isset($value)) {
			if(!@is_array($this->_tpl_vars[$tpl_var])) {
			 settype($this->_tpl_vars[$tpl_var],'array');
			}
			if ($merge && is_array($value)) {
				foreach($value as $_key => $_val) {
					$this->_tpl_vars[$tpl_var][$_key] = &$value[$_key];
				}
			} else {
				$this->_tpl_vars[$tpl_var][] = &$value;
			}
        }
    }


        
    function clear_assign($tpl_var)
    {
        if (is_array($tpl_var))
            foreach ($tpl_var as $curr_var)
                unset($this->_tpl_vars[$curr_var]);
        else
            unset($this->_tpl_vars[$tpl_var]);
    }


        
    function register_function($function, $function_impl)
    {
        $this->_plugins['function'][$function] =
            array($function_impl, null, null, false);
    }

        
    function unregister_function($function)
    {
        unset($this->_plugins['function'][$function]);
    }

        
    function register_object($object, &$object_impl, $allowed = array(), $smarty_args = true)
    {
        settype($allowed, 'array');        
        settype($smarty_args, 'boolean');        
        $this->_reg_objects[$object] =
            array(&$object_impl, $allowed, $smarty_args);
    }

        
    function unregister_object($object)
    {
        unset($this->_reg_objects[$object]);
    }
    
    
        
    function register_block($block, $block_impl)
    {
        $this->_plugins['block'][$block] =
            array($block_impl, null, null, false);
    }

        
    function unregister_block($block)
    {
        unset($this->_plugins['block'][$block]);
    }

        
    function register_compiler_function($function, $function_impl)
    {
        $this->_plugins['compiler'][$function] =
            array($function_impl, null, null, false);
    }

        
    function unregister_compiler_function($function)
    {
        unset($this->_plugins['compiler'][$function]);
    }

        
    function register_modifier($modifier, $modifier_impl)
    {
        $this->_plugins['modifier'][$modifier] =
            array($modifier_impl, null, null, false);
    }

        
    function unregister_modifier($modifier)
    {
        unset($this->_plugins['modifier'][$modifier]);
    }

        
    function register_resource($type, $functions)
    {
        $this->_plugins['resource'][$type] =
            array((array)$functions, false);
    }

        
    function unregister_resource($type)
    {
        unset($this->_plugins['resource'][$type]);
    }

        
    function register_prefilter($function)
    {
        $this->_plugins['prefilter'][$function]
            = array($function, null, null, false);
    }

        
    function unregister_prefilter($function)
    {
        unset($this->_plugins['prefilter'][$function]);
    }

        
    function register_postfilter($function)
    {
        $this->_plugins['postfilter'][$function]
            = array($function, null, null, false);
    }

        
    function unregister_postfilter($function)
    {
        unset($this->_plugins['postfilter'][$function]);
    }

        
    function register_outputfilter($function)
    {
        $this->_plugins['outputfilter'][$function]
            = array($function, null, null, false);
    }

        
    function unregister_outputfilter($function)
    {
        unset($this->_plugins['outputfilter'][$function]);
    }    
    
        
    function load_filter($type, $name)
    {
        switch ($type) {
            case 'output':
                $this->_load_plugins(array(array($type . 'filter', $name, null, null, false)));
                break;

            case 'pre':
            case 'post':
                if (!isset($this->_plugins[$type . 'filter'][$name]))
                    $this->_plugins[$type . 'filter'][$name] = false;
                break;
        }
    }

        
    function clear_cache($tpl_file = null, $cache_id = null, $compile_id = null, $exp_time = null)
    {
        
        if (!isset($compile_id))
            $compile_id = $this->compile_id;

	if (!isset($tpl_file))
	    $compile_id = null;

	$_auto_id = $this->_get_auto_id($cache_id, $compile_id);

        if (!empty($this->cache_handler_func)) {
            $_funcname = $this->cache_handler_func;
            return $_funcname('clear', $this, $dummy, $tpl_file, $cache_id, $compile_id);
        } else {
            return $this->_rm_auto($this->cache_dir, $tpl_file, $_auto_id, $exp_time);
        }
        
    }


        
    function clear_all_cache($exp_time = null)
    {
        if (!empty($this->cache_handler_func)) {
            $funcname = $this->cache_handler_func;
            return $funcname('clear', $this, $dummy);
        } else {
            return $this->_rm_auto($this->cache_dir,null,null,$exp_time);
        }
    }


        
    function is_cached($tpl_file, $cache_id = null, $compile_id = null)
    {
        if (!$this->caching)
            return false;

        if (!isset($compile_id))
            $compile_id = $this->compile_id;

        return $this->_read_cache_file($tpl_file, $cache_id, $compile_id, $results);
    }


        
    function clear_all_assign()
    {
        $this->_tpl_vars = array();
    }

        
    function clear_compiled_tpl($tpl_file = null, $compile_id = null, $exp_time = null)
    {
        if (!isset($compile_id))
            $compile_id = $this->compile_id;
        return $this->_rm_auto($this->compile_dir, $tpl_file, $compile_id, $exp_time);
    }

        
    function template_exists($tpl_file)
    {
        return $this->_fetch_template_info($tpl_file, $source, $timestamp, true, true);
    }

        
    function &get_template_vars($name=null)
    {
		if(!isset($name)) {
        	return $this->_tpl_vars;
		}
		if(isset($this->_tpl_vars[$name])) {
			return $this->_tpl_vars[$name];
		}
    }

        
    function &get_config_vars($name=null)
    {
		if(!isset($name) && is_array($this->_config[0])) {
        	return $this->_config[0]['vars'];
		} else if(isset($this->_config[0]['vars'][$name])) {
			return $this->_config[0]['vars'][$name];
		}
    }

        
    function trigger_error($error_msg, $error_type = E_USER_WARNING)
    {
        trigger_error("Smarty error: $error_msg", $error_type);
    }


        
    function display($tpl_file, $cache_id = null, $compile_id = null)
    {
        $this->fetch($tpl_file, $cache_id, $compile_id, true);
    }

    
    function fetch($tpl_file, $cache_id = null, $compile_id = null, $display = false)
    {
        $_smarty_old_error_level = $this->debugging ? error_reporting() : error_reporting(error_reporting() & ~E_NOTICE);
        if($this->security && !in_array($this->template_dir, $this->secure_dir)) {
            
            array_unshift($this->secure_dir, $this->template_dir);
        }

        if (!$this->debugging && $this->debugging_ctrl == 'URL'
               && strstr($GLOBALS['HTTP_SERVER_VARS']['QUERY_STRING'], $this->_smarty_debug_id)) {
            
            $this->debugging = true;
        }        
        
        if ($this->debugging) {
            
            $debug_start_time = $this->_get_microtime();
            $this->_smarty_debug_info[] = array('type'      => 'template',
                                                'filename'  => $tpl_file,
                                                'depth'     => 0);
            $included_tpls_idx = count($this->_smarty_debug_info) - 1;
        }

        if (!isset($compile_id)) {
            $compile_id = $this->compile_id;
        }

        $this->_compile_id = $compile_id;
        $this->_inclusion_depth = 0;

        if ($this->caching) {
            if(!empty($this->_cache_info)) {
                
                $_cache_info = $this->_cache_info;
                $this->_cache_info = array();
            }
            if ($this->_read_cache_file($tpl_file, $cache_id, $compile_id, $_smarty_results)) {
                if (@count($this->_cache_info['insert_tags'])) {
                    $this->_load_plugins($this->_cache_info['insert_tags']);
                    $_smarty_results = $this->_process_cached_inserts($_smarty_results);
                }
                if ($display) {
                    if ($this->debugging)
                    {
                        
                        $this->_smarty_debug_info[$included_tpls_idx]['exec_time'] = $this->_get_microtime() - $debug_start_time;

                        $_smarty_results .= $this->_generate_debug_output();
                    }
                    if ($this->cache_modified_check) {
                        $last_modified_date = substr($GLOBALS['HTTP_SERVER_VARS']['HTTP_IF_MODIFIED_SINCE'], 0, strpos($GLOBALS['HTTP_SERVER_VARS']['HTTP_IF_MODIFIED_SINCE'], 'GMT') + 3);
                        $gmt_mtime = gmdate('D, d M Y H:i:s', $this->_cache_info['timestamp']).' GMT';
                        if (@count($this->_cache_info['insert_tags']) == 0
                            && $gmt_mtime == $last_modified_date) {
                            header("HTTP/1.1 304 Not Modified");
                        } else {
                            header("Last-Modified: ".$gmt_mtime);
                            echo $_smarty_results;
                        }
                    } else {
                            echo $_smarty_results;                        
                    }
                    error_reporting($_smarty_old_error_level);
                    return true;    
                } else {
                    error_reporting($_smarty_old_error_level);
                    return $_smarty_results;
                }
            } else {
                $this->_cache_info['template'][] = $tpl_file;
                if ($this->cache_modified_check) {
                    header("Last-Modified: ".gmdate('D, d M Y H:i:s', time()).' GMT');
                }
            }
            if(isset($_cache_info)) {
                
                $this->_cache_info = $_cache_info;
            }
        }

        if (count($this->autoload_filters)) {
            $this->_autoload_filters();
        }

        $_smarty_compile_path = $this->_get_compile_path($tpl_file);

        
        
        if ($display && !$this->caching && count($this->_plugins['outputfilter']) == 0) {
            if ($this->_process_template($tpl_file, $_smarty_compile_path))
            {
                include($_smarty_compile_path);
            }
        } else {
            ob_start();
            if ($this->_process_template($tpl_file, $_smarty_compile_path))
            {
                include($_smarty_compile_path);
            }
            $_smarty_results = ob_get_contents();
            ob_end_clean();

            foreach ((array)$this->_plugins['outputfilter'] as $output_filter) {
                $_smarty_results = $output_filter[0]($_smarty_results, $this);
            }
        }

        if ($this->caching) {
            $this->_write_cache_file($tpl_file, $cache_id, $compile_id, $_smarty_results);
            $_smarty_results = $this->_process_cached_inserts($_smarty_results);
        }

        if ($display) {
            if (isset($_smarty_results)) { echo $_smarty_results; }
            if ($this->debugging) {
                
                $this->_smarty_debug_info[$included_tpls_idx]['exec_time'] = ($this->_get_microtime() - $debug_start_time);

                echo $this->_generate_debug_output();
            }
            error_reporting($_smarty_old_error_level);
            return;
        } else {
            error_reporting($_smarty_old_error_level);
            if (isset($_smarty_results)) { return $_smarty_results; }
        }
    }


        
    function _assign_smarty_interface()
    {
        if (isset($this->_smarty_vars) && isset($this->_smarty_vars['request'])) {
            return;
		}

        $globals_map = array('g'  => 'HTTP_GET_VARS',
                             'p'  => 'HTTP_POST_VARS',
                             'c'  => 'HTTP_COOKIE_VARS',
                             's'  => 'HTTP_SERVER_VARS',
                             'e'  => 'HTTP_ENV_VARS');

        $_smarty_vars_request  = array();

        foreach (preg_split('!!', strtolower($this->request_vars_order)) as $c) {
            if (isset($globals_map[$c])) {
                $_smarty_vars_request = array_merge($_smarty_vars_request, $GLOBALS[$globals_map[$c]]);
            }
        }
        $_smarty_vars_request = @array_merge($_smarty_vars_request, $GLOBALS['HTTP_SESSION_VARS']);

        $this->_smarty_vars['request'] = $_smarty_vars_request;
		
    }


    
    function _generate_debug_output()
    {
        
        

        if(empty($this->debug_tpl)) {
            
            $this->debug_tpl = 'file:'.SMARTY_DIR.'debug.tpl';
            if($this->security && is_file($this->debug_tpl)) {
                $secure_dir[] = $this->debug_tpl;
            }
        }

        $_ldelim_orig = $this->left_delimiter;
        $_rdelim_orig = $this->right_delimiter;    

        $this->left_delimiter = '{';
        $this->right_delimiter = '}';

        $_force_compile_orig = $this->force_compile;
        $this->force_compile = true;
        $_compile_id_orig = $this->_compile_id;
        $this->_compile_id = null;

        $compile_path = $this->_get_compile_path($this->debug_tpl);
        if ($this->_process_template($this->debug_tpl, $compile_path))
        {
            ob_start();
            include($compile_path);
            $results = ob_get_contents();
            ob_end_clean();
        }
        $this->force_compile = $_force_compile_orig;
        $this->_compile_id = $_compile_id_orig;

        $this->left_delimiter = $_ldelim_orig;
        $this->right_delimiter = $_rdelim_orig;

        return $results;
    }

        
    function config_load($file, $section = null, $scope = 'global')
    {        
        if(@is_dir($this->config_dir)) {
            $_config_dir = $this->config_dir;            
        } else {
            
            $this->_get_include_path($this->config_dir,$_config_dir);
        }

        $_file_path = str_replace('//', '/' ,$_config_dir . '/' . $file);
        
        
        if(isset($section)) {
               $_compile_file = $this->_get_auto_filename($this->compile_dir, $section . ' ' . $file);
        } else {
               $_compile_file = $this->_get_auto_filename($this->compile_dir, $file);
        }

        
        if($this->force_compile || !file_exists($_compile_file) ||
            ($this->compile_check &&
                file_exists($_file_path) &&
                ( filemtime($_compile_file) != filemtime($_file_path) ))) {
            $_compile_config = true;
        } else {
            include($_compile_file);
			$_compile_config = empty($_config_vars);
        }
		
        if($_compile_config) {
            if(!is_object($this->_conf_obj)) {
                require_once SMARTY_DIR . $this->config_class . '.class.php';
                $this->_conf_obj = new $this->config_class($_config_dir);
                $this->_conf_obj->overwrite = $this->config_overwrite;
                $this->_conf_obj->booleanize = $this->config_booleanize;
                $this->_conf_obj->read_hidden = $this->config_read_hidden;
                $this->_conf_obj->fix_newlines = $this->config_fix_newlines;
                $this->_conf_obj->set_path = $_config_dir;
            }
            if($_config_vars = array_merge($this->_conf_obj->get($file),
                    $this->_conf_obj->get($file, $section))) {
                if(function_exists('var_export')) {
                    $_compile_data = '<?php $_config_vars = ' . var_export($_config_vars, true) . '; return true; ?>';                    
                } else {
                    $_compile_data = '<?php $_config_vars = unserialize(\'' . str_replace('\'','\\\'', serialize($_config_vars)) . '\'); return true; ?>';
                }
                $this->_write_file($_compile_file, $_compile_data, true);
                touch($_compile_file,filemtime($_file_path));
            }
        }
        
        if ($this->debugging) {
            $debug_start_time = $this->_get_microtime();
        }

        if ($this->caching) {
            $this->_cache_info['config'][] = $file;
        }

        $this->_config[0]['vars'] = @array_merge($this->_config[0]['vars'], $_config_vars);
        $this->_config[0]['files'][$file] = true;
        
        if ($scope == 'parent') {
                $this->_config[1]['vars'] = @array_merge($this->_config[1]['vars'], $_config_vars);
                $this->_config[1]['files'][$file] = true;
        } else if ($scope == 'global') {
            for ($i = 1, $for_max = count($this->_config); $i < $for_max; $i++) {
                    $this->_config[$i]['vars'] = @array_merge($this->_config[$i]['vars'], $_config_vars);
                    $this->_config[$i]['files'][$file] = true;
            }
        }

        if ($this->debugging) {
            $debug_start_time = $this->_get_microtime();
            $this->_smarty_debug_info[] = array('type'      => 'config',
                                                'filename'  => $file.' ['.$section.'] '.$scope,
                                                'depth'     => $this->_inclusion_depth,
                                                'exec_time' => $this->_get_microtime() - $debug_start_time);
        }
    
    }

        
	function &get_registered_object($name) {
		if (!isset($this->_reg_objects[$name]))
		$this->_trigger_fatal_error("'$name' is not a registered object");

		if (!is_object($this->_reg_objects[$name][0]))
		$this->_trigger_fatal_error("registered '$name' is not an object");

		return $this->_reg_objects[$name][0];		
	}	

    
        
    function _is_trusted($resource_type, $resource_name)
    {
        $_smarty_trusted = false;
        if ($resource_type == 'file') {
            if (!empty($this->trusted_dir)) {
                
                

                if (!empty($this->trusted_dir)) {
                    foreach ((array)$this->trusted_dir as $curr_dir) {
                        if (!empty($curr_dir) && is_readable ($curr_dir)) {
                            if (substr(realpath($resource_name),0, strlen(realpath($curr_dir))) == realpath($curr_dir)) {
                                $_smarty_trusted = true;
                                break;
                            }
                        }
                    }
                }
            }
        } else {
            
            $resource_func = $this->_plugins['resource'][$resource_type][0][3];
            $_smarty_trusted = $resource_func($resource_name, $this);
        }

        return $_smarty_trusted;
    }

    
        
    function _is_secure($resource_type, $resource_name)
    {
        if (!$this->security || $this->security_settings['INCLUDE_ANY']) {
            return true;
        }

        $_smarty_secure = false;
        if ($resource_type == 'file') {
            if (!empty($this->secure_dir)) {
                foreach ((array)$this->secure_dir as $curr_dir) {
                    if ( !empty($curr_dir) && is_readable ($curr_dir)) {
                        if (substr(realpath($resource_name),0, strlen(realpath($curr_dir))) == realpath($curr_dir)) {
                            $_smarty_secure = true;
                            break;
                        }
                    }
                }
            }
        } else {
            
            $resource_func = $this->_plugins['resource'][$resource_type][0][2];
            $_smarty_secure = $resource_func($resource_name, $_smarty_secure, $this);
        }

        return $_smarty_secure;
    }


        
    function _get_php_resource($resource, &$resource_type, &$php_resource)
    {
        $this->_parse_file_path($this->trusted_dir, $resource, $resource_type, $resource_name);

        
        
        if ($resource_type == 'file') {
            $readable = false;
            if(file_exists($resource_name) && is_readable($resource_name)) {
                $readable = true;
            } else {
                
                if($this->_get_include_path($resource_name,$_include_path)) {
                    $readable = true;
                }
            }
        } else if ($resource_type != 'file') {
            $readable = true;
			$template_source = null;
            $resource_func = $this->_plugins['resource'][$resource_type][0][0];
            $readable = $resource_func($resource_name, $template_source, $this);
        }

        
        if (method_exists($this, '_syntax_error')) {
            $error_func = '_syntax_error';
        } else {
            $error_func = 'trigger_error';
        }

        if ($readable) {
            if ($this->security) {
                if (!$this->_is_trusted($resource_type, $resource_name)) {
                    $this->$error_func("(secure mode) '$resource_type:$resource_name' is not trusted");
                    return false;
                }
            }
        } else {
            $this->$error_func("'$resource_type: $resource_name' is not readable");
            return false;
        }

        if ($resource_type == 'file') {
            $php_resource = $resource_name;
        } else {
            $php_resource = $template_source;
        }

        return true;
    }


        
    function _process_template($tpl_file, $compile_path)
    {
        
        if (!$this->force_compile && file_exists($compile_path)) {
            if (!$this->compile_check) {
                
                return true;
            } else {
                
                if (!$this->_fetch_template_info($tpl_file, $template_source,
                                                 $template_timestamp)) {
                    return false;
                }
                if ($template_timestamp <= filemtime($compile_path)) {
                    
                    return true;
                } else {
                    
                    $this->_compile_template($tpl_file, $template_source, $template_compiled);
                    $this->_write_compiled_template($compile_path, $template_compiled, $template_timestamp);
                    return true;
                }
            }
        } else {
            
            if (!$this->_fetch_template_info($tpl_file, $template_source,
                                             $template_timestamp)) {
                return false;
            }
            $this->_compile_template($tpl_file, $template_source, $template_compiled);
            $this->_write_compiled_template($compile_path, $template_compiled, $template_timestamp);
            return true;
        }
    }

        
    function _get_compile_path($tpl_file)
    {
        return $this->_get_auto_filename($this->compile_dir, $tpl_file,
                                         $this->_compile_id);
    }

       
    function _write_compiled_template($compile_path, $template_compiled, $template_timestamp)
    {
        
        $this->_write_file($compile_path, $template_compiled, true);
        touch($compile_path, $template_timestamp);
        return true;
    }

        
    function _parse_file_path($file_base_path, $file_path, &$resource_type, &$resource_name)
    {
        
        $_file_path_parts = explode(':', $file_path, 2);

        if (count($_file_path_parts) == 1) {
            
            $resource_type = 'file';
            $resource_name = $_file_path_parts[0];
        } else {
            $resource_type = $_file_path_parts[0];
            $resource_name = $_file_path_parts[1];
            if ($resource_type != 'file') {
                $this->_load_resource_plugin($resource_type);
            }
        }

        if ($resource_type == 'file') {
            if (!preg_match("/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/", $resource_name)) {
                
                
                foreach ((array)$file_base_path as $_curr_path) {
                    $_fullpath = $_curr_path . DIR_SEP . $resource_name;
                    if (file_exists($_fullpath) && is_file($_fullpath)) {
                        $resource_name = $_fullpath;
                        return true;
                    }
                    
                    if($this->_get_include_path($_fullpath, $_include_path)) {
                        $resource_name = $_include_path;
                        return true;
                    }
                }
                return false;
            }
        }

        
        return true;
    }


        
    function _fetch_template_info($tpl_path, &$template_source, &$template_timestamp, $get_source = true, $quiet = false)
    {
        $_return = false;
        if ($this->_parse_file_path($this->template_dir, $tpl_path, $resource_type, $resource_name)) {
            switch ($resource_type) {
                case 'file':
                    if ($get_source) {
                        $template_source = $this->_read_file($resource_name);
                    }
                    $template_timestamp = filemtime($resource_name);
                    $_return = true;
                    break;

                default:
                    
                    if ($get_source) {
                        $resource_func = $this->_plugins['resource'][$resource_type][0][0];
                        $_source_return = $resource_func($resource_name, $template_source, $this);
                    } else {
                        $_source_return = true;
                    }
                    $resource_func = $this->_plugins['resource'][$resource_type][0][1];
                    $_timestamp_return = $resource_func($resource_name, $template_timestamp, $this);
                    $_return = $_source_return && $_timestamp_return;
                    break;
            }
        }
        
        if (!$_return) {
            
            if (!empty($this->default_template_handler_func)) {
                if (!function_exists($this->default_template_handler_func)) {
                    $this->trigger_error("default template handler function \"$this->default_template_handler_func\" doesn't exist.");
                } else {
                	$funcname = $this->default_template_handler_func;
                	$_return = $funcname($resource_type, $resource_name, $template_source, $template_timestamp, $this);
				}
            }
        }

        if (!$_return) {
            if (!$quiet) {
                $this->trigger_error("unable to read template resource: \"$tpl_path\"");
            }
        } else if ($_return && $this->security && !$this->_is_secure($resource_type, $resource_name)) {
            if (!$quiet)
                $this->trigger_error("(secure mode) accessing \"$tpl_path\" is not allowed");
            $template_source = null;
            $template_timestamp = null;
            return false;
        }

        return $_return;
    }


        
    function _compile_template($tpl_file, $template_source, &$template_compiled)
    {
        if(file_exists(SMARTY_DIR.$this->compiler_file)) {
            require_once SMARTY_DIR.$this->compiler_file;            
        } else {
            
            require_once $this->compiler_file;
        }

        $smarty_compiler = new $this->compiler_class;

        $smarty_compiler->template_dir      = $this->template_dir;
        $smarty_compiler->compile_dir       = $this->compile_dir;
        $smarty_compiler->plugins_dir       = $this->plugins_dir;
        $smarty_compiler->config_dir        = $this->config_dir;
        $smarty_compiler->force_compile     = $this->force_compile;
        $smarty_compiler->caching           = $this->caching;
        $smarty_compiler->php_handling      = $this->php_handling;
        $smarty_compiler->left_delimiter    = $this->left_delimiter;
        $smarty_compiler->right_delimiter   = $this->right_delimiter;
        $smarty_compiler->_version          = $this->_version;
        $smarty_compiler->security          = $this->security;
        $smarty_compiler->secure_dir        = $this->secure_dir;
        $smarty_compiler->security_settings = $this->security_settings;
        $smarty_compiler->trusted_dir       = $this->trusted_dir;
        $smarty_compiler->_reg_objects      = &$this->_reg_objects;
        $smarty_compiler->_plugins          = &$this->_plugins;
        $smarty_compiler->_tpl_vars         = &$this->_tpl_vars;
        $smarty_compiler->default_modifiers = $this->default_modifiers;
        $smarty_compiler->compile_id        = $this->_compile_id;

        if ($smarty_compiler->_compile_file($tpl_file, $template_source, $template_compiled)) {
            return true;
        } else {
            $this->trigger_error($smarty_compiler->_error_msg);
            return false;
        }
    }

        
    function _smarty_include($_smarty_include_tpl_file, $_smarty_include_vars)
    {
        if ($this->debugging) {
            $debug_start_time = $this->_get_microtime();
            $this->_smarty_debug_info[] = array('type'      => 'template',
                                                'filename'  => $_smarty_include_tpl_file,
                                                'depth'     => ++$this->_inclusion_depth);
            $included_tpls_idx = count($this->_smarty_debug_info) - 1;
        }

        $this->_tpl_vars = array_merge($this->_tpl_vars, $_smarty_include_vars);

        
        
        array_unshift($this->_config, $this->_config[0]);

        $_smarty_compile_path = $this->_get_compile_path($_smarty_include_tpl_file);

        if ($this->_process_template($_smarty_include_tpl_file, $_smarty_compile_path)) {
            include($_smarty_compile_path);
        }

        
        array_shift($this->_config);

        $this->_inclusion_depth--;

        if ($this->debugging) {
            
            $this->_smarty_debug_info[$included_tpls_idx]['exec_time'] = $this->_get_microtime() - $debug_start_time;
        }

        if ($this->caching) {
            $this->_cache_info['template'][] = $_smarty_include_tpl_file;
        }
    }

        
    function _smarty_include_php($_smarty_include_php_file, $_smarty_assign, $_smarty_once, $_smarty_include_vars)
    {
        $this->_get_php_resource($_smarty_include_php_file, $_smarty_resource_type,
                                 $_smarty_php_resource);

        extract($_smarty_include_vars, EXTR_PREFIX_SAME, 'include_php_');

        if (!empty($_smarty_assign)) {
            ob_start();
            if ($_smarty_resource_type == 'file') {
                if($_smarty_once) {
                    include_once($_smarty_php_resource);
                } else {
                    include($_smarty_php_resource);                    
                }
            } else {
                eval($_smarty_php_resource);
            }
            $this->assign($_smarty_assign, ob_get_contents());
            ob_end_clean();
        } else {
            if ($_smarty_resource_type == 'file') {
                if($_smarty_once) {
                    include_once($_smarty_php_resource);
                } else {
                    include($_smarty_php_resource);                    
                }
            } else {
                eval($_smarty_php_resource);
            }
        }
    }

    
        
    function clear_config($var = null)
    {
        if(!isset($var)) {
            
            $this->_config = array(array('vars'  => array(),
                                         'files' => array()));
        } else {
            unset($this->_config[0]['vars'][$var]);            
        }
    }    
    
    
        
    function _process_cached_inserts($results)
    {
        preg_match_all('!'.$this->_smarty_md5.'{insert_cache (.*)}'.$this->_smarty_md5.'!Uis',
                       $results, $match);
        list($cached_inserts, $insert_args) = $match;

        for ($i = 0, $for_max = count($cached_inserts); $i < $for_max; $i++) {
            if ($this->debugging) {
                $debug_start_time = $this->_get_microtime();
            }

            $args = unserialize($insert_args[$i]);
            $name = $args['name'];

            if (isset($args['script'])) {
                if (!$this->_get_php_resource($this->_dequote($args['script']), $resource_type, $php_resource)) {
                    return false;
                }

                if ($resource_type == 'file') {
                    include_once($php_resource);
                } else {
                    eval($php_resource);
                }
            }

            $function_name = $this->_plugins['insert'][$name][0];
            $replace = $function_name($args, $this);

            $results = str_replace($cached_inserts[$i], $replace, $results);
            if ($this->debugging) {
                $this->_smarty_debug_info[] = array('type'      => 'insert',
                                                    'filename'  => 'insert_'.$name,
                                                    'depth'     => $this->_inclusion_depth,
                                                    'exec_time' => $this->_get_microtime() - $debug_start_time);
            }
        }

        return $results;
    }


        
    function _run_insert_handler($args)
    {
        if ($this->debugging) {
            $debug_start_time = $this->_get_microtime();
        }
    
        if ($this->caching) {
            $arg_string = serialize($args);
            $name = $args['name'];
            if (!isset($this->_cache_info['insert_tags'][$name])) {
                $this->_cache_info['insert_tags'][$name] = array('insert',
                                                                 $name,
                                                                 $this->_plugins['insert'][$name][1],
                                                                 $this->_plugins['insert'][$name][2],
                                                                 !empty($args['script']) ? true : false);
            }
            return $this->_smarty_md5."{insert_cache $arg_string}".$this->_smarty_md5;
        } else {
            if (isset($args['script'])) {
                if (!$this->_get_php_resource($this->_dequote($args['script']), $resource_type, $php_resource)) {
                    return false;
                }
    
                if ($resource_type == 'file') {
                    include_once($php_resource);
                } else {
                    eval($php_resource);
                }
                unset($args['script']);
            }
    
            $function_name = $this->_plugins['insert'][$args['name']][0];
            $content = $function_name($args, $this);
            if ($this->debugging) {
                $this->_smarty_debug_info[] = array('type'      => 'insert',
                                                    'filename'  => 'insert_'.$args['name'],
                                                    'depth'     => $this->_inclusion_depth,
                                                    'exec_time' => $this->_get_microtime() - $debug_start_time);
            }
    
            if (!empty($args["assign"])) {
                $this->assign($args["assign"], $content);
            } else {
                return $content;
            }
        }
    }


    
    function _run_mod_handler()
    {
        $args = func_get_args();
        list($modifier_name, $map_array) = array_splice($args, 0, 2);
        list($func_name, $tpl_file, $tpl_line) =
            $this->_plugins['modifier'][$modifier_name];
        $var = $args[0];

        if ($map_array && is_array($var)) {
            foreach ($var as $key => $val) {
                $args[0] = $val;
                $var[$key] = call_user_func_array($func_name, $args);
            }
            return $var;
        } else {
            return call_user_func_array($func_name, $args);
        }
    }


        
    function _dequote($string)
    {
        if (($string{0} == "'" || $string{0} == '"') &&
            $string{strlen($string)-1} == $string{0})
            return substr($string, 1, -1);
        else
            return $string;
    }


        
    function _read_file($filename, $start=null, $lines=null)
    {
        if (!($fd = @fopen($filename, 'r'))) {
            return false;
        }
        flock($fd, LOCK_SH);
        if ($start == null && $lines == null) {
            
            $contents = fread($fd, filesize($filename));
        } else {
            if ( $start > 1 ) {
                
                for ($loop=1; $loop < $start; $loop++) {
                    fgets($fd, 65536);
                }
            }
            if ( $lines == null ) {
                
                while (!feof($fd)) {
                    $contents .= fgets($fd, 65536);
                }
            } else {
                
                for ($loop=0; $loop < $lines; $loop++) {
                    $contents .= fgets($fd, 65536);
                    if (feof($fd)) {
                        break;
                    }
                }
            }
        }
        fclose($fd);
        return $contents;
    }

        
    function _write_file($filename, $contents, $create_dirs = false)
    {
		$_dirname = dirname($filename);
		
        if ($create_dirs) {
            $this->_create_dir_structure($_dirname);
		}

		
		
		$_tmp_file = $_dirname . '/' . uniqid('');
		
        if (!($fd = @fopen($_tmp_file, 'w'))) {
            $this->trigger_error("problem writing temporary file '$_tmp_file'");
            return false;
        }

        fwrite($fd, $contents);
        fclose($fd);
		
		if(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' && file_exists($filename)) {
			@unlink($filename);
		} 
		@rename($_tmp_file, $filename);
        chmod($filename, $this->_file_perms);

        return true;
    }

        
    function _get_auto_filename($auto_base, $auto_source = null, $auto_id = null)
    {
        static $_dir_sep = null;
        static $_dir_sep_enc = null;
        
        if(!isset($_dir_sep)) {
            $_dir_sep_enc = urlencode(DIR_SEP);
            if($this->use_sub_dirs) {
                $_dir_sep = DIR_SEP;
            } else {
                $_dir_sep = '^';        
            }
        }
        
        if(@is_dir($auto_base)) {
            $res = $auto_base . DIR_SEP;
        } else {
            
            $this->_get_include_path($auto_base,$_include_path);
            $res = $_include_path . DIR_SEP;
        }
        
        if(isset($auto_id)) {
            
            $auto_id = str_replace('%7C','|',(urlencode($auto_id)));
            
            $auto_id = str_replace('|', $_dir_sep, $auto_id);
            $res .= $auto_id . $_dir_sep;
        }
        
        if(isset($auto_source)) {
            
            if($this->use_sub_dirs) {
                $_filename = urlencode(basename($auto_source));
                $_crc32 = crc32($auto_source) . $_dir_sep;
                
                

                $_crc32 = substr($_crc32,0,3) . $_dir_sep . $_crc32;
                $res .= $_crc32 . $_filename . '.php';
            } else {
                $res .= str_replace($_dir_sep_enc,'^',urlencode($auto_source));
            }
        }
        $res = SMARTY_TEMPLATE . $res ;     


        return $res;
    }

        
    function _rm_auto($auto_base, $auto_source = null, $auto_id = null, $exp_time = null)
    {
        if (!@is_dir($auto_base))
          return false;

        if(!isset($auto_id) && !isset($auto_source)) {
            $res = $this->_rmdir($auto_base, 0, $exp_time);            
        } else {        
            $tname = $this->_get_auto_filename($auto_base, $auto_source, $auto_id);
            
            if(isset($auto_source)) {
                $res = $this->_unlink($tname);
            } elseif ($this->use_sub_dirs) {
                $res = $this->_rmdir($tname, 1, $exp_time);
            } else {
                
                $handle = opendir($auto_base);
		$res = true;
                while (false !== ($filename = readdir($handle))) {
                    if($filename == '.' || $filename == '..') {
                        continue;    
                    } elseif (substr($auto_base . DIR_SEP . $filename,0,strlen($tname)) == $tname) {
                        $res &= (bool)$this->_unlink($auto_base . DIR_SEP . $filename, $exp_time);
                    }
                }
            }
        }

        return $res;
    }

        
    function _rmdir($dirname, $level = 1, $exp_time = null)
    {

       if($handle = @opendir($dirname)) {

            while (false !== ($entry = readdir($handle))) {
                if ($entry != '.' && $entry != '..') {
                    if (@is_dir($dirname . DIR_SEP . $entry)) {
                        $this->_rmdir($dirname . DIR_SEP . $entry, $level + 1, $exp_time);
                    }
                    else {
                        $this->_unlink($dirname . DIR_SEP . $entry, $exp_time);
                    }
                }
            }

            closedir($handle);

            if ($level)
                @rmdir($dirname);
            
            return true;
        
        } else {
                return false;
        }
    }

        
    function _unlink($resource, $exp_time = null)
    {
        if(isset($exp_time)) {
            if(time() - filemtime($resource) >= $exp_time) {
                @unlink($resource);
            }
        } else {            
            @unlink($resource);
        }
    }
    
        
    function _create_dir_structure($dir)
    {
        if (!file_exists($dir)) {
            $_dir_parts = preg_split('!\\'.DIR_SEP.'+!', $dir, -1, PREG_SPLIT_NO_EMPTY);
            $_new_dir = ($dir{0} == DIR_SEP) ? DIR_SEP : '';
            
            
            $_open_basedir_ini = ini_get('open_basedir');
            if(!empty($_open_basedir_ini)) {
                $_use_open_basedir = true;
                $_open_basedir_sep = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') ? ';' : ':';
                $_open_basedirs = explode($_open_basedir_sep, $_open_basedir_ini);
            } else {                    
                $_use_open_basedir = false;
            }

            foreach ($_dir_parts as $_dir_part) {
                $_new_dir .= $_dir_part;

                if ($_use_open_basedir) {
                    $_make_new_dir = false;
                    foreach ($_open_basedirs as $_open_basedir) {
                        if (substr($_new_dir.'/', 0, strlen($_open_basedir)) == $_open_basedir) {
                            $_make_new_dir = true;
                            break;
                        }
                    }
                } else {
                    $_make_new_dir = true;                    
                }

                if ($_make_new_dir && !file_exists($_new_dir) && !@mkdir($_new_dir, $this->_dir_perms)) {
                    $this->trigger_error("problem creating directory \"$dir\"");
                    return false;
                }
                $_new_dir .= DIR_SEP;
            }
        }
    }

        
    function _write_cache_file($tpl_file, $cache_id, $compile_id, $results)
    {
        
        $this->_cache_info['timestamp'] = time();
        if ($this->cache_lifetime > -1){
            
            $this->_cache_info['expires'] = $this->_cache_info['timestamp'] + $this->cache_lifetime;
        } else {
            
            $this->_cache_info['expires'] = -1;
        }

        
        $results = serialize($this->_cache_info)."\n".$results;

        if (!empty($this->cache_handler_func)) {
            
            $_funcname = $this->cache_handler_func;
            return $_funcname('write', $this, $results, $tpl_file, $cache_id, $compile_id);
        } else {
            
            $_auto_id = $this->_get_auto_id($cache_id, $compile_id);
            $_cache_file = $this->_get_auto_filename($this->cache_dir, $tpl_file, $_auto_id);
            $this->_write_file($_cache_file, $results, true);
            return true;
        }
    }

        
    function _read_cache_file($tpl_file, $cache_id, $compile_id, &$results)
    {
        static  $content_cache = array();

        if ($this->force_compile) {
            
            return false;
        }

        if (isset($content_cache["$tpl_file,$cache_id,$compile_id"])) {
            list($results, $this->_cache_info) = $content_cache["$tpl_file,$cache_id,$compile_id"];
            return true;
        }

        if (!empty($this->cache_handler_func)) {
            
            $_funcname = $this->cache_handler_func;
            $_funcname('read', $this, $results, $tpl_file, $cache_id, $compile_id);
        } else {
            
            $_auto_id = $this->_get_auto_id($cache_id, $compile_id);
            $_cache_file = $this->_get_auto_filename($this->cache_dir, $tpl_file, $_auto_id);
            $results = $this->_read_file($_cache_file);
        }

        if (empty($results)) {
            
            return false;
        }

        $cache_split = explode("\n", $results, 2);
        $cache_header = $cache_split[0];

        $this->_cache_info = unserialize($cache_header);

        if ($this->caching == 2 && isset ($this->_cache_info['expires'])){
            
            if ($this->_cache_info['expires'] > -1 && (time() > $this->_cache_info['expires'])) {
            
            return false;
            }
        } else {
            
            if ($this->cache_lifetime > -1 && (time() - $this->_cache_info['timestamp'] > $this->cache_lifetime)) {
            
            return false;
            }
        }

        if ($this->compile_check) {
            foreach ($this->_cache_info['template'] as $template_dep) {
                $this->_fetch_template_info($template_dep, $template_source, $template_timestamp, false);
                if ($this->_cache_info['timestamp'] < $template_timestamp) {
                    
                    return false;
                }
            }

            if (isset($this->_cache_info['config'])) {
                foreach ($this->_cache_info['config'] as $config_dep) {
                    if ($this->_cache_info['timestamp'] < filemtime($this->config_dir.DIR_SEP.$config_dep)) {
                        
                        return false;
                    }
                }
            }
        }

        $results = $cache_split[1];
        $content_cache["$tpl_file,$cache_id,$compile_id"] = array($results, $this->_cache_info);

        return true;
    }

    
    function _get_auto_id($cache_id=null, $compile_id=null) {
	if (isset($cache_id))
	    return (isset($compile_id)) ? $cache_id . '|' . $compile_id  : $cache_id;
	elseif(isset($compile_id))
	    return $compile_id;
	else
	    return null;
    }

        
    function _get_plugin_filepath($type, $name)
    {
        $_plugin_filename = "$type.$name.php";
        
        foreach ((array)$this->plugins_dir as $_plugin_dir) {

            $_plugin_filepath = $_plugin_dir . DIR_SEP . $_plugin_filename;

            
            if (!preg_match("/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/", $_plugin_dir)) {
                $_relative_paths[] = $_plugin_dir;
                
                if (@is_readable(SMARTY_DIR . $_plugin_filepath)) {
                    return SMARTY_DIR . $_plugin_filepath;
                }
            }
            
            if (@is_readable($_plugin_filepath)) {
                return $_plugin_filepath;
            }
        }

        
        if(isset($_relative_paths)) {
            foreach ((array)$_relative_paths as $_plugin_dir) {

                $_plugin_filepath = $_plugin_dir . DIR_SEP . $_plugin_filename;

                if ($this->_get_include_path($_plugin_filepath, $_include_filepath)) {
                    return $_include_filepath;
                }
            }
        }
        
        
        return false;
    }

        
    function _load_plugins($plugins)
    {
        
        foreach ($plugins as $plugin_info) {            
            list($type, $name, $tpl_file, $tpl_line, $delayed_loading) = $plugin_info;
            $plugin = &$this->_plugins[$type][$name];
            
            
            if (isset($plugin)) {
                if (!$plugin[3]) {
                    if (!function_exists($plugin[0])) {
                        $this->_trigger_fatal_error("[plugin] $type '$name' is not implemented", $tpl_file, $tpl_line, __FILE__, __LINE__);
                    } else {
                        $plugin[1] = $tpl_file;
                        $plugin[2] = $tpl_line;
                        $plugin[3] = true;
                    }
                }
                continue;
            } else if ($type == 'insert') {
                
                $plugin_func = 'insert_' . $name;
                if (function_exists($plugin_func)) {
                    $plugin = array($plugin_func, $tpl_file, $tpl_line, true);
                    continue;
                }
            }

            $plugin_file = $this->_get_plugin_filepath($type, $name);

            if (! $found = ($plugin_file != false)) {
                $message = "could not load plugin file '$type.$name.php'\n";
            }

            
            if ($found) {
                include_once $plugin_file;

                $plugin_func = 'smarty_' . $type . '_' . $name;
                if (!function_exists($plugin_func)) {
                    $this->_trigger_fatal_error("[plugin] function $plugin_func() not found in $plugin_file", $tpl_file, $tpl_line, __FILE__, __LINE__);
                    continue;
                }
            }
            
            else if ($type == 'insert' && $delayed_loading) {
                $plugin_func = 'smarty_' . $type . '_' . $name;
                $found = true;
            }

            
            if (!$found) {
                if ($type == 'modifier') {
                    
                    if ($this->security && !in_array($name, $this->security_settings['MODIFIER_FUNCS'])) {
                        $message = "(secure mode) modifier '$name' is not allowed";
                    } else {
                        if (!function_exists($name)) {
                            $message = "modifier '$name' is not implemented";
                        } else {
                            $plugin_func = $name;
                            $found = true;
                        }
                    }
                } else if ($type == 'function') {
                    
                    $message = "unknown tag - '$name'";
                }
            }

            if ($found) {
                $this->_plugins[$type][$name] = array($plugin_func, $tpl_file, $tpl_line, true);
            } else {
                
                $this->_trigger_fatal_error('[plugin] ' . $message, $tpl_file, $tpl_line, __FILE__, __LINE__);
            }
        }
    }

        
    function _load_resource_plugin($type)
    {
        

        $plugin = &$this->_plugins['resource'][$type];
        if (isset($plugin)) {
            if (!$plugin[1] && count($plugin[0])) {
                $plugin[1] = true;
                foreach ($plugin[0] as $plugin_func) {
                    if (!function_exists($plugin_func)) {
                        $plugin[1] = false;
                        break;
                    }
                }
            }

            if (!$plugin[1]) {
                $this->_trigger_fatal_error("[plugin] resource '$type' is not implemented", null, null, __FILE__, __LINE__);
            }

            return;
        }

        $plugin_file = $this->_get_plugin_filepath('resource', $type);
        $found = ($plugin_file != false);

        if ($found) {            
            include_once $plugin_file;

            
            $resource_ops = array('source', 'timestamp', 'secure', 'trusted');
            $resource_funcs = array();
            foreach ($resource_ops as $op) {
                $plugin_func = 'smarty_resource_' . $type . '_' . $op;
                if (!function_exists($plugin_func)) {
                    $this->_trigger_fatal_error("[plugin] function $plugin_func() not found in $plugin_file", null, null, __FILE__, __LINE__);
                    return;
                } else {
                    $resource_funcs[] = $plugin_func;
                }
            }

            $this->_plugins['resource'][$type] = array($resource_funcs, true);
        }
    }

    
    function _autoload_filters()
    {
        foreach ($this->autoload_filters as $filter_type => $filters) {
            foreach ($filters as $filter) {
                $this->load_filter($filter_type, $filter);
            }
        }
    }

    
    function quote_replace($string)
    {
        return preg_replace('![\\$]\d!', '\\\\\\0', $string);
    }


        
    function _trigger_fatal_error($error_msg, $tpl_file = null, $tpl_line = null,
            $file = null, $line = null, $error_type = E_USER_ERROR)
    {
        if(isset($file) && isset($line)) {
            $info = ' ('.basename($file).", line $line)";
        } else {
            $info = null;
        }
        if (isset($tpl_line) && isset($tpl_file)) {
            trigger_error("Smarty error: [in " . $tpl_file . " line " .
                          $tpl_line . "]: $error_msg$info", $error_type);
        } else {
            trigger_error("Smarty error: $error_msg$info", $error_type);
        }
    }

        
    function _get_microtime()
    {
        $mtime = microtime();
        $mtime = explode(" ", $mtime);
        $mtime = (double)($mtime[1]) + (double)($mtime[0]);
        return ($mtime);
    }

        
    function _get_include_path($file_path, &$new_file_path)
    {
        static $_path_array = null;
        
        if(!isset($_path_array)) {
            $_ini_include_path = ini_get('include_path');

            if(strstr($_ini_include_path,';')) {
                
                $_path_array = explode(';',$_ini_include_path);
            } else {
                $_path_array = explode(':',$_ini_include_path);
            }
        }
        foreach ($_path_array as $_include_path) {
            if (file_exists($_include_path . DIR_SEP . $file_path)) {
                   $new_file_path = $_include_path . DIR_SEP . $file_path;
                return true;
            }
        }
        return false;
    }    
    
}



?>
