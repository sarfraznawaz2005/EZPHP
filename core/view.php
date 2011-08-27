<?php if (!defined('__SITE_PATH')) exit('No Direct Access Allowed !!'); ?>
<?php

	class view extends ezphp
	{
		private $vars = array();
		private $no_template = false;
		private $view_path = '';


		function __construct()
		{
			parent::__construct();
			
			$this->load_globals();

			// set path vars
			$this->vars['ez_template_path'] = ezphp::ez_get('ez_templath_path');
			$this->vars['ez_full_path'] = ezphp::ez_get('ez_full_path');
			$this->vars['ez_content_path'] = ezphp::ez_get('ez_content_path');
			$this->vars['ez_base_path'] = ezphp::ez_get('ez_base_path');
			$this->vars['ez_template_name'] = ezphp::ez_get('ez_template_name');
			
		}
		
		function __set($index, $value)
		{
			$this->vars[$index] = $value;
		}

		function __get($index)
		{
			return (isset($this->vars[$index])) ? $this->vars[$index] : ezphp::ez_get($index);
		}
		
		function load_template($template_name)
		{
			if (empty($template_name))
			{
				return false;
			}
			
			ezphp::set_template($template_name);
			ezphp::ez_set('ez_templath_path', './template/' . $template_name . '/');
			ezphp::ez_set('ez_full_path', __SITE_PATH . '/template/' . $template_name . '/');

			// set path vars
			$this->vars['ez_template_path'] = ezphp::ez_get('ez_templath_path');
			$this->vars['ez_full_path'] = ezphp::ez_get('ez_full_path');
		}


		function load_globals()
		{
			if (is_readable(__SITE_PATH . '/config/globals.php'))
			{
				include_once __SITE_PATH . '/config/globals.php';
				
				// get vars
				$vars = get_defined_vars();
				// get constants
				$consts = get_defined_constants();
				// get functions
				$funcs = get_defined_functions();
				
				// we dont need to add internal php functions to be stored for view files.
				unset($funcs['internal']);

				
				// allow only constants starting with an underscore char eg _
				// this helps avoid adding hundreds of php constants to the view files and gaining some speed
				// and load the pages a bit faster.
				foreach ($consts as $key => $value)
				{
					$match = preg_match('/^[_]+/', $key);
					
					// no match ?
					if ($match === 0)
					{
						unset ($consts[$key]);
					}
				}

				// load vars
				if (count($vars))
				{
					foreach ($vars as $index => $value)
					{
						// for V
						$this->vars[$index] = $value;
						// for C, M
						$this->ez_set($index, $value);
					}
				}
				
				// load functions
				if (count($funcs))
				{
					foreach ($funcs as $index => $value)
					{
						// for V
						$this->vars[$index] = $value;
						// for C, M
						$this->ez_set($index, $value);
					}
				}

				// load constants
				if (count($consts))
				{
					foreach ($consts as $index => $value)
					{
						// for V
						$this->vars[$index] = $value;
						// for C, M
						$this->ez_set($index, $value);
					}
				}
				
			}

		}

		function print_data()
		{
			print '<pre>';
			var_export($this->vars);
			exit;
		}

		function has($index)
		{
			return isset($this->vars[$index]);
		}
		
		function remove($index)
		{
			unset ($this->vars[$index]);
			return true;
		}

		function no_template()
		{
			$this->no_template = true;
		}

		function view_path()
		{
			if (empty($this->view_path))
			{
				return false;
			}
			
			return $this->view_path;
		}
		
		
		function render($view_name)
		{
			$path = __SITE_PATH . '/content/views/' . $view_name . '.php';
		
			if (file_exists($path) === false || is_readable($path) === false)
			{
				throw new Exception('View Not Found In '. $path);
				return false;
			}

			// set view path
			ezphp::ez_set('ez_view_path', dirname($path) . '/');
			$this->view_path = dirname($path) . '/';
			$this->vars['ez_view_path'] = dirname($path) . '/';
			
			# load variables to views from controller
			extract ($this->vars);

			
			// for cache
			$qstring = $_SERVER['QUERY_STRING'];
			$qstring = preg_replace("/[^a-zA-Z0-9_]+/", '_', $qstring);

			# cache the current file
			//if (__ENABLE_CACHE == '1' && !$this->cache->isCached($view_name . $qstring))
			if (__ENABLE_CACHE == '1')
			{
				$cache_data = $this->getContent($path);
				$this->cache->set($view_name . $qstring, $cache_data);
				$file = glob(__SITE_PATH . '/cache/' . 'cache.' . $view_name . $qstring . '.*');
			}
			

			if (($this->no_template == true) || (ezphp::ez_get('use_template') == '0'))
			{
				$ez_layout_content = '';
				
				if (__ENABLE_CACHE == '1')
				{
					if ($file[0])
					{
						$handle = fopen($file[0], 'r') or die('Could not open the file: ' . $file[0]);
						$cache  = fread($handle, filesize($file[0]));		  
						fclose($handle);
	
						print unserialize($cache);
					}
				}
				 else
				{
					include $path;
				}
				
				if (!file_exists($file[0]) || !is_readable($file[0]) || empty($cache))
				{
					include $path;
				}
				
			}
			else
			{
				if (__ENABLE_CACHE == '1')
				{
					$ez_layout_content = $this->cache->get($view_name . $qstring);
				}
				 else
				{
					$ez_layout_content = $this->getContent($path);
				}
				
				if (empty($ez_layout_content))
				{
					$ez_layout_content = $this->getContent($path);
				}
	
				// load the template file
				include_once __SITE_PATH . '/template/' . ezphp::get_template() . '/template.php';
			}
		}
		
		private function getContent($view_file)
		{
			# load variables to views from controller
			extract ($this->vars);
		
			ob_start();
			include($view_file);
			$contents = ob_get_contents();
			ob_end_clean();
			
			return ($contents) ? $contents : false;
		}
		
		private function read_file($file_path)
		{
			if (is_readable($file_path))
			{
				return file_get_contents($file_path);
			}
			else
			{
				return false;
			}
		}
	}
?>
