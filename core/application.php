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
	

	/*
		Note: Anything put in this class will be available to all models,
		views and controllers
	*/
	
	class application
	{
		function __construct()
		{
			// any class initialization code goes here
			
			//we want copyright var to be available to all views
			$this->copyright = 'Copyright Info';
			$this->title = 'EZPHP Framework';
		}
	
		/*
			As an example, hello function below will be available to models, views and controllers
			and accessed like this:
			
			$this->hello();
			
			You could also set properties (variables) on top of this class to make them
			universally available.
		*/
		
		function hello()
		{
			print 'Hello World !!';
		}
	
	}

?>
