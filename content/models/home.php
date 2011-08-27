<?php
	
	class home_model extends model
	{
		function getProducts()
		{
			$this->db->execute("select * from products");
		}
	}

?>