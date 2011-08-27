<?php if (!defined('__SITE_PATH')) exit('No Direct Access Allowed !!'); ?>
<?php

	class uri
	{
		public $parts = array();
		public $current = '';
		public $query_string = '';
		public $complete_uri = '';
		public $ruri = '';
		public $rparts = array();
		public $controller = '';
		public $controller_path = '';
		public $method = '';
		public $arguments = array();
		public $suffix = '';
		public $last_part = '';
		public $total_parts = '';


		function __construct()
		{
			$this->parts = explode('/', $_SERVER['QUERY_STRING']);
			$this->parts[0] = str_replace('route=', '', $this->parts[0]);

			$this->current = $this->current();
			$this->query_string = $this->query_string();
			$this->complete_uri = $this->complete_uri();
			$this->ruri = $this->ruri();
			$this->rparts = $this->rparts();
			$this->controller = $this->controller();
			$this->controller_path = $this->controller_path();
			$this->method = $this->method();
			$this->arguments = $this->arguments();
			$this->suffix = $this->suffix();
			$this->last_part = $this->last_part();
			$this->total_parts = $this->total_parts();
		}

		function build_uri(array $array)
		{
			if (!is_array($array))
			{
				$array = array($array);
			}
			
			return implode('/', $array);
		}

		function base($protocol = '')
		{
			if (strlen($protocol))
			{
				if (__DIRNAME === '/')
				{
					return $protocol . '://' . $_SERVER['HTTP_HOST'] . __DIRNAME;
				}
				else
				{
					return $protocol . '://' . $_SERVER['HTTP_HOST'] . __DIRNAME . '/';
				}
			}
			else
			{
				// find out the protocol
				if (__DIRNAME === '/')
				{
					return ((empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] === 'off') ? 'http' : 'https') . '://' . $_SERVER['HTTP_HOST'] . __DIRNAME;
				}
				else
				{
					return ((empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] === 'off') ? 'http' : 'https') . '://' . $_SERVER['HTTP_HOST'] . __DIRNAME . '/';
				}
				
			}
		}

		function site($uri = '', $protocol = '')
		{
			if (!strlen($protocol))
			{
				$protocol = (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] === 'off') ? 'http' : 'https';
			}

			// strip leading slash from uri
			if (substr($uri, 0, 1) === '/')
			{
				$uri = substr($uri, 1);
			}

			// strip trailing slash from uri
			if (substr($uri, -1, 1) === '/')
			{
				$uri = substr($uri, 0, strlen($uri) - 1);
			}

			if (strlen($uri))
			{
				$uri_part = 'index.php?route=' . $uri . $this->suffix();
				return $this->base($protocol) . $uri_part;
			}
			else
			{
				return $this->base($protocol) . $uri . 'index.php';
			}
		}

		function file($filename, $uri = '')
		{
			$protocol = (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] === 'off') ? 'http' : 'https';

			// strip leading slash from uri
			if (substr($uri, 0, 1) === '/')
			{
				$uri = substr($uri, 1);
			}

			// add trailing slash to uri
			if (substr($uri, -1, 1) !== '/')
			{
				$uri .= '/';
			}
			
			if (strlen($uri) > 1)
			{
				return $this->base($protocol) . $uri . $filename;
			}
			else
			{

				// strip leading slash from uri
				if (substr($filename, 0, 1) === '/')
				{
					$filename = substr($filename, 1);
				}

				// strip trailing slash from uri
				if (substr($filename, -1, 1) === '/')
				{
					$filename = substr($filename, 0, strlen($filename) - 1);
				}
			
				return $this->base($protocol) . $filename;
			}
		}

		function title($title, $separator = '-')
		{
			$separator = ($separator === '-') ? '-' : '_';
	
			$regex = array(
							'&\#\d+?;'				=> '',
							'&\S+?;'				=> '',
							'\s+'					=> $separator,
							'[^a-z0-9\-\._]'		=> '',
							$separator.'+'			=> $separator,
							$separator.'$'			=> $separator,
							'^'.$separator			=> $separator,
							'\.+$'					=> ''
						  );
	
			$title = strip_tags($title);
	
			foreach ($regex as $key => $val)
			{
				$title = preg_replace("#" . $key . "#i", $val, $title);
			}

			$title = preg_replace('/[^'.$separator.'a-z0-9\s]+/', '', strtolower($title));
			$title = preg_replace('/['.$separator.'\s]+/', $separator, $title);
	
			return trim(stripslashes($title), $separator);
		}

		function current()
		{
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
		
			return $route_path;
		}

		function query_string()
		{
			return $_SERVER['QUERY_STRING'];
		}

		function complete_uri()
		{
			return 'index.php?' . $this->query_string();
		}

		function ruri()
		{
			return $this->ruri;
		}

		function parts()
		{
			return $this->parts;
		}
		
		function rparts()
		{
			return explode('/', $this->ruri());
		}

		function last_part()
		{
			return end($this->parts);
		}
		
		function total_parts()
		{
			return count($this->parts);
		}

		function controller()
		{
			return $this->controller;
		}

		function controller_path()
		{
			return $this->controller_path;
		}

		function method()
		{
			return $this->method;
		}

		function arguments()
		{
			return $this->arguments;
		}

		function suffix()
		{
			return __SUFFIX;
		}

		function part($key, $default_value = false)
		{
			if (count($this->rparts) && !empty($this->rparts[0]))
			{
				return (array_key_exists($key, $this->rparts)) ? $this->rparts[$key] : $default_value;
			}
			else
			{
				return (array_key_exists($key, $this->parts)) ? $this->parts[$key] : $default_value;
			}
		}

		function redirect($route, $qs = '', $method = 'LOCATION')
		{
			if (!isset($qs))
			{
				switch($method)
				{
					case 'refresh' :
						header("Refresh:0; url=" . 'index.php?route=' . html_entity_decode($route));
						break;
					default :
						header($method . ': index.php?route=' . html_entity_decode($route));
						break;
				}
			}
			else
			{
				switch($method)
				{
					case 'refresh' :
						header("Refresh:0; url=" . 'index.php?route=' . html_entity_decode($route) . $qs);
						break;
					default :
						header($method . ': index.php?route=' . html_entity_decode($route) . $qs);
						break;
				}
			}
			exit;
		}

		function http($route)
		{
			return 'http://' . $_SERVER['HTTP_HOST'] . '/' . __DIRNAME . '/index.php?route=' . $route;
		}
	
		function https($route)
		{
			return 'https://' . $_SERVER['HTTP_HOST'] . '/' . __DIRNAME . '/index.php?route=' . $route;
		}
		
		function jredirect($location)
		{
			echo '<script type="text/javascript">';
			echo 'window.location = ' . $location . ';';
			echo '</script>';
		}
		
		
		function hyperlink($uri = '', $title = '', $attributes = '')
		{
			$title = (string) $title;
	
			if (! is_array($uri))
			{
				$site_url = (! preg_match('!^\w+://! i', $uri)) ? $this->site($uri) : $uri;
			}
			else
			{
				$site_url = $this->site($uri);
			}
	
			if ($title == '')
			{
				$title = $site_url;
			}
	
			if ($attributes != '')
			{
				$attributes = $this->parse_attribs($attributes);
			}
	
			return '<a href="' . $site_url . '"' . $attributes . '>' . $title . '</a>';
		}

		function popup($uri = '', $title = '', $attributes = FALSE)
		{
			$title = (string) $title;
	
			$site_url = (! preg_match('!^\w+://! i', $uri)) ? $this->site($uri) : $uri;
	
			if ($title == '')
			{
				$title = $site_url;
			}
	
			if ($attributes == false)
			{
				return "<a href='javascript:void(0);' onclick=\"window.open('" . $site_url . "', '_blank');\">" . $title . "</a>";
			}
	
			if (! is_array($attributes))
			{
				$attributes = array();
			}
	
			foreach (array('width' => '800', 'height' => '600', 'scrollbars' => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0', ) as $key => $val)
			{
				$atts[$key] = (! isset($attributes[$key])) ? $val : $attributes[$key];
				unset($attributes[$key]);
			}
	
			if ($attributes != '')
			{
				$attributes = $this->parse_attribs($attributes);
			}
	
			return "<a href='javascript:void(0);' onclick=\"window.open('" . $site_url . "', '_blank', '" . $this->parse_attribs($atts, true) . "');\"$attributes>" . $title . "</a>";
		}

		function link_mail($email, $title = '', $attributes = '')
		{
			$title = (string) $title;
	
			if ($title == "")
			{
				$title = $email;
			}
	
			$attributes = $this->parse_attribs($attributes);
	
			return '<a href="mailto:' . $email . '"' . $attributes . '>' . $title . '</a>';
		}

		function protocol($str = '')
		{
			if ($str == 'http://' or $str == '')
			{
				return '';
			}
	
			if (substr($str, 0, 7) != 'http://' && substr($str, 0, 8) != 'https://')
			{
				$str = 'http://' . $str;
			}
	
			return $str;
		}

		private function parse_attribs($attributes, $javascript = false)
		{
			if (is_string($attributes))
			{
				return ($attributes != '') ? ' ' . $attributes : '';
			}
	
			$att = '';
			
			foreach ($attributes as $key => $val)
			{
				if ($javascript == true)
				{
					$att .= $key . '=' . $val . ',';
				}
				else
				{
					$att .= ' ' . $key . '="' . $val . '"';
				}
			}
	
			if ($javascript == true and $att != '')
			{
				$att = substr($att, 0, -1);
			}
	
			return $att;
		}

	}
?>