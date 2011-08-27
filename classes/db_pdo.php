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
	
	
	class db_pdo
	{
		private static $instance = NULL;
		
		/*
		 the constructor is set to private so
		 so nobody can create a new instance using new
		*/
		
		private function __construct()
		{
		  # maybe set the db name here later
		}
		
		public static function getInstance()
		{
			if (!self::$instance)
			{
				self::$instance = new PDO("mysql:host=localhost;dbname=webvision", 'root', '');
				self::$instance-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}

			return self::$instance;
		}
		
		/*
		 Like the constructor, we make __clone private
		 so nobody can clone the instance
		*/
		
		private function __clone()
		{
		}
	
	}

?>
