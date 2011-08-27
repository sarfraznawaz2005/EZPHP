<?php
	
	class contact_us_model extends model
	{
		function insertContact()
		{
			$name = $this->input->post('name', true);	# true argument for xss attack security
			$msg = $this->input->post('msg');

			$res = $this->db->insert("contacts", "name = '$name', msg = '$msg'");
			return $res;
		}
	}

?>