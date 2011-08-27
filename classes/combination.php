<?php

	/*
		-----------------------------------------------------------
		Purpose: Generates combinations of provided arrays
		-----------------------------------------------------------
	
		-----------------------------------------------------------
		Author/Source: http://tagarga.com/blok/
		-----------------------------------------------------------
		
		-----------------------------------------------------------
		Example Use:
		-----------------------------------------------------------
		
		$fruits = array('apple', 'banana', 'orange', 'pear');
		$combination = $this->load->cls('combination', array($fruits, 3));
		
		echo '<pre>';
		foreach($combination as $k => $v)
		{
			echo $k + 1, ' ', implode(',', $v), " \n";
		}

		-----------------------------------------------------------
		Sample Output:
		-----------------------------------------------------------

		1 apple,banana,orange
		2 apple,banana,pear
		3 apple,orange,pear
		4 banana,orange,pear		

	*/

	class combination implements Iterator
	{
		protected $c = null;
		protected $s = null;
		protected $n = 0;
		protected $k = 0;
		protected $pos = 0;
	
		function __construct($s, $k) {
			if(is_array($s)) {
				$this->s = array_values($s);
				$this->n = count($this->s);
			} else {
				$this->s = (string) $s;
				$this->n = strlen($this->s);
			}
			$this->k = $k;
			$this->rewind();
		}
		function key() {
			return $this->pos;
		}
		function current() {
			$r = array();
			for($i = 0; $i < $this->k; $i++)
				$r[] = $this->s[$this->c[$i]];
			return is_array($this->s) ? $r : implode('', $r);
		}
		function next() {
			if($this->_next())
				$this->pos++;
			else
				$this->pos = -1;
		}
		function rewind() {
			$this->c = range(0, $this->k);
			$this->pos = 0;
		}
		function valid() {
			return $this->pos >= 0;
		}
		//
		protected function _next() {
			$i = $this->k - 1;
			while ($i >= 0 && $this->c[$i] == $this->n - $this->k + $i)
				$i--;
			if($i < 0)
				return false;
			$this->c[$i]++;
			while($i++ < $this->k - 1)
				$this->c[$i] = $this->c[$i - 1] + 1;
			return true;
		}
	}



?>