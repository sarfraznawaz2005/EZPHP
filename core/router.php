<?php if (!defined('__SITE_PATH')) exit('No Direct Access Allowed !!'); ?>
<?php

	class router
	{
		 private $__args = array();
		 private $__file = '';
		 private $__controller = '';
		 private $__action = '';
		 private $__class = '';
		 

		 # load the controller
		 public function dispatch()
		 {
			# check the route
			$this->parseURL();

			if (is_readable($this->__file) == false)
			{
				# show the 404 error page
				$this->error404();
			}
		
			# include the controller
			include $this->__file;
			
			$this->__class = $this->__controller . "_controller";
			
			if (class_exists($this->__class))
			{
				if (method_exists($this->__class, 'index'))
				{
					$__controller = new $this->__class();
				}
				else
				{
					exit("Required method <strong>index</strong> not found for the class: <strong>$this->__class</strong>");
				}
			}
			else
			{
				exit ("<strong>Class could not be found: $this->__class</strong>");
				return false;
			}
		
			# check if the action is callable
			if (is_callable(array($__controller, $this->__action)) === false)
			{
				# default action
				$__action = 'index';
			}
			else
			{
				$__action = $this->__action;
			}
			
			#####

			# set the argumentss for later retrieval
			ezphp::ez_set('arguments', $this->__args);
			
			# make an array of arguments
			foreach($this->__args as $value)
			{
				# make an associative array
				ezphp::ez_set($value, $value);
			}


			// set vars for the uri library
			$lib_uri = ezphp::ez_get('uri');
			$lib_uri->arguments = $this->__args;
			$lib_uri->method = $this->__action;
			$lib_uri->controller = $this->__controller;
			$lib_uri->controller_path = $this->__file;
			/////////////////////////////////

			# call the action now
			call_user_func_array(array($__controller, $__action), $this->getArgs());
		 }
		
		private function parseURL()
		{
			$ezphp_config = setting::getInstance();
			
			$path = "";
			$route_path = (empty($_GET['route'])) ? '' : $_GET['route'];
			$route_path = trim($route_path, '/\\');
			$route_path_len = strlen($route_path);
	
			if ($route_path_len > 1 && substr($route_path, -1) == '/')
			{
				$route_path = substr($route_path, 0, -1);
			}
			elseif ($route_path_len == 0)
			{
				$route_path = '/';
			}
			//print $route_path;exit;


			// for routed urls start
			$routes = $ezphp_config->get['routes'];

			if (count($routes) > 0)
			{
				if (!in_array('/', array_keys($routes)))
				{
					$controller = ($ezphp_config->get['application']['default_controller']) ? $ezphp_config->get['application']['default_controller'] : 'home';
					$routes['/'] = $controller . '/index';
				}
				
				foreach ($routes as $route => $uri)
				{
					if (strpos($route, ':') !== false)
					{
						$wildcard = array(':any', ':alphanum', ':num', ':alpha');
						$regex = array('(.+)', '([a-z0-9]+)', '([0-9]+)', '([a-z]+)');
						$route = str_replace($wildcard, $regex, $route);
					}
	
					if (preg_match('#^' . $route . '$#u', $route_path))
					{
						if (strpos($uri, '$') !== false && strpos($route, '(') !== false)
						{
							// for regex routing
							$route_path = preg_replace('#^' . $route . '$#', $uri, $route_path);
						}
						else
						{
							// for normal routing
							$route_path = $uri;
						}
		
						// we found a valid route
						$lib_uri = ezphp::ez_get('uri');
						$lib_uri->ruri = $route_path;
						$lib_uri->rparts = explode('/', $route_path);
						
						break;
					}
				}
			}
			// for routed urls end


			// filter bad/malacious urls
			// (not sure whether we really need this...)
			// $route_path = $this->filter_url($route_path);


			$parts = explode('/', str_replace('../', '', $route_path));
			$path = __SITE_PATH . '/content/controllers/';

			// Find right controller including sub-dirs
			foreach ($parts as $part)
			{
				$fullpath = $path . $part;
				
				// do we have dir?
				if (is_dir($fullpath))
				{
					$path .= $part . '/';
					array_shift($parts);
					continue;
				}

				// find the file
				if (is_file($fullpath . '.php'))
				{
					$this->__controller = $part;
					array_shift($parts);
					break;
				}
			}
			

			if (empty($this->__controller))
			{
				if (@$parts[0])
				{
					$this->__controller = $parts[0];
				}
			}

			if (empty($this->__controller))
			{
				# default controller
				$def_controller = $ezphp_config->get['application']['default_controller'];
				$this->__controller = ($def_controller) ? $def_controller : 'home';
			}

			$method = '';

			if (!empty($parts))
			{
				$method = array_shift($parts);
			}
			
			$this->__action = (!empty($method)) ? $method : 'index';
			$this->__args = $parts;

			
			# do we have the same suffix in url and config file?
			if (count($this->__args))
			{
				if ($this->match_suffix(end($this->__args)) === false)
				{
					# show the 404 error page
					$this->error404();
					return;
				}
			}

			if ($this->match_suffix($this->__action) === false || $this->match_suffix($this->__controller) === false)
			{
				# show the 404 error page
				$this->error404();
				return;
			}
			#########################
			
			# strip url suffix if any
			if (count($this->__args))
			{
				foreach($this->__args as $key => $value)
				{
					$this->__args[$key] = $this->clean_suffix($value);
				}
			}
			
			$this->__action = $this->clean_suffix($this->__action);
			$this->__controller = $this->clean_suffix($this->__controller);
			#################			

			# is this private action/function?
			$private = substr($this->__action, 0, 8);
			
			if (strtolower($private) === "private_")
			{
				# show the 404 error page
				$this->error404();
			}
			else
			{
				$this->__file = $path . $this->__controller . '.php';
				$this->__file = str_replace('../', '', $this->__file);
			}
		}

		# strips suffix from path
		private function clean_suffix($path)
		{
			if (!__SUFFIX)
			{
				return $path;
			}
			
			if (strpos($path, '.') !== false)
			{
				return substr($path, 0, (strlen($path) - strlen(__SUFFIX)));
			}
			else
			{
				return $path;
			}
		}

		private function match_suffix($path)
		{
			if (!__SUFFIX)
			{
				return true;
			}

			if (strpos($path, '.') !== false)
			{
				$extension = $this->get_extension($path);
				
				if (strtolower(__SUFFIX) == strtolower($extension))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return true;
			}
		}

		// filters possible malacious stuff from URLs
		private function filter_url($url)
		{
			// Allow only one ? in URLs
			$total_question_marks = substr_count($url, '?');

			if ($total_question_marks >= 2)
			{
				exit('You can not use 2 question marks (?) in URLs for security reasons!!');
			}
			
			// decode URLs
			$url = rawurldecode($url);
			$url = urldecode($url);
			// remove bad stuff
			$url = str_replace('../', '', $url);
			$url = str_replace('..\\', '', $url);
			$url = str_replace('..%5C', '', $url);
			$url = str_replace('%00', '', $url);
			$url = str_ireplace('http', '', $url);
			$url = str_ireplace('https', '', $url);
			$url = str_ireplace('ftp', '', $url);
			$url = str_ireplace('smb', '', $url);
			$url = str_replace('://', '', $url);
			$url = str_replace(':\\\\', '', $url);
			$url = str_replace(array('<', '>'), array('&lt;', '&gt;'), $url);
			
			// Allow only a-zA-Z0-9_/.-?=&~%:
			$url = preg_replace("/[^a-zA-Z0-9_\-\/\.\?=\\&~%:]+/", "", $url);
			
			//print $url;
			return $url;
		}

		function get_extension($path)
		{
			$extension_parts = explode('.', $path);
			return '.' . end($extension_parts);
		}	

		function getController()
		{
			return $this->__controller . '_controller';
		}
		
		function getMethod()
		{
			return $this->__action;
		}
		
		function getArgs()
		{
			return $this->__args;
		}

		function controllerPath()
		{
			return $this->__file;
		}
		

		function error404()
		{
			ob_start();
			@header("HTTP/1.1 404 Not Found");
			require $path = __SITE_PATH . '/error404/404.php';
			exit();
		}
		
	}

?>
