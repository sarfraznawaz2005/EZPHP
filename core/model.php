<?php if (!defined('__SITE_PATH')) exit('No Direct Access Allowed !!'); ?>
<?php

	class model extends ezphp
	{
		function __construct()
		{
			parent::__construct();
		}
	
		public function __get($index)
		{
			return ezphp::ez_get($index);
		}
		
		public function __set($index, $value)
		{
			ezphp::ez_set($index, $value);
		}
		
	}
	
?>
