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
		Example:

		if ($browser->name == 'msie' && $browser->version <= 6 && $browser->os == 'macintosh')
		{
			echo '
				body {background: #eee}
				a:link {color: #222}
			';
		}		
	
	
	*/


	class browser
	{
		private $props  = array('version' => '0.0.0',
								'name' => 'unknown',
								'agent' => 'unknown',
					'os' => 'unkown');
	 
		public function __construct()
		{
			$browsers = array("firefox", "msie", "opera", "chrome", "safari",
								"mozilla", "seamonkey",  "konqueror", "netscape",
								"gecko", "navigator", "mosaic", "lynx", "amaya",
								"omniweb", "avant", "camino", "flock", "aol", "webtv",
					"netpositive", "mspie", "galeon", "icab", "phoenix", "firebird");
	 
			$this->agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	 
			if (preg_match ("win/i", $this->agent))
				$this->os = "windows";
			elseif (preg_match ("iPhone/i", $this->agent))
				$this->os = "iphone";
			elseif (preg_match ("mac/i", $this->agent))
				$this->os = "macintosh";
			elseif (preg_match ("linux/i", $this->agent))
				$this->os = "linux";
			elseif (preg_match ("OS/2/i", $this->agent))
				$this->os = "os/2";
			elseif (preg_match ("BeOS/i", $this->agent))
				$this->os = "beos";
	 
			foreach($browsers as $browser)
			{
				if (preg_match("#($browser)[/ ]?([0-9.]*)#", $this->agent, $match))
				{
					$this->name = $match[1];
					$this->version = $match[2];
					break;
				}
			}
		}
	 
		public function __get($name)
		{
			if (!array_key_exists($name, $this->props))
			{
			   SimpleError("No such property or function.", "Failed to set $name", $this->props);
			}
			return $this->props[$name];
		}
	 
		public function __set($name, $val)
		{
			if (!array_key_exists($name, $this->props))
			{
				SimpleError("No such property or function.", "Failed to set $name", $this->props);
				die;
			}
			$this->props[$name] = $val;
		}
	 
	}
?>