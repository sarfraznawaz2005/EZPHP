<?php
/**
 * Super Global Secure Class File
 *
 * @author Bendikt Martin Myklebust <bendikt@armed.nu>
 * @all code is released under the GNU General Public License V3.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * Example

	include('secure.class.php'); // include the class
	$secure = new secure(); // load the class
	$secure->secureGlobals(); // run the main class function
 
 *
 *
 */


class secure
{
	function secureSuperGlobalGET(&$value, $key)
	{
		$_GET[$key] = htmlspecialchars(stripslashes($_GET[$key]));
		$_GET[$key] = str_ireplace("script", "blocked", $_GET[$key]);
		$_GET[$key] = mysql_real_escape_string($_GET[$key]);
		return $_GET[$key];
	}
	
	function secureSuperGlobalPOST(&$value, $key)
	{
		$_POST[$key] = htmlspecialchars(stripslashes($_POST[$key]));
		$_POST[$key] = str_ireplace("script", "blocked", $_POST[$key]);
		$_POST[$key] = mysql_real_escape_string($_POST[$key]);
		return $_POST[$key];
	}
		
	function secureGlobals()
	{
		array_walk($_GET, array($this, 'secureSuperGlobalGET'));
		array_walk($_POST, array($this, 'secureSuperGlobalPOST'));
	}
}
?>
