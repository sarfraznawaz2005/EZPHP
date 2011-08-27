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

	
	class security
	{
		private $key;
		
		function __construct($key)
		{
			$this->key = $key;
		}
		
		function encrypt($value)
		{
			$output = '';
			
			for ($i = 0; $i < strlen($value); $i++)
			{
				$char = substr($value, $i, 1);
				$keychar = substr($this->key, ($i % strlen($this->key)) - 1, 1);
				$char = chr(ord($char) + ord($keychar));
				
				$output .= $char;
			} 
			
			return base64_encode($output); 
		}
		
		function decrypt($value)
		{
			$output = '';
			
			$value = base64_decode($value);
			
			for ($i = 0; $i < strlen($value); $i++)
			{
				$char = substr($value, $i, 1);
				$keychar = substr($this->key, ($i % strlen($this->key)) - 1, 1);
				$char = chr(ord($char) - ord($keychar));
				
				$output .= $char;
			}
			
			return $output;
		}

		# generates valid GUID
		static function get_guid()
		{ 
			$s = strtoupper(md5(uniqid(rand(),true)));
			
			$guidText = 
				substr($s,0,8) . '-' . 
				substr($s,8,4) . '-' . 
				substr($s,12,4). '-' . 
				substr($s,16,4). '-' . 
				substr($s,20); 
			
			return strtolower($guidText);
		}
		
		function random_string($length = 8)
		{
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQ 
					 RSTUVWXYZ0123456789!%,-:;@_{}~";
					 
			for ($i = 0, $makepass = '', $len = strlen($chars); $i < $length; $i++)
			{
				$makepass .= $chars[mt_rand(0, $len-1)];
			}
			
			return $makepass;
		}
		
		public function get_ip()
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
	}
	
?>