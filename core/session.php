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
	
	
	class session
	{
		public $data = array();
				
		public function __construct()
		{
			ini_set('session.use_cookies', '1');
			ini_set('session.use_trans_sid', '0');
			
			session_set_cookie_params(0, '/');
			@session_start();
			
			$this->data =& $_SESSION;
		}
		
		public function clear()
		{
		  @session_destroy();
		}
		
	}
	
?>