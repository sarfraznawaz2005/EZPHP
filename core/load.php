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
	
	class load
	{
		static $cls_objects = array();
		static $mod_objects = array();
	
		// loads classes
		function cls($file_name, $args = array(), $object_name = '')
		{
			if (!strlen($file_name))
			{
				return false;
			}
			
			if (ezphp::ez_get('cls_' . $file_name))
			{
				return ezphp::ez_get('cls_' . $file_name);
			}
			elseif (isset(self::$cls_objects['cls_' . $file_name]))
			{
				return self::$cls_objects['cls_' . $file_name];
			}

			if (!is_readable(__SITE_PATH . "/classes/" . $file_name . ".php"))
			{
				exit ('<strong>Could not find the class: ' . __SITE_PATH . "/classes/" . $file_name . ".php" . '</strong>');
				return false;
			}
			
			include __SITE_PATH . "/classes/" . $file_name . ".php";
			
			// get class name from subdir-x if specified one
			if (strpos($file_name, '/') !== false)
			{
				$file_name = end(explode('/', $file_name));
			}
			

			if(class_exists($file_name))
			{
				if (count($args) === 0)
				{
					$class_instance = new $file_name();
				}
				elseif (count($args) === 1)
				{
					if (is_array($args))
					{
						$class_instance = new $file_name($args[0]);
					}
					else
					{
						$class_instance = new $file_name($args);
					}
				}
				elseif (count($args) > 1)
				{
					if (method_exists($file_name,  '__construct') === false)
					{
						exit("Constructor for the class <strong>$file_name</strong> does not exist, you should not pass arguments to the constructor of this class!");
					}
				
					$refMethod = new ReflectionMethod($file_name,  '__construct');
					$params = $refMethod->getParameters();

					$re_args = array();

					foreach($params as $key => $param)
					{
						if ($param->isPassedByReference())
						{
							$re_args[$key] = &$args[$key];
						}
						else
						{
							$re_args[$key] = $args[$key];
						}
					}
				
					$refClass = new ReflectionClass($file_name);
					$class_instance = $refClass->newInstanceArgs((array) $re_args);
				}

				// make it global for use in Cs, Vs
				$ref_name = $file_name;
				
				if (strlen($object_name))
				{
					$ref_name = $object_name;
					ezphp::ez_set($object_name, $class_instance);
				}
				else
				{
					ezphp::ez_set($file_name, $class_instance);
				}

				// for instance returning
				self::$cls_objects['cls_' . $ref_name] = $class_instance;
				ezphp::ez_set('cls_' . $ref_name, $class_instance);
				
				if (is_object($class_instance))
				{
					return $class_instance;
				}
				else
				{
					exit('Could not instantiate the class: ' . __SITE_PATH . "/classes/" . $file_name . ".php");
				}
				
			}
			else
			{
				exit ("<strong>Class could not be found: $file_name</strong>");
				return false;
			}
		}


		// loads model classes
		function model($model, $object_name = '')
		{
			if (!strlen($model))
			{
				return false;
			}
		
			if (ezphp::ez_get('mod_' . $model))
			{
				return ezphp::ez_get('mod_' . $model);
			}
			elseif (isset(self::$mod_objects['mod_' . $model]))
			{
				return self::$mod_objects['mod_' . $model];
			}

			/*
			// get the respective controller's path for the model
			if (strpos($model, '/') !== false)
			{
				global $router;
				$controller_path = $router->controllerPath();
				$controller_path = substr(strrchr($controller_path, 'controllers'), 11);
				$model_path = str_ireplace(end(explode('/', $router->controllerPath())), '', $controller_path);
				$path = __SITE_PATH . '/content/models' . $model_path . $model . '.php';
			}
			else
			{
				$path = __SITE_PATH . '/content/models/' . $model . '.php';
			}
			*/

			$path = __SITE_PATH . '/content/models/' . $model . '.php';
			
			$model_class = $model;
			
			// get class name from subdir-x if specified one
			if (strpos($model, '/') !== false)
			{
				$model_class = end(explode('/', $model));
				$model = str_replace('/', '_', $model);
				$model = str_replace('\\', '_', $model);
			}
			
			$class = $model_class . '_model';
			
			if (file_exists($path) === false)
			{
				throw new Exception('Model Not Found: ' . $path);
				return false;
			}

			// include the model class
			include ($path);

			if(class_exists($class))
			{
				$class_instance = new $class();
				
				// make it global for use in Cs, Vs
				$ref_name = $model;
				
				if (strlen($object_name))
				{
					$ref_name = $object_name;
					ezphp::ez_set($object_name, $class_instance);
				}
				else
				{
					ezphp::ez_set($model, $class_instance);
				}
	
				
				// for instance returning
				self::$mod_objects['mod_' . $ref_name] = $class_instance;
				ezphp::ez_set('mod_' . $ref_name, $class_instance);
				
				if (is_object($class_instance))
				{
					return $class_instance;
				}
				else
				{
					exit('Could not instantiate the class: ' . __SITE_PATH . '/content/models/' . $model . '.php');
				}
			}
			else
			{
				exit ("<strong>Class could not be found: $class</strong>");
				return false;
			}

		}
		
	}

?>