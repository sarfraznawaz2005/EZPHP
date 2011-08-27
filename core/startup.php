<?php
	
	/*
	 * ------------------------------------------------------
	 *  The Startup File
	 * ------------------------------------------------------
	 *
	 *	This file contains the needed initialization code for
	 *	the framework to work.
	 *
	 */


	# Check PHP Version
	if (version_compare(phpversion(), '5.2', '<') == true)
	{
		exit('PHP >= 5.2 Required!');
	}



	# directory separator
	define ('DS', DIRECTORY_SEPARATOR);

	# sets folder name application resides in.
	$_SERVER['PHP_SELF'] = filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING);
	
	define ('__DIRNAME', dirname($_SERVER['PHP_SELF']));

	# sets site path for inclusion of files
	define ('__SITE_PATH', $_SERVER['DOCUMENT_ROOT'] . __DIRNAME);

	# sets absolute site path
	define ('__SITE_PATH_ABS', realpath(__SITE_PATH));

	// save paths for global access
	ezphp::ez_set('ez_dir', __DIRNAME);
	ezphp::ez_set('ez_site_path', __SITE_PATH);
	ezphp::ez_set('ez_site_path_abs', __SITE_PATH_ABS);

	# get the site settings
	$ezphp_config = setting::getInstance();

	// set time zone
	if (function_exists('date_default_timezone_set'))
	{
		# default time zone
		$timezone = $ezphp_config->get['application']['timezone'];
		
		if (!$timezone)
		{
			$timezone = 'Europe/London';
		}
		
		date_default_timezone_set(empty($timezone) ? date_default_timezone_get() : $timezone);
	}
	
	
	$possible_values = array('0', '1');
	
	# security settings ##################################
	$error_reporting = $ezphp_config->get['security']['error_reporting'];
	error_reporting((strlen(trim($error_reporting))) ? $error_reporting : E_ALL);

	$display_errors = $ezphp_config->get['security']['display_errors'];
	$xss_filter = $ezphp_config->get['security']['xss_filter'];
	
	if (!in_array($display_errors, $possible_values))
	{
		exit('[security]<br /> display_errors should have a value 0 or 1');
	}

	if (!in_array($xss_filter, $possible_values))
	{
		exit('[security]<br /> xss_filter should have a value 0 or 1');
	}

	ini_set('display_errors', ($display_errors == '1') ? true : false);
	######################################################	
	

	# get url suffix if any
	$url_suffix = $ezphp_config->get['url']['url_suffix'];
	define('__SUFFIX', strlen(trim($url_suffix)) ? $url_suffix : '');


	# get cache info
	$enable_cache = $ezphp_config->get['cache']['enable_cache'];
	
	if (!in_array($enable_cache, $possible_values))
	{
		exit('[cache]<br /> enable_cache should have a value 0 or 1');
	}

	define ('__ENABLE_CACHE', $ezphp_config->get['cache']['enable_cache']);	
	define ('__CACHE_TIME', $ezphp_config->get['cache']['cache_lifetime']);


	// do we have to use the template in the first place ?
	$use_template = $ezphp_config->get['template']['use_template'];

	if (!in_array($use_template, $possible_values))
	{
		exit('[template]<br /> use_template should have a value 0 or 1');
	}

	ezphp::ez_set('use_template', $use_template);

	# get template name and path
	$template_dir = $ezphp_config->get['template']['template_dir'];

	if (ezphp::ez_get('use_template') == '1' && !strlen(trim($template_dir)))
	{
		exit('template directory needs to be specified in the config file !!');
	}
	
	define ('__TEMPLATE_PATH',  './template/' . $template_dir . '/');
	define ('__FULL_PATH', __SITE_PATH . '/template/' . $template_dir . '/');
	
	// set the template options
	ezphp::set_template($template_dir);
	ezphp::ez_set('ez_templath_path', __TEMPLATE_PATH);
	ezphp::ez_set('ez_full_path', __FULL_PATH);
	ezphp::ez_set('ez_template_name', $template_dir);
	ezphp::ez_set('ez_content_path', __SITE_PATH . '/content/');
	ezphp::ez_set('ez_base_path', __SITE_PATH . '/');
	

	# get database info
	$__use_db = $ezphp_config->get['database']['use_db'];

	if (!in_array($__use_db, $possible_values))
	{
		exit('[security]<br /> use_db should have a value 0 or 1');
	}
	
	if ($__use_db == '1')
	{
		$__host = $ezphp_config->get['database']['db_hostname'];
		$__user = $ezphp_config->get['database']['db_username'];
		$__password = $ezphp_config->get['database']['db_password'];
		$__database = $ezphp_config->get['database']['db_name'];
		$__port = $ezphp_config->get['database']['db_port'];
		$db_class = $ezphp_config->get['database']['db_class'];
		
		// is port specified?
		$__host = (strlen($__port) > 0) ? $__host . ':' . $__port : $__host;
		

		if (is_readable(__SITE_PATH . '/classes/' .  $db_class . '.php'))
		{
			include_once (__SITE_PATH . '/classes/' .  $db_class . '.php');
	
			# create the database access object
			ezphp::ez_set('db', new $db_class($__host, $__user, $__password, $__database));
		}
		else
		{
			exit('Database class: <strong>' . $db_class . '.php</strong>' . ' could not be found!');
		}
	}



	// include core files not looked for by __autoload
	// include (__SITE_PATH . '/core/is_php6.php');

	
	// include core files when requested
	function __autoload($class_name)
	{
		$__file = __SITE_PATH . '/core/' . strtolower($class_name) . '.php';

		if (file_exists($__file) === false)
		{
			return false;
		}
		
		include ($__file);
	}

	// load up autoload classes if any
	$autoloads = $ezphp_config->get['autoload'];
	
	if (isset($autoloads) && count($autoloads))
	{
		include_autoloads($autoloads);
	}
	
	function include_autoloads($autoloads)
	{
		foreach($autoloads as $autoload)
		{
			if (is_array($autoload))
			{
				$class_name = $autoload[0];
				$args = $autoload;
				array_shift($args); // shift the first value eg class name
				$args = $args[0];
			}
			else
			{
				$class_name = $autoload;
			}


			$file = __SITE_PATH . '/classes/' . $class_name . '.php';

			if (file_exists($file) === true)
			{
				include $file;
				
				if (count($args) === 0)
				{
					ezphp::ez_set($class_name, new $class_name());
				}
				elseif (count($args) === 1)
				{
					if (is_array($args))
					{
						ezphp::ez_set($class_name, new $class_name($args[0]));
					}
					else
					{
						ezphp::ez_set($class_name, new $class_name($args));
					}
				}
				elseif (count($args) > 1)
				{
					if (method_exists($class_name,  '__construct') === false)
					{
						exit("Constructor for the class <strong>$class_name</strong> does not exist, you should not pass arguments to this class or provide a constructor for it!");
					}

					$refMethod = new ReflectionMethod($class_name,  '__construct');
					$params = $refMethod->getParameters();

					$re_args = array();

					foreach($params as $key => $param)
					{
						if ($param->isPassedByReference())
						{
							$re_args[$key] = &$args[$key];
						}
						else
						{
							$re_args[$key] = $args[$key];
						}
					}

					$refClass = new ReflectionClass($class_name);
					ezphp::ez_set($class_name, $refClass->newInstanceArgs((array) $re_args));
				}
				
			}
		}
	}
	# -----------------------------------------------------------


	# load up the needed core classes ---------------------------
	ezphp::ez_set('input', new input);
	ezphp::ez_set('uri', new uri);
	ezphp::ez_set('cache', new cache);
	ezphp::ez_set('session', new session);
	# -----------------------------------------------------------



// end of startup file...