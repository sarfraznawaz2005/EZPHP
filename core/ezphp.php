<?php if (!defined('__SITE_PATH')) exit('No Direct Access Allowed !!'); ?>
<?php

	class ezphp extends application
	{
		static protected $data = array();
		static private $template_name = '';

		function __construct()
		{
			parent::__construct();
		}

		static function ez_get($key)
		{
			return (isset(self::$data[$key]) ? self::$data[$key] : NULL);
		}

		static function ez_set($key, $value)
		{
			self::$data[$key] = $value;
		}

		static function ez_has($key)
		{
			return isset(self::$data[$key]);
		}

		static function ez_remove($key)
		{
			unset(self::$data[$key]);
		}

		static function set_template($template_name)
		{
			self::$template_name = $template_name;
			return self::$template_name;
		}
		
		static function get_template()
		{
			return self::$template_name;
		}
		
		function template_path()
		{
			return self::ez_get('ez_templath_path');
		}

		function full_path()
		{
			return self::ez_get('ez_full_path');
		}

		function view_path()
		{
			return self::ez_get('ez_view_path');
		}

		function content_path()
		{
			return self::ez_get('ez_content_path');
		}

		function base_path()
		{
			return self::ez_get('ez_base_path');
		}

		function dir_name()
		{
			return self::ez_get('ez_dir');
		}

		function site_path()
		{
			return self::ez_get('ez_site_path');
		}

		function site_path_abs()
		{
			return self::ez_get('ez_site_path_abs');
		}

		function export_class()
		{
			echo '<pre>';
			Reflection::export(new ReflectionClass($this->router->getController()));
			exit;
		}

	}
?>