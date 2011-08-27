<?php if (!defined('__SITE_PATH')) exit('No Direct Access Allowed !!'); ?>
<?php

	class upload
	{
		private $max_size		= 0;
		private $upload_path	= "";
		private $overwrite		= false;
		private $error_msg		= array();
		private $allowed_types	= array();
		private $file_temp		= "";
		private $file_name		= "";
		private $orig_name		= "";
		private $file_type		= "";
		private $file_size		= 0;
		private $file_ext		= "";
		private $clean_xss		= false;
		private $mimes			= array();

		private $default_mimes = array(	'hqx'	=>	'application/mac-binhex40',
						'cpt'	=>	'application/mac-compactpro',
						'csv'	=>	array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel'),
						'bin'	=>	'application/macbinary',
						'dms'	=>	'application/octet-stream',
						'lha'	=>	'application/octet-stream',
						'lzh'	=>	'application/octet-stream',
						'exe'	=>	'application/octet-stream',
						'class'	=>	'application/octet-stream',
						'psd'	=>	'application/x-photoshop',
						'so'	=>	'application/octet-stream',
						'sea'	=>	'application/octet-stream',
						'dll'	=>	'application/octet-stream',
						'oda'	=>	'application/oda',
						'pdf'	=>	array('application/pdf', 'application/x-download'),
						'ai'	=>	'application/postscript',
						'eps'	=>	'application/postscript',
						'ps'	=>	'application/postscript',
						'smi'	=>	'application/smil',
						'smil'	=>	'application/smil',
						'mif'	=>	'application/vnd.mif',
						'xls'	=>	array('application/excel', 'application/vnd.ms-excel', 'application/msexcel'),
						'ppt'	=>	array('application/powerpoint', 'application/vnd.ms-powerpoint'),
						'wbxml'	=>	'application/wbxml',
						'wmlc'	=>	'application/wmlc',
						'dcr'	=>	'application/x-director',
						'dir'	=>	'application/x-director',
						'dxr'	=>	'application/x-director',
						'dvi'	=>	'application/x-dvi',
						'gtar'	=>	'application/x-gtar',
						'gz'	=>	'application/x-gzip',
						'php'	=>	'application/x-httpd-php',
						'php4'	=>	'application/x-httpd-php',
						'php3'	=>	'application/x-httpd-php',
						'phtml'	=>	'application/x-httpd-php',
						'phps'	=>	'application/x-httpd-php-source',
						'js'	=>	'application/x-javascript',
						'swf'	=>	'application/x-shockwave-flash',
						'sit'	=>	'application/x-stuffit',
						'tar'	=>	'application/x-tar',
						'tgz'	=>	'application/x-tar',
						'xhtml'	=>	'application/xhtml+xml',
						'xht'	=>	'application/xhtml+xml',
						'zip'	=>  array('application/x-zip', 'application/zip', 'application/x-zip-compressed'),
						'mid'	=>	'audio/midi',
						'midi'	=>	'audio/midi',
						'mpga'	=>	'audio/mpeg',
						'mp2'	=>	'audio/mpeg',
						'mp3'	=>	array('audio/mpeg', 'audio/mpg'),
						'aif'	=>	'audio/x-aiff',
						'aiff'	=>	'audio/x-aiff',
						'aifc'	=>	'audio/x-aiff',
						'ram'	=>	'audio/x-pn-realaudio',
						'rm'	=>	'audio/x-pn-realaudio',
						'rpm'	=>	'audio/x-pn-realaudio-plugin',
						'ra'	=>	'audio/x-realaudio',
						'rv'	=>	'video/vnd.rn-realvideo',
						'wav'	=>	'audio/x-wav',
						'bmp'	=>	'image/bmp',
						'gif'	=>	'image/gif',
						'jpeg'	=>	array('image/jpeg', 'image/pjpeg'),
						'jpg'	=>	array('image/jpeg', 'image/pjpeg'),
						'jpe'	=>	array('image/jpeg', 'image/pjpeg'),
						'png'	=>	array('image/png',  'image/x-png'),
						'tiff'	=>	'image/tiff',
						'tif'	=>	'image/tiff',
						'css'	=>	'text/css',
						'html'	=>	'text/html',
						'htm'	=>	'text/html',
						'shtml'	=>	'text/html',
						'txt'	=>	'text/plain',
						'text'	=>	'text/plain',
						'log'	=>	array('text/plain', 'text/x-log'),
						'rtx'	=>	'text/richtext',
						'rtf'	=>	'text/rtf',
						'xml'	=>	'text/xml',
						'xsl'	=>	'text/xml',
						'mpeg'	=>	'video/mpeg',
						'mpg'	=>	'video/mpeg',
						'mpe'	=>	'video/mpeg',
						'qt'	=>	'video/quicktime',
						'mov'	=>	'video/quicktime',
						'avi'	=>	'video/x-msvideo',
						'movie'	=>	'video/x-sgi-movie',
						'doc'	=>	'application/msword',
						'docx'	=>	'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
						'xlsx'	=>	'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
						'word'	=>	array('application/msword', 'application/octet-stream'),
						'xl'	=>	'application/excel',
						'eml'	=>	'message/rfc822'
					);
		
		
		function __construct($options = array())
		{
			$properties = array(
								 'max_size'			=> 0,
								 'upload_path'		=> "",
								 'overwrite'		=> false,
								 'error_msg'		=> array(),
								 'allowed_types'	=> array(),
								 'file_temp'		=> "",
								 'file_name'		=> "",
								 'orig_name'		=> "",
								 'file_type'		=> "",
								 'file_size'		=> 0,
								 'file_ext'			=> "",
								 'clean_xss'		=> false,
								 'mimes'			=> array()

							);
		
		
			foreach ($properties as $key => $value)
			{
				if (isset($options[$key]))
				{
					$function = 'set_' . $key;
					
					if (method_exists($this, $function))
					{
						$this->$function($options[$key]);
					}
					else
					{
						$this->$key = $options[$key];
					}
				}
				else
				{
					$this->$key = $value;
				}
			}
		}
	
	
		function info()
		{
			return array (
							'file_name'			=> $this->file_name,
							'file_type'			=> $this->file_type,
							'file_path'			=> $this->upload_path,
							'full_path'			=> $this->upload_path . $this->file_name,
							'raw_name'			=> str_replace($this->file_ext, '', $this->file_name),
							'orig_name'			=> $this->orig_name,
							'file_ext'			=> $this->file_ext,
							'file_size'			=> $this->file_size . 'kb'
						);
		}
	
	
		function upload($field)
		{
			// do we have FILES array set? Set it in either case.
			$field = is_array($field) ? $field : $_FILES[$field];
			//echo '<pre>';
			//print_r($field);exit;

			if (! isset($field))
			{
				$this->set_error('No file selected');
				return false;
			}

			
			if (! $this->_validate_path())
			{
				return false;
			}
	
			if (! is_uploaded_file($field['tmp_name']))
			{
				$error = (! isset($field['error'])) ? 4 : $field['error'];
	
				switch($error)
				{
					case 1:
						$this->set_error('File exceeds limit');
						break;
					case 2:
						$this->set_error('File exceeds form limit');
						break;
					case 3:
					   $this->set_error('File partial');
						break;
					case 4:
					   $this->set_error('No file selected');
						break;
					case 6:
						$this->set_error('No temp directory');
						break;
					case 7:
						$this->set_error('Unable to write file');
						break;
					case 8:
						$this->set_error('Stopped by extension');
						break;
					default :   $this->set_error('No file selected');
						break;
				}
	
				return false;
			}
	
	
			$this->file_temp = $field['tmp_name'];		
			$this->file_name = $this->_prep_filename($field['name']);
			$this->file_size = $field['size'];		
			$this->file_type = preg_replace("/^(.+?);.*$/", "\\1", $field['type']);
			$this->file_type = strtolower($this->file_type);
			$this->file_ext	 = $this->get_extension($field['name']);
			
			// size to kilobytes
			if ($this->file_size > 0)
			{
				$this->file_size = round($this->file_size/1024, 2);
			}

			if (! $this->is_allowed_filesize())
			{
				$this->set_error('Invalid filesize');
				return false;
			}
	
			if (count($this->allowed_types) && !$this->is_allowed_filetype())
			{
				$this->set_error('Invalid filetype');
				return false;
			}
	
			$this->file_name = $this->clean_file_name($this->file_name);
			
			// Remove white spaces in the name
			$this->file_name = preg_replace("/\s+/", "_", $this->file_name);

	
			$this->orig_name = $this->file_name;
	
			if ($this->overwrite == false)
			{
				$this->file_name = $this->set_filename($this->upload_path, $this->file_name);
				
				if ($this->file_name == false)
				{
					return false;
				}
			}
	
			/*
			 * Move the file to the final destination
			 * To deal with different server configurations
			 * we'll attempt to use copy() first.  If that fails
			 * we'll use move_uploaded_file().  One of the two should
			 * reliably work in most environments
			 */
			 
			if (! @copy($this->file_temp, $this->upload_path . $this->file_name))
			{
				if (! @move_uploaded_file($this->file_temp, $this->upload_path . $this->file_name))
				{
					 $this->set_error('Destination error');
					 return false;
				}
			}
			
			if ($this->clean_xss == true)
			{
				// is uploaded file an image?
				if (getimagesize($this->upload_path . $this->file_name) !== false)
				{
					$this->do_clean_xss(true);
				}
				else
				{
				  $this->do_clean_xss();
				}				
			}
	
			return true;
		}

		function do_clean_xss($is_image = false)
		{		
			$file = $this->upload_path . $this->file_name;
			
			if (filesize($file) == 0)
			{
				return false;
			}
	
			if (($data = @file_get_contents($file)) === false)
			{
				return false;
			}
			
			if (! $fp = @fopen($file, 'r+b'))
			{
				return false;
			}
	
			if ($is_image == true)
			{
				// short tags not processed for images for they contain it usually
				$data = str_replace(array('<?php', '<?PHP'), array('&lt;?php', '&lt;?PHP'), $data);
			}
			else
			{
				$input = ezphp::ez_get('input');
				$data = $input->clean_xss($data);
			}

			flock($fp, LOCK_EX);
			fwrite($fp, $data);
			flock($fp, LOCK_UN);
			fclose($fp);
		}

		function set_upload_path($path)
		{
			$this->upload_path = rtrim($path, '/') . '/';
		}
	
		function set_filename($path, $filename)
		{
			if (! file_exists($path.$filename))
			{
				return $filename;
			}
		
			$filename = str_replace($this->file_ext, '', $filename);
			
			$new_filename = '';
			
			for ($i = 1; $i < 100; $i++)
			{			
				if ( ! file_exists($path.$filename.$i.$this->file_ext))
				{
					$new_filename = $filename.$i.$this->file_ext;
					break;
				}
			}
	
			if ($new_filename == '')
			{
				$this->set_error('Bad filename');
				return false;
			}
			else
			{
				return $new_filename;
			}
		}
		
		function set_max_filesize($n)
		{
			$this->max_size = ((int) $n < 0) ? 0: (int) $n;
		}
		
		function set_allowed_types($types)
		{
			$this->allowed_types = explode('|', $types);
		}
		
		function set_clean_xss($flag = false)
		{
			$this->clean_xss = ($flag == true) ? true : false;
		}
	
		function set_error($msg)
		{
			if (is_array($msg))
			{
				foreach ($msg as $val)
				{
					$this->error_msg[] = $msg;
				}		
			}
			else
			{
				$this->error_msg[] = $msg;
			}
		}
	
		function display_errors()
		{
			$str = '';
			
			foreach ($this->error_msg as $val)
			{
				$str .= $val . '<br /><br />';
			}
		
			return $str;
		}

		private function _validate_path()
		{
			if ($this->upload_path == '')
			{
				$this->set_error('No filepath');
				return false;
			}
			
			if (function_exists('realpath') AND @realpath($this->upload_path) !== false)
			{
				$this->upload_path = str_replace("\\", "/", realpath($this->upload_path));
			}
	
			if (! @is_dir($this->upload_path))
			{
				$this->set_error('No filepath');
				return false;
			}
	
			if (! $this->is_really_writable($this->upload_path))
			{
				$this->set_error('Not writable');
				return false;
			}
	
			$this->upload_path = preg_replace("/(.+?)\/*$/", "\\1/",  $this->upload_path);
			return true;
		}

		function mimes_types($mime)
		{
			global $mimes;
		
			if (count($this->mimes) == 0)
			{
				$this->mimes = $this->default_mimes;
				unset($this->default_mimes);
			}
		
			return (! isset($this->mimes[$mime])) ? false : $this->mimes[$mime];
		}

		function _prep_filename($filename)
		{
			if (strpos($filename, '.') === false)
			{
				return $filename;
			}
			
			$parts		= explode('.', $filename);
			$ext		= array_pop($parts);
			$filename	= array_shift($parts);
					
			foreach ($parts as $part)
			{
				if ($this->mimes_types(strtolower($part)) === false)
				{
					$filename .= '.' . $part . '_';
				}
				else
				{
					$filename .= '.' . $part;
				}
			}
			
			$filename .= '.' . $ext;
			
			return $filename;
		}

		function get_extension($filename)
		{
			$x = explode('.', $filename);
			return '.'.end($x);
		}	

		function clean_file_name($filename)
		{
			$bad = array(
							"<!--",
							"-->",
							"'",
							"<",
							">",
							'"',
							'&',
							'$',
							'=',
							';',
							'?',
							'/',
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
						
			$filename = str_replace($bad, '', $filename);
	
			return stripslashes($filename);
		}

		function is_allowed_filesize()
		{
			if ($this->max_size != 0  AND  $this->file_size > $this->max_size)
			{
				return false;
			}
			else
			{
				return true;
			}
		}

		function is_allowed_filetype()
		{
			/*
			if (count($this->allowed_types) == 0 OR ! is_array($this->allowed_types))
			{
				$this->set_error('No file types');
				return false;
			}
			*/

			$image_types = array('gif', 'jpg', 'jpeg', 'png', 'jpe');
	
			foreach ($this->allowed_types as $val)
			{
				$mime = $this->mimes_types(strtolower($val));
	
				// Images get some additional checks
				if (in_array($val, $image_types))
				{
					if (getimagesize($this->file_temp) === false)
					{
						return false;
					}
				}
	
				if (is_array($mime))
				{
					if (in_array($this->file_type, $mime, true))
					{
						return true;
					}
				}
				else
				{
					if ($mime == $this->file_type)
					{
						return true;
					}
				}		
			}
			
			return false;
		}

		function is_really_writable($file)
		{	
			// If we're on a Unix server with safe_mode off we call is_writable
			if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == false)
			{
				return is_writable($file);
			}
		
			if (is_dir($file))
			{
				$file = rtrim($file, '/').'/'.md5(rand(1,100));
		
				if (($fp = @fopen($file, 'ab')) === false)
				{
					return false;
				}
		
				fclose($fp);
				@chmod($file, 0777);
				@unlink($file);
				return true;
			}
			elseif (($fp = @fopen($file, 'ab')) === false)
			{
				return false;
			}
		
			fclose($fp);
			return true;
		}

	}

?>