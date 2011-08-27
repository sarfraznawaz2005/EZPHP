<?php if (!defined('__SITE_PATH')) exit('No Direct Access Allowed !!'); ?>
<?php

	class input
	{
		private $xsshash = '';

		function __construct()
		{
			if (get_magic_quotes_runtime())
			{
				// disable magic_quotes_runtime; it is deprecated
				@set_magic_quotes_runtime(0);
			}


			// register_globals is enabled?
			if (ini_get('register_globals'))
			{
				if (isset($_REQUEST['GLOBALS']))
				{
					// Prevent GLOBALS override attacks
					exit('Possible GLOBALS override attack!!');
				}

				// empty the REQUEST
				$_REQUEST = array();

				// These globals are standard and should therefore be preserved
				$preserve = array('GLOBALS', '_REQUEST', '_GET', '_POST', '_FILES', '_COOKIE', '_SERVER', '_ENV', '_SESSION');

				// Mimics as register_globals = off
				foreach (array_diff(array_keys($GLOBALS), $preserve) as $key)
				{
					global $$key;
					$$key = NULL;

					// remove the GLOBALS variables
					unset($GLOBALS[$key], $$key);
				}
			}


			if (is_array($_GET))
			{
				foreach ($_GET as $key => $val)
				{
					// sanitize $_GET
					$_GET[$this->clean_input_keys($key)] = $this->clean_input_data($val);
				}
			}
			else
			{
				$_GET = array();
			}

			if (is_array($_POST))
			{
				foreach ($_POST as $key => $val)
				{
					// sanitize $_POST
					$_POST[$this->clean_input_keys($key)] = $this->clean_input_data($val);
				}
			}
			else
			{
				$_POST = array();
			}

			if (is_array($_COOKIE))
			{
				foreach ($_COOKIE as $key => $val)
				{
					// Ignore special attributes in RFC2109 compliant cookies
					if ($key == '$Version' || $key == '$Path' || $key == '$Domain')
					{
						continue;
					}

					// sanitize $_COOKIE
					$_COOKIE[$this->clean_input_keys($key)] = $this->clean_input_data($val);
				}
			}
			else
			{
				$_COOKIE = array();
			}


			$ezphp_config = setting::getInstance();
			$xss_filter = $ezphp_config->get['security']['xss_filter'];

			$_GET = $xss_filter == '1' ? $this->clean_xss($_GET) : $_GET;
			$_POST = $xss_filter == '1' ? $this->clean_xss($_POST) : $_POST;
			$_COOKIE = $xss_filter == '1' ? $this->clean_xss($_COOKIE) : $_COOKIE;
			$_REQUEST = $xss_filter == '1' ? $this->clean_xss($_REQUEST) : $_REQUEST;
			$_FILES = $xss_filter == '1' ? $this->clean_xss($_FILES) : $_FILES;
			$_SERVER = $xss_filter == '1' ? $this->clean_xss($_SERVER) : $_SERVER;

		}

		function get($index, $clean_xss = false)
		{
			return $this->get_input($_GET, $index, $clean_xss);
		}

		function post($index, $clean_xss = false)
		{
			return $this->get_input($_POST, $index, $clean_xss);
		}

		/*

			$_REQUEST array is disabled for security reasons.

		function request($index, $clean_xss = false)
		{
			return $this->get_input($_REQUEST, $index, $clean_xss);
		}
		*/

		function cookie($index, $clean_xss = false)
		{
			return $this->get_input($_COOKIE, $index, $clean_xss);
		}

		function server($index, $clean_xss = false)
		{
			return $this->get_input($_SERVER, $index, $clean_xss);
		}

		private function get_input(&$array, $index, $clean_xss = false)
		{
			if (!isset($array[$index]))
			{
				return false;
			}

			if ($clean_xss === true)
			{
				return $this->clean_xss($array[$index]);
			}
			else
			{
				return $array[$index];
			}
		}


		// checks whether email supplied is valid one

		// Example use:
		/*
			if (!is_valid_email($email))
			{
			  echo 'Sorry, invalid email';
			  exit;
			}
		*/

		function is_valid_email($email)
		{
		  return preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s]+\.+[a-z]{2,6}))$#si', $email);
		}


		# strip injection chars from email headers
		function clean_email($string)
		{
			return preg_replace('((?:\n|\r|\t|%0A|%0D|%08|%09)+)i', '', $string);
		}


		/*
		* This function should only be used to deal with data
		* which is submitted one.
		*/

		# useful in preventing XSS attacks
		function clean_xss($data, $is_image = false)
		{
			if (is_array($data))
			{
				foreach ($data as $key => $value)
				{
					$data[$key] =& $this->clean_xss($value, $is_image);
				}
			}
			else
			{

				if (trim($data) === '')
				{
					return $data;
				}

				if ($is_image == true)
				{
					// short tags not processed for images for they contain it usually
					$data = str_replace(array('<?php', '<?PHP'), array('&lt;?php', '&lt;?PHP'), $data);
					return $data;
				}
				else
				{
					$data = str_replace(array('<?php', '<?PHP', '<?', '?'.'>'), array('&lt;?php', '&lt;?PHP', '&lt;?', '?&gt;'), $data);
				}

				// Prevent potential Unicode codec problems
				$data = utf8_decode($data);

				// HTMLize HTML-specific characters.
				$data = htmlentities($data, ENT_QUOTES, 'UTF-8');

				$data = $this->remove_non_displayables($data);

				$data = preg_replace('#(alert|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si', "\\1\\2&#40;\\3&#41;", $data);

				/*
				* Protect GET variables inside URLs
				Example:
				// 901119URL5918AMP18930PROTECT8198
				*/
				$data = preg_replace('|\&([a-z\_0-9]+)\=([a-z\_0-9]+)|i', $this->xss_hash()."\\1=\\2", $data);

				/*
				* Validate UTF16 two byte encoding (x00)
				* adding a semicolon if missing.
				*/
				$data = preg_replace('#(&\#x?)([0-9A-F]+);?#i',"\\1\\2;", $data);

				/*
				* URL Decode
				* Example:
				* <a href="http://%77%77%77%2E%67%6F%6F%67%6C%65%2E%63%6F%6D">Google</a>
				*/
				$data = rawurldecode($data);

				/*
				   Convert tabs to spaces to disallow something like this:
				   ja	vascript
				*/
				if (strpos($data, "\t") !== false)
				{
					$data = str_replace("\t", ' ', $data);
				}

				// Remove any attribute starting with "on" or xmlns
				$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

				// Remove javascript: and vbscript: protocols
				$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
				$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
				$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

				// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
				$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
				$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
				$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

				// Remove namespaced elements (we do not need them)
				$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

				do
				{
					$old_data = $data;
					// these are not allowed under any case !!
					$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
				}
				while ($old_data !== $data);
			}

			return $data;
		}

		function get_ip()
		{
			$ip = false;

			if (!empty($_SERVER['HTTP_CLIENT_IP']))
			{
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			}

			if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			{
				 $ips = explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']);

				 if ($ip != false)
				 {
					  array_unshift($ips,$ip);
					  $ip = false;
				 }

				 $count = count($ips);

				 # exclude IP addresses reserved for LANs
				 for ($i = 0; $i < $count; $i++)
				 {
					  if (!preg_match("/^(10|172\.16|192\.168)\./i", $ips[$i]))
					  {
						  $ip = $ips[$i];
						  break;
					  }
				 }
			}

			if (false == $ip AND isset($_SERVER['REMOTE_ADDR']))
			{
				$ip = $_SERVER['REMOTE_ADDR'];
			}

			return $ip;
		}

		function strip_img_tags($str)
		{
			return preg_replace('#<img\s.*?(?:src\s*=\s*["\']?([^"\'<>\s]*)["\']?[^>]*)?>#is', '$1', $str);
		}

		function encode_php_tags($str)
		{
			return str_replace(array('<?', '?>'), array('&lt;?', '?&gt;'), $str);
		}

		function secure_filename($str)
		{
			$bad = array(
							"../",
							"./",
							"<!--",
							"-->",
							"<",
							">",
							"'",
							'"',
							'&',
							'$',
							'#',
							'{',
							'}',
							'[',
							']',
							'=',
							';',
							'?',
							"%20",
							"%22",
							"%3c",		// <
							"%253c", 	// <
							"%3e", 		// >
							"%0e", 		// >
							"%28", 		// (
							"%29", 		// )
							"%2528", 	// (
							"%26", 		// &
							"%24", 		// $
							"%3f", 		// ?
							"%3b", 		// ;
							"%3d"		// =
						);

			return stripslashes(str_replace($bad, '', $str));
		}

		function remove_non_displayables($string)
		{
			static $non_displayables;

			if (! isset($non_displayables))
			{
				$non_displayables = array(
											'/%0[0-8bcef]/',			// url encoded 00-08, 11, 12, 14, 15
											'/%1[0-9a-f]/',				// url encoded 16-31
											'/[\x00-\x08]/',			// 00-08
											'/\x0b/', '/\x0c/',			// 11, 12
											'/[\x0e-\x1f]/'				// 14-31
										);
			}

			do
			{
				$cleaned = $string;
				$string = preg_replace($non_displayables, '', $string);
			}
			while ($cleaned != $string);

			return $string;
		}

		// Converts plaintext to HTML-compliant stuff
		// replacement of nl2br
		function autop($pee, $br = 1) {
			$pee = $pee . "\n"; // Just to make things a little easier, pad the end.

			$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
			$pee = preg_replace('!(<(?:table|ul|ol|li|pre|form|blockquote|h[1-6])[^>]*>)!', "\n$1", $pee); // Space things out a little.
			$pee = preg_replace('!(</(?:table|ul|ol|li|pre|form|blockquote|h[1-6])>)!', "$1\n", $pee); // Space things out a little.
			$pee = preg_replace("/(\r\n|\r)/", "\n", $pee); // Cross-platform newlines.
			$pee = preg_replace("/\n\n+/", "\n\n", $pee); // Take care of duplicates.
			$pee = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "\t<p>$1</p>\n", $pee);
			// Make paragraphs, including one at the end.
			$pee = preg_replace('|<p>\s*?</p>|', '', $pee); // Under certain strange conditions, it could create a P of entirely whitespace.
			$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // Problem with nested lists
			$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
			$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
			$pee = preg_replace('!<p>\s*(</?(?:table|tr|td|th|div|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)!', "$1", $pee);
			$pee = preg_replace('!(</?(?:table|tr|td|th|div|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)\s*</p>!', "$1", $pee);

			if ($br)
			{
				$pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // Optionally make line breaks.
			}

			$pee = preg_replace('!(</?(?:table|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)\s*<br />!', "$1", $pee);
			$pee = preg_replace('!<br />(\s*</?(?:p|li|div|th|pre|td|ul|ol)>)!', '$1', $pee);
			$pee = preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $pee);

			return $pee;
		}

		function xss_hash()
		{
			if ($this->xsshash == '')
			{
				if (phpversion() >= 4.2)
					mt_srand();
				else
					mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

				$this->xsshash = md5(time() + mt_rand(0, 1999999999));
			}

			return $this->xsshash;
		}


		function is_ascii($str)
		{
			return !preg_match('/[^\x00-\x7F]/S', $str);
		}


		function stamp()
		{
			return date("m-d-Y H:i:s");
		}


		// Gets today's date
		public function get_date($format = null)
		{
			# Params:
			#		$format = date format like m-d-Y

			if ($format)
			{
				$today = date($format);
			}
			else
			{
				$today = date("m-d-Y");
			}

			return $today;
		}

		// Gets currents time
		public function get_time($format = null)
		{
			# Params:
			#		$format = date format like H:i:s

			if ($format)
			{
				$time = date($format);
			}
			else
			{
				$time = date("H:i:s");
			}

			return $time;
		}

		// This function can be used to discard any characters that can be used to manipulate the SQL queries or SQL injection

		/* EXAMPLE USE:

			if (is_valid($_REQUEST["username"]) === true && is_valid($_REQUEST["pass"]) === true)
			{
				//login now
			}
		*/

		public function is_valid($input)
		{
			$input = strtolower($input);

			if (str_word_count($input) > 1)
			{
				$loop = "true";
				$input = explode(" ",$input);
			}

			$bad_strings = array("'", "--", "select", "union", "insert", "update", "like", "delete", "1=1", "or");

			if ($loop)
			{
				foreach($input as $value)
				{
					if (in_array($value, $bad_strings))
					{
					  return false;
					}
					else
					{
					  return true;
					}
				}
			}
			else
			{
				if (in_array($input, $bad_strings))
				{
				  return false;
				}
				else
				{
				  return true;
				}
			}
		}

		/**
		* This enforces W3C specifications for allowed key name strings to prevent
		* malicious stuff.
		*
		* @param   string  string to clean
		* @return  string
		*/
		function clean_input_keys($str)
		{
			$pcre_unicode_properties = (bool) preg_match('/^\pL$/u', 'ñ');
	
			$chars = $pcre_unicode_properties ? '\pL' : 'a-zA-Z';
	
			if (! preg_match('#^[' . $chars . '0-9:_.-]++$#uD', $str))
			{
				exit('Disallowed key characters in global data!!');
			}
	
			return $str;
		}
	
		/**
		 * This escapes data and forces all newline characters to "\n".
		 *
		 * @param   unknown_type  string to clean
		 * @return  string
		 */
		function clean_input_data($str)
		{
			$ezphp_config = setting::getInstance();
			$xss_filter = $ezphp_config->get['security']['xss_filter'];

			if (is_array($str))
			{
				$new_array = array();

				foreach ($str as $key => $val)
				{
					// recursion!
					$new_array[$this->clean_input_keys($key)] = $this->clean_input_data($val);
				}
				
				return $new_array;
			}
	
			if (get_magic_quotes_gpc())
			{
				// remove annoying magic quotes
				$str = stripslashes($str);
			}
	
			/*
			if ($xss_filter == '1')
			{
				$str = $this->clean_xss($str);
			}
			*/
	
			if (strpos($str, "\r") !== false)
			{
				// standardize newlines
				$str = str_replace(array("\r\n", "\r"), "\n", $str);
			}
	
			return $str;
		}
		

		// Replaces special characters with non-special equivalents
		function normalize_chars( $str )
		{
			# Quotes cleanup
			$str = preg_replace( chr(ord("`")), "'", $str ); # `
			$str = preg_replace( chr(ord("´")), "'", $str ); # ´
			$str = preg_replace( chr(ord("„")), ",", $str ); # „
			$str = preg_replace( chr(ord("`")), "'", $str ); # `
			$str = preg_replace( chr(ord("´")), "'", $str ); # ´
			$str = preg_replace( chr(ord("“")), "\"", $str ); # “
			$str = preg_replace( chr(ord("”")), "\"", $str ); # ”
			$str = preg_replace( chr(ord("´")), "'", $str ); # ´
			
			$unwanted_array = array( 'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
			'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
			'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
			'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
			'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
			$str = strtr( $str, $unwanted_array );
			
			# Bullets, dashes, and trademarks
			$str = preg_replace( chr(149), "&#8226;", $str ); # bullet •
			$str = preg_replace( chr(150), "&ndash;", $str ); # en dash
			$str = preg_replace( chr(151), "&mdash;", $str ); # em dash
			$str = preg_replace( chr(153), "&#8482;", $str ); # trademark
			$str = preg_replace( chr(169), "&copy;", $str ); # copyright mark
			$str = preg_replace( chr(174), "&reg;", $str ); # registration mark
			
			return $str;
		}


	}
	
?>