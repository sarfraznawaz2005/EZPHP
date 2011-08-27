<?php if (!defined('__SITE_PATH')) exit('No Direct Access Allowed !!'); ?>
<?php

	#############################################################
	# EZPHP Framework											#
	#-----------------------------------------------------------#
	# Author: Sarfraz Ahmed Chandio								#
	#-----------------------------------------------------------#
	# Email: sarfraznawaz2005@gmail.com							#
	#-----------------------------------------------------------#
	# Company: The Brains										#
	#-----------------------------------------------------------#
	# Website: http://www.brainstech.com						#
	#-----------------------------------------------------------#
	# Dated: 25th of June 2009									#
	#-----------------------------------------------------------#
	#############################################################
	
	
	# class for reading settings from configuration INI file
	
	class setting
	{
		private static $files = array('config.php', 'routes.php', 'autoload.php');
		private static $instance = NULL;
		public $get = array();
	
		public static function getInstance()
		{
			if(is_null(self::$instance))
			{
				self::$instance = new setting;
			}
			return self::$instance;
		}

		private function __construct()
		{
			if (!in_array('config.php', self::$files))
			{
				exit ("Oops! config/config.php file could not be found !!");
			}
			
			foreach(self::$files as $key => $value)
			{
				if (is_readable(__SITE_PATH . '/config/' . self::$files[$key]))
				{
					if (self::$files[$key] == 'config.php')
					{
						$this->get = parse_ini_file(__SITE_PATH . '/config/' . self::$files[$key], true);
					}
					else
					{
						require __SITE_PATH . '/config/' . self::$files[$key];
						
						$file_name = substr(self::$files[$key], 0, strpos(self::$files[$key], '.'));
						$this->get[$file_name] = ${$file_name};
					}
				}
			}
		}

		# disallow cloning of this class	
		private function __clone()
		{
		}
	}
	
?>