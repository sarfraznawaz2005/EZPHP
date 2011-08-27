<?php

	/*
		-----------------------------------------------------------
		Purpose: Miscellaneous useful functions.
		-----------------------------------------------------------

		-----------------------------------------------------------
		Author/Source: Provided within functions
		-----------------------------------------------------------
		
		-----------------------------------------------------------
		Example Use: Provided within functions
		-----------------------------------------------------------
		
	*/

class misc_funcs
{

	// generates textual tables from given data
	
	/*
		Example:

		echo "<pre>\n";
		echo text_table($array);
		echo "</pre>\n";
		
	*/
	
	function text_table($data)
	{
		$keys = array_keys(end($data));
		$size = array_map('strlen', $keys);
		
		foreach(array_map('array_values', $data) as $e)
			$size = array_map('max', $size,
				array_map('strlen', $e));

		foreach($size as $n) {
			$form[] = "%-{$n}s";
			$line[] = str_repeat('-', $n);
		}
		
		$form = '| ' . implode(' | ', $form) . " |\n";
		$line = '+-' . implode('-+-', $line) . "-+\n";
		$rows = array(vsprintf($form, $keys));
		
		foreach($data as $e)
			$rows[] = vsprintf($form, $e);
		return $line . implode($line, $rows) . $line;
	}

	function fun_text($text)
	{
		if (empty($text))
		{
			return $text;
		}
		
		$output="";
		$cur=0;
		$turn=0;

		while($cur<=strlen($text)){
			$curchar = substr($text,$cur,1);
			$cur++;
			if($turn==0){
				$turn=1;
				$output .= "<font face=\"arial\" size=\"2\">".$curchar."</font>";
			}else if($turn==1){
				$turn=2;
				$output .= "<font face=\"courier new\" size=\"3\">".$curchar."</font>";
			}else if($turn==2){
				$turn=3;
				$output .= "<font face=\"arial narrow\" size=\"2\">".$curchar."</font>";
			}else if($turn==3){
				$turn=0;
				$output .= "<font face=\"verdana\" size=\"3\">".$curchar."</font>";
			}
		}

		return $output;	
	}

}


?>