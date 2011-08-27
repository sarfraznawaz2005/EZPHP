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
		Example Use:
		
		try
		{
			$log = new logger();
			$log->write('An error has occured', __FILE__, __LINE__);
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
		
	
	*/
	
	class logger
	{
		private $logfile = __LOG_FILE;

		public function write($msg, $file = NULL, $line = NULL)
		{
			$msg = date("d-m-Y - h:i:s", strtotime(now)) .' - '.$msg;
			$msg .= is_null($file) ? '' : " in $file";
			$msg .= is_null($line) ? '' : " on line $line";
			$msg .= "\n";
			
			$handle = fopen($this->logfile, 'a+');
			$response = fwrite($handle, $msg);
			fclose($handle);
			
			return $response;
		}

	}

?>

