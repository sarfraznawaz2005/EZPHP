<?php

	/*
		Example:
		
		$site = $this->load->cls('scraper', 'http://www.anysite.com');
		$excss = $site->getExternalCSS();
		$incss = $site->getInternalCSS();
		$ids = $site->getIds();
		$classes = $site->getClasses();
		$spans = $site->getSpans(); 
		
		print '<pre>';
		print_r($excss);
		print_r($incss);
		print_r($ids);
		print_r($classes);
		print_r($spans);		
	
	*/

	class scraper
	{
		private $url = '';
	 
		public function __construct($url)
		{
			$this->url = file_get_contents("$url");
		}
	 
		public function getInternalCSS()
		{
			$tmp = preg_match_all('/(style=")(.*?)(")/is', $this->url, $patterns);
			$result = array();
			array_push($result, $patterns[2]);
			array_push($result, count($patterns[2]));
			return $result;
		}
	 
		public function getExternalCSS()
		{
			$tmp = preg_match_all('/(href=")(\w.*\.css)"/i', $this->url, $patterns);
			$result = array();
			array_push($result, $patterns[2]);
			array_push($result, count($patterns[2]));
			return $result;
		}
	 
		public function getIds()
		{
			$tmp = preg_match_all('/(id="(\w*)")/is', $this->url, $patterns);
			$result = array();
			array_push($result, $patterns[2]);
			array_push($result, count($patterns[2]));
			return $result;
		}
	 
		public function getClasses()
		{
			$tmp = preg_match_all('/(class="(\w*)")/is', $this->url, $patterns);
			$result = array();
			array_push($result, $patterns[2]);
			array_push($result, count($patterns[2]));
			return $result;
		}
	 
		public function getSpans(){
			$tmp = preg_match_all('/(<span>)(.*)(<\/span>)/', $this->url, $patterns);
			$result = array();
			array_push($result, $patterns[2]);
			array_push($result, count($patterns[2]));
			return $result;
		}
	 
	}
?>