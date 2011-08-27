<?php 

	class test
	{
		private $a = '';
		private $b = '';
		private $c = '';

		/*

		Reflection class's newInstanceArgs is equivilant to call_user_func_array() while
		Reflection::newInstance() is equivilant to call_user_func() 
		
		the newInstanceArgs function cannot call a class' constructor if it has references
		in its arguments, so be careful what you pass into it: 

		Be aware that calling the method newInstanceArgs with an empty array will still call
		the constructor with no arguments. If the class has no constructor then it will generate
		an exception. 

		You need to check if a constructor exists before calling this method or use try and
		catch to act on the exception. 

		*/

		// references do not work for Reflection class
		function __construct(&$a, &$b, &$c)
	//	function __construct($a, $b, $c)
		{
			$this->a = $a;
			$this->b = $b;
			$this->c = $c;
		}
		
		function show()
		{
			echo 'Here are the arguments<br /><br />';
			echo $this->a . '<br />';
			echo $this->b . '<br />';
			echo $this->c . '<br />';
		}
	}

?>