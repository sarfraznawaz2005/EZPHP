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
		This class uses built-in Character Type functions (ctype extension) 
		as well as custom functions to validate the user input.
		
		Argument Types
		------------------------
		alnum 					= Check for alphanumeric character(s)
		alpha 					= Check for alphabetic character(s)
		control 				= Check for control character(s) -> line feed, tab, esc
		digit/number/numeric 	= Check for numeric character(s)
		graph 					= Check for any printable character(s) except space
		lower 					= Check for lowercase character(s)
		print 					= Check for printable character(s)
		punct/punctuation		= Checks for punctuation character(s)
		space/whitespace		= Check for whitespace character(s)
		upper 					= Check for uppercase character(s)
		xdigit/hexa				= Check for hexadecimal digit character(s)
		length					= Checks for length specified (in third argument)
		regex					= Checks for regular expression
		email					= Checks for proper email
		string					= Checks for string
		float					= Checks for float numbers
		url/web					= Checks for proper url/website
		ipv4					= Checks for ipv4 IP address
		ipv6					= Checks for ipv6 IP address
		
	**/
	
	class validator
	{
		
		function validate($str, $vtype = NULL, $option = NULL)
		{
			# check for required fields
			if (is_null($vtype))
			{
				return (!empty($str)) ? true : false;
			}
		
			switch ($vtype)
			{
				case strtolower('alnum'):
					return (preg_match('/^[a-z0-9 ]*$/i', utf8_decode($str))) ? true : false;
					break;
				case strtolower('alpha'):
					return (preg_match('/^[a-z ]*$/i', utf8_decode($str))) ? true : false;
					break;
				case strtolower('control'):
					return (ctype_cntrl(utf8_decode($str))) ? true : false;
					break;
				case strtolower('digit'):
				case strtolower('number'):
				case strtolower('numeric'):
					return (preg_match('/^[0-9,.]*$/i', utf8_decode($str))) ? true : false;
					break;
				case strtolower('graph'):
					return (ctype_graph(utf8_decode($str))) ? true : false;
					break;
				case strtolower('lower'):
					return (ctype_lower(utf8_decode($str))) ? true : false;
					break;
				case strtolower('print'):
					return (ctype_print(utf8_decode($str))) ? true : false;
					break;
				case strtolower('punct'):
				case strtolower('punctuation'):
					return (ctype_punct(utf8_decode($str))) ? true : false;
					break;
				case strtolower('space'):
				case strtolower('whitespace'):
					return (ctype_space(utf8_decode($str))) ? true : false;
					break;
				case strtolower('upper'):
					return (ctype_upper(utf8_decode($str))) ? true : false;
					break;
				case strtolower('xdigit'):
				case strtolower('hexa'):
					return (ctype_xdigit(utf8_decode($str))) ? true : false;
					break;
				case strtolower('length'):
					
					# for length
					if (is_null($option) || !is_numeric($option))
					{
						return 'The length is not specified or is invalid in third argument!';
					}
					
					return (strlen(utf8_decode($str)) > $length) ? false : true;
					break;
				case strtolower('regex'):
					
					# for regex
					if (is_null($option))
					{
						return 'The pattern is not specified or is invalid in third argument!';
					}
					
					return (preg_match("'" . $option . "'", $str)) ? true : false;
					break;
				case strtolower('email'):
					return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? false : true;
					break;
				case strtolower('string'):
					return (is_string(utf8_decode($str))) ? true : false;
					break;
				case strtolower('float'):
					return (filter_var($str, FILTER_VALIDATE_FLOAT) === true) ? true : false;
					break;
				case strtolower('url'):
				case strtolower('web'):
					return (filter_var($str, FILTER_VALIDATE_URL) === true) ? true : false;
					break;
				case strtolower('ipv4'):
					return (filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === true) ? true : false;
					break;
				case strtolower('ipv6'):
					return (filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === true) ? true : false;
					break;


				default:
				print "Invalid Validator Type Specified !!";
				exit;
			}
		}
		
	}

?>