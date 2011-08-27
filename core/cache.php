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
	
	
	class cache
	{ 
		private $expire = NULL;

		public function __construct()
		{
			$files = glob(__SITE_PATH . '/cache/' . 'cache.*');
			$this->expire = (__CACHE_TIME) ? __CACHE_TIME : 3600;
			
			if ($files)
			{
				foreach ($files as $file)
				{
					$time = end(explode('.', basename($file)));

					if ($time < time())
					{
						@unlink($file);
					}
				}
			}
		}

		public function get($key)
		{
			$key = str_replace('/', '_', $key);
			$files = glob(__SITE_PATH . '/cache/' . 'cache.' . $key . '.*');
			
			if ($files)
			{
				foreach ($files as $file)
				{
					$handle = fopen($file, 'r');
					$cache  = fread($handle, filesize($file));
		  
					fclose($handle);

					return unserialize($cache);
				}
			}
		}

		public function set($key, $value)
		{
			$this->delete($key);

			$key = str_replace('/', '_', $key);
			$file = __SITE_PATH . '/cache/' . 'cache.' . $key . '.' . (time() + $this->expire);
			//print $file;exit;
			
			$handle = fopen($file, 'w');

			fwrite($handle, serialize($value));
			
			fclose($handle);
		}
		
		public function delete($key)
		{
			$key = str_replace('/', '_', $key);
			$files = glob(__SITE_PATH . '/cache/' . 'cache.' . $key . '.*');
			
			if ($files)
			{
				foreach ($files as $file)
				{
					@unlink($file);
				}
			}
		}
		
		public function isCached($key)
		{
			$key = str_replace('/', '_', $key);
			$files = glob(__SITE_PATH . '/cache/' . 'cache.' . $key . '.*');
			
			if ($files)
			{
				foreach ($files as $file)
				{
					clearstatcache();
					if (filemtime($file) > (time() - $this->expire))
					{
						$isCached = true;
					}
				}
			}

			return isset($isCached) ? true : false;
		}

		public function clear()
		{
			$files = glob(__SITE_PATH . '/cache/' . 'cache.' . '.*');
			
			if ($files)
			{
				foreach ($files as $file)
				{
					@unlink($file);
				}
			}
		}

		
	}
?>