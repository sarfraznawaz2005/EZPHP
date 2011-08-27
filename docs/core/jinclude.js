/*
	Client Side File Include
	------------------------
	Author: Sarfraz Ahmed Chandio
	Website: http://sarfraznawaz.wordpress.com
	Dated: 24 December 2009
	Copyright 2009-2010
	
	Desciption:
	-----------
	This piece of javascript code allows you to insert files
	into document like server-side scripting languages such
	as PHP, ASP, etc.
	
	Usage:
	-------
		jinclude(path, elem_id);

	Where:
		'path' is the path of the file you want to include.
		'elem_id' is the id of the element where contents of file will be written.
		
	Note:
	------
	It is recommended to use this function inside onload event of the documenet or
	simply put it on the bottom of the page after all content.
	
	window.onload = function()
	{
		jinclude(path, elem_id);
	}
*/

function getXMLHttp()
{
  var XMLHttp = null;
  
  if (window.XMLHttpRequest)
  {
	try
	{
	  XMLHttp = new XMLHttpRequest();
	}
	catch (e){}
  }
  else if (window.ActiveXObject)
  {
	try
	{
	  XMLHttp = new ActiveXObject("Msxml2.XMLHTTP");
	}
	catch (e)
	{
	  try
	  {
		XMLHttp = new ActiveXObject("Microsoft.XMLHTTP");
	  }
	  catch (e){}
	}
  }

	return XMLHttp;
}

function jinclude(path, elem_id)
{
	var elem = document.getElementById(elem_id);
	var XMLHttp = getXMLHttp();
	
	XMLHttp.open("GET", path);
	
	XMLHttp.onreadystatechange = function()
	{
	  if (XMLHttp.readyState == 4)
	  {
		  if (document.getElementById(elem_id))
		  {
			elem.innerHTML = XMLHttp.responseText;
		  }
	  }
	};
	
	XMLHttp.send(null);
}
