<?php if (!defined('__SITE_PATH')) exit('No Direct Access Allowed !!'); ?>
<?php

	abstract class controller extends ezphp
	{
		function __construct()
		{
			parent::__construct();
		}
	
		protected function __get($index)
		{
			return ezphp::ez_get($index);
		}
		
		protected function __set($index, $value)
		{
			ezphp::ez_set($index, $value);
		}
		
		/*abstract function index();*/

	}

?>
