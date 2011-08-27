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
	
	
	class cssjs
	{
		private static $css_data = array();
		private static $js_data = array();
				
		public function addCSS($file, $compress = false)
		{
			if (!isset($file))
			{
				exit('CSS file path not specified !!');
				return false;
			}
			
			if (!file_exists($file))
			{
				exit('The CSS file does not exist !!');
				return false;
			}
			
			$handle = fopen($file, 'r');
			$css_data = fread($handle, filesize($file));
			fclose($handle);
			
			self::$css_data = ($compress == true) ? preg_replace('/\s+/', ' ', $css_data) : $css_data;
		}
		
		public function renderCSS()
		{
			header("Content-type: text/css");
			print self::$css_data;
		}

		public function addJS($file, $compress = false)
		{
			if (!isset($file))
			{
				exit('Javascript file path not specified !!');
				return false;
			}
			
			if (!file_exists($file))
			{
				exit('The Javascript file does not exist !!');
				return false;
			}
			
			$handle = fopen($file, 'r');
			$js_data = fread($handle, filesize($file));
			fclose($handle);
			
			self::$js_data = ($compress == true) ? preg_replace('/\s+/', ' ', $js_data) : $js_data;
		}
		
		public function renderJS()
		{
			header("Content-type: text/javascript");
			print self::$js_data;
		}
		
	}
	
?>