<?php

	// This file does the proper handling of functions/etc for compatibility if we are
	// under PHP6

	/*
	
	------------------------------------------------------
	PHP6 NOTES
	------------------------------------------------------
	
	
	1. Call Time Pass by Reference
	-------------------------------
	Don't initiate your objects with the reference operator anymore.
	
	// incorrect
	$var =& new stdObject(); //current object reference
	// correct
	$var = new stdObject(); //just do this instead 
	
	
	2. register_long_arrays, HTTP_*_VARS
	-------------------------------------
	Removed and if you are not using $_POST and $_GET, do so!
	
	
	3. Case Sensitivity
	-------------------
	Case insensitivity of functions and classes. Use case-sensitive names

	
	4. ereg
	-------
	ereg removed. use preg instead.
	
	
	5. Strings and {}
	-----------------
	Instead of using $somevar{1}, use $somevar[1]
	
	
	6. Error Supression @
	----------------------
	Because @ operator is very slow, it won't work on ini_set eg @ini_set.
	
	
	7. break $somevar
	-----------------
	Removed support for dynamic breaks. You can't do this:
	
		break $somevar;
	
	You need to find an alternative to that.
	
	
	8. Database Extensions/Objects
	------------------------------
	Use PDO as early as you can (PHP 5.1). Their plan is to slowly move
	Non-PDO database extensions out of the core. It is good idea to migrate
	to those as soon as you can.
	
	
	9. ASP Style Tags <% %> and Short Tags
	--------------------------------------
	ASP style tags are no more supported. Also for security reasons, you should
	use full PHP tags eg <?php ?> instead of <?=$somevar?>.
	
	
	10. safe_mode
	-------------
	No more there.
	
	
	11. var
	------------
	"var" keyword is now an alias of "public"; not used for variable declaration any more.
	
	
	12. Support for Freetype 1 and GD 1
	--------------------------------------
	Support for both of them removed cause they are archaic.
	
	
	13. ?: operator
	----------------
	They drop the middle value for the ?: operator eg:
	

	
	*/

	if (version_compare(phpversion(), '6', '>='))
	{

		// no more register_globals
		if (ini_get('register_globals'))
		{
			ini_set('register_globals', false);
		}
	
		// no more magic quotes
		function get_magic_quotes_gpc()
		{
			return false;
		}
		
		// zend compatibility mode setting
		if (ini_get('zend.ze1_compatibility_mode'))
		{
			ini_set('zend.ze1_compatibility_mode', false);
		}

		// disable magic_quotes_runtime
		if (get_magic_quotes_runtime())
		{
			@set_magic_quotes_runtime(0);
		}
		
	}

?>