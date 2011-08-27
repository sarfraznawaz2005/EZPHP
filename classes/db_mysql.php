<?php
	/*
		-----------------------------------------------------------
		Purpose: MySQL Wrapper
		-----------------------------------------------------------
	
		-----------------------------------------------------------
		Author/Source: EZPHP
		-----------------------------------------------------------
		
		-----------------------------------------------------------
		Example Use:
		-----------------------------------------------------------

		In order to use the example here, you need to create a table,
		just execute the following SQL lines using your MySQL client:
		-------------------------------------------------------------
	
		################################
		CREATE TABLE `quickdbtest` (
		  `catid` int(11) NOT NULL auto_increment,
		  `catname` varchar(255) default NULL,
		  PRIMARY KEY  (`catid`)
		);
		insert  into `quickdbtest`(`catid`,`catname`) values (1,'Big'),(2,'Small'),(3,'Average'),(4,'Light'),(5,'Huge');	
		################################


		// This file uses most of the functions of QuickDB MySQL wrapper.
		// It is very easy to use these funtions (methods), see below examples.
	

		// Argumentd are: host, user, password, database, persistent connection, show errors on screen
		$db = new db_mysql("localhost", "root", "", "test", false, true);
	
	
		## execute Method/Function
		// it can run any query whether select, insert, update or delete like mysql_query function
		
		// returns:
		// 1) resource identifier for "Select query" that can later be used with mysql_fetch_array or mysql_fetch_object
		// 2) number of rows affected for "insert, update or delete" queries		
	
																												// Usage Of:
																												// --------------------------
	
		$result = $db->execute("select * from quickdbtest order by catname");									// $db->execute
		// Or
		// 		$result = $db->select("quickdbtest");															// $db->select
					
		while ($row = mysql_fetch_array($result))
		{
			print $row["catname"] . "<br />";
		}
	
		print "<br />Number of rows selected from previous query : " . $db->count_select();						// $db->count_select()
	
		$affected = $db->execute("insert into quickdbtest set catname = 'New Category'");
		// Or
		//		$affected = $db->insert("quickdbtest", "catname = 'New Category'");								// $db->insert
	
		print "<br />Number of rows affected are: $affected";
		// Or
		print "<br />Number of rows affected using class method: " . $db->count_affected();						// $db->count_affected()
		$db->success_msg("Record was added successfully !!");
	
		$affected = $db->update("quickdbtest", "catname = 'New Category 2'", "catname = 'New Category'");		// $db->update
		print "<br />Number of rows affected are: $affected";
		
		$db->delete("quickdbtest", "catid = " . $db->last_insert_id());											// $db->last_insert_id(), $db->delete
		print "<br />Number of rows affected using class method: " . $db->count_affected();
		
		print "<br />Total records in table are: " . $db->count_all("quickdbtest");								// $db->count_all
		
		print "<br />Counting records using 'count_rows' : " . $db->count_rows("select * from quickdbtest");	// $db->count_rows
		
		// $db->insert_update will update if row exists, or insert data if it doesn't with catid=5
		$affected = $db->insert_update("quickdbtest", "catname='Test Category'", "catid=5");					// $db->insert_update
		print "<br />Number of rows affected are: $affected<br />";
		
		$result = $db->select_limited("quickdbtest", 4, 1);														// $db->select_limited
	
		while ($row = mysql_fetch_array($result))
		{
			print $row["catname"] . "<br />";
		}
	
		
		if ($db->has_rows("quickdbtest"))																		// $db->has_rows
		{
			print "This table is not empty, it has rows in it !!";
		}
		else
		{
			print "Oops, the table is empty !!!";
		}
	
	
		if ($db->row_exists("select * from quickdbtest where catid = 5"))										// $db->row_exists
		{
			print "<br />Yes, row exists";
		}
		else
		{
			print "<br />No, row does not exist";
		}
	
		// fetch a single row from db
		$row = $db->fetch_row("select * from quickdbtest where catid = 1");										// $db->fetch_row
		print "<br />The fetched values are: " . $row->catid . " " . $row->catname ;
	
		// fetch a single row from db
		$catname = $db->fetch_value("quickdbtest", "catname", "catid = 3");										// $db->fetch_value
		print "<br />The fetched value is: " . $catname;
		
		print "<br /> The date today is: " . $db->get_date();													// $db->get_date()
		print "<br /> The current time is: " . $db->get_time();													// $db->get_time()
		
		// $db->last_query() gives the last run query, may be useful for debugging queries
		print "<br /> The last run query was: <strong>" . $db->last_query()  . "</strong><br /><br />";
	
		// using very useful $db->load_data()
		$db->select("quickdbtest");																				// $db->select
		$data = $db->load_array();																				// $db->load_array()
		print_r($data);
		/////////////////////////
		
		// using another very useful function that convert db table to a html table
		$db->get_html("select * from quickdbtest order by catname" , true, 'width = 50%, align="center"');		// $db->get_html
		
		print "<br /><strong>Database Tables</strong><br />";
		$db->list_tables();																						// $db->list_tables()
	
		print "<br /><strong>Table Information</strong><br />";
		$db->table_info("quickdbtest");																			// $db->table_info
		
		
		$db->success_msg("Wow, this is cool class !!");															// $db->success_msg
		
		// just a wrong table to get that error
		$db->count_rows("select * from NO_TABLE");
		$db->display_errors();																					// $db->display_errros()
		
		$db->failure_msg("Oops, i must have received the error !!");											// $db->failure_msg
		
		$db->alert_msg("This is general alert message !!");														// $db->alert_msg
	
	*/

	class db_mysql
	{
		private $con 			= null;		// for db connection
		private $result 		= null;		// for mysql result resource id
		private $row 			= null;		// for fetched row
		private $rows 			= null;		// for number of rows fetched
		private $affected 		= null;		// for number of rows affected
		private $insert_id 		= null;		// for last inserted id
		private $query 			= null;		// for the last run query
		private $show_errors 	= null;		// for knowing whether to display errors
		private $emsg 			= null;		// for mysql error description
		private $eno 			= null;		// for mysql error number
		
		
		// Intialize the class with connection to db
		public function __construct($host, $user, $password, $db, $persistent = false, $show_errors = true)
		{
			if ($show_errors == true)
			{
				$this->show_errors = true;
			}
			
			if ($persistent == true)
			{
				$this->con = mysql_pconnect($host, $user, $password);
			}
			else
			{
				$this->con = mysql_connect($host, $user, $password);
			}
			
			if ($this->con)
			{
				$result = mysql_select_db($db, $this->con) or die("Could Not Select The Database !!");

				// utf8 charset for multi-lingual support
				mysql_query("SET NAMES 'utf8'", $this->con);
				mysql_query("SET CHARACTER SET utf8", $this->con);
				mysql_query("SET character_set_results=utf8", $this->con);
				mb_language("uni");
				mb_internal_encoding("UTF-8");
				
				return $result;
			}
			else
			{
				die("Could Not Establish The Connection !!");
			}
		}
		
		// Close the connection to database
		public function __destruct()
		{
			$this->close();
		}

		// Close the connection to database
		public function close()
		{
			$result = @mysql_close($this->con);
			return $result;
		}
	
		// stores mysql errors
		private function setError($msg, $no)
		{
			$this->emsg = $msg;
			$this->eno = $no;
			
			if ($this->show_errors == true)
			{
				print '	<br /><br /><div style="background:#f6f6f6; padding:5px; font-size:13px; font-family:verdana; border:1px solid #cccccc;">
						<span style="color:#ff0000;">MySQL Error Number</span> : ' . $no . '<br />
						<span style="color:#ff0000;">MySQL Error Message</span> : ' . $msg . '</div><br />';
			}
		}
		
	
		#################################################
		#				General Functions				#
		#################################################
	
		// Runs the SQL query (general execute query function)
		public function execute($command)
		{
			# Params:
			# 		$command = query command
			
			if (!$command)
			{
				exit("No Query Command Specified !!");
			}
			
			$this->query = $command;
			
			// For Operational query
			if 	(
				(stripos($command, "insert ") !== false) ||
				(stripos($command, "update ") !== false) ||
				(stripos($command, "delete ") !== false) ||
				(stripos($command, "replace ") !== false)
				)
			{
				$this->result = mysql_query($command) or $this->setError(mysql_error(), mysql_errno());

				if (stripos($command, "insert ") !== false)
				{
					if ($this->result)
					{
						$this->insert_id = intval(mysql_insert_id());
					}
				}

				if ($this->result)
				{
					$this->affected = intval(mysql_affected_rows());
					// return the number of rows affected
					return $this->affected;
				}
			}
			else
			{
				// For Selection query
				$this->result = mysql_query($command) or $this->setError(mysql_error(), mysql_errno());
				if ($this->result)
				{
					$this->rows = intval(mysql_num_rows($this->result));
					// return the query resource for later processing
					
					
					return $this->result;
				}
			}
		}	

		// Gets records from table
		public function select($table, $rows = "*", $condition = null, $order = null)
		{
			# Params:
			# 		$table = the name of the table
			#		$rows = rows to be selected
			# 		$condition = example: where id = 99
			#		$order = ordering field name

			if (!$table)
			{
				exit("No Table Specified !!");
			}
			
			if (!$rows)
			{
				$rows = '*';
			}
			
			$sql = "select $rows from $table";

			if($condition)
			{
				$sql .= ' where ' . $condition;
			}
			
			if($order)
			{
				$sql .= ' order by ' . $order;
			}

			$this->query = $sql;
			$this->result = mysql_query($sql) or $this->setError(mysql_error(), mysql_errno());

			if ($this->result)
			{
				$this->rows = intval(mysql_num_rows($this->result));
				// return the query resource for later processing

				return $this->result;
			}
		}	


		// Inserts records
		public function insert($table, $data)
		{
			# Params:
			# 		$table = the name of the table
			# 		$data = field/value pairs to be inserted
			
			if ($table)
			{
				if ($data)
				{
					$this->result = mysql_query("insert into $table set $data") or $this->setError(mysql_error(), mysql_errno());
					$this->query = "insert into $table set $data";

					if ($this->result)
					{
						$this->affected = intval(mysql_affected_rows());
						$this->insert_id = intval(mysql_insert_id());
						// return the number of rows affected
						return $this->affected;
					}
				}
				else
				{
					print "No Data Specified !!";
				}
			}
			else
			{
				print "No Table Specified !!";
			}
		}

		// Updates records
		public function update($table, $data, $condition)
		{
			# Params:
			# 		$table = the name of the table
			# 		$data = field/value pairs to be updated
			# 		$condition = example: where id = 99

			if ($table)
			{
				if ($data)
				{
					if ($condition)
					{
						$this->result = mysql_query("update $table set $data where $condition") or $this->setError(mysql_error(), mysql_errno());
						$this->query = "update $table set $data where $condition";

						if ($this->result)
						{
							$this->affected = intval(mysql_affected_rows());
							// return the number of rows affected
							return $this->affected;
						}
					}
					else
					{
						print "No Condition Specified !!";
					}
				}
				else
				{
					print "No Data Specified !!";
				}
			}
			else
			{
				print "No Table Specified !!";
			}
		}

		// Deletes records
		public function delete($table, $condition)
		{
			# Params:
			# 		$table = the name of the table
			# 		$condition = example: where id = 99

			if ($table)
			{
				if ($condition)
				{
					$this->result = mysql_query("delete from $table where $condition") or $this->setError(mysql_error(), mysql_errno());
					$this->query = "delete from $table where $condition";

					if ($this->result)
					{
						$this->affected = intval(mysql_affected_rows());
						// return the number of rows affected
						return $this->affected;
					}
				}
				else
				{
					print "No Condition Specified !!";
				}
			}
			else
			{
				print "No Table Specified !!";
			}
		}

		// returns table data in array
		public function load_array()
		{
			$i = 0;
			$data = array();
	
			while ($result = mysql_fetch_assoc($this->result))
			{
				$data[$i] = $result;
				$i++;
			}
			
			mysql_free_result($this->result);
			
			return $data;	
		}


		// print a complete html table from the specified db table
		public function get_html($command, $display_field_headers = true, $table_attribs = 'border="0" cellpadding="5" cellspacing="0" style="padding-bottom:5px; border:1px solid #cccccc; font-size:13px; font-family:verdana;"')
		{
			if (!$command)
			{
				exit("No Query Command Specified !!");
			}

			$this->query = $command;
			$this->result = mysql_query($command) or $this->setError(mysql_error(), mysql_errno());
			
			if ($this->result)
			{
				$this->rows = intval(mysql_num_rows($this->result));
				
				
				
				$num_fields = mysql_num_fields($this->result);

				print '<br /><br /><div>
						<table ' . $table_attribs . '>'
						. "\n" . '<tr>';

				if ($display_field_headers == true)
				{
					// printing table headers
					for($i = 0; $i < $num_fields; $i++)
					{
						$field = mysql_fetch_field($this->result);
						print "<td bgcolor='#f6f6f6' style=' border:1px solid #cccccc; padding:5px;'><strong style='color:#666666;'>" . ucwords($field->name) . "</strong></td>\n";
					}
					print "</tr>\n";
				}
				
				// printing table rows
				while($row = mysql_fetch_row($this->result))
				{
					print "<tr>";
				
					foreach($row as $td)
					{
						print "<td bgcolor='#f6f6f6'>$td</td>\n";
					}
				
					print "</tr>\n";
				}
				print "</table></div><br /><br />";
			}
		}
		
		
		public function last_insert_id()
		{
			return $this->insert_id ? $this->insert_id : false;
		}
		
		// Counts all records from a table
		public function count_all($table)
		{
			if (!$table)
			{
				exit("No Table Specified !!");
			}
			
			$this->result = mysql_query("select count(*) as total from $table") or $this->setError(mysql_error(), mysql_errno());
			$this->query = "select count(*) as total from $table";

			if ($this->result)
			{
				$this->row = mysql_fetch_array($this->result);
				return intval($this->row["total"]);
			}
		}
		
		// Counts records based on specified criteria
		public function count_rows($command)
		{
			# Params:
			# 		$command = query command

			if (!$command)
			{
				exit("No Query Command Specified !!");
			}
		
			$this->query = $command;
			$this->result = mysql_query($command) or $this->setError(mysql_error(), mysql_errno());

			if ($this->result)
			{
				return intval(mysql_num_rows($this->result));
			}
		}

		// Updates a row if it exists or adds if it doesn't already exist.
		public function insert_update($table, $data, $condition)
		{
			# Params:
			# 		$table = the name of the table
			# 		$data = field/value pairs to be added/updated
			# 		$condition = example: where id = 99

			if ($table)
			{
				if ($data)
				{
					if ($condition)
					{
						if ($this->row_exists("select * from $table where $condition"))
						{
							$this->result = mysql_query("update $table set $data where $condition") or $this->setError(mysql_error(), mysql_errno());
							$this->query = "update $table set $data where $condition";

							if ($this->result)
							{
								$this->affected = intval(mysql_affected_rows());
								// return the number of rows affected
								return $this->affected;
							}
						}
						else
						{
							$this->result = mysql_query("insert into $table set $data") or $this->setError(mysql_error(), mysql_errno());
							$this->query = "insert into $table set $data";

							if ($this->result)
							{
								$this->insert_id = intval(mysql_insert_id());
								$this->affected = intval(mysql_affected_rows());
								// return the number of rows affected
								return $this->affected;
							}
						}
					}
					else
					{
						print "No Condition Specified !!";
					}
				}
				else
				{
					print "No Data Specified !!";
				}
			}
			else
			{
				print "No Table Specified !!";
			}
		}


		// Runs the sql query with claus "limit x, x"
		public function select_limited($table, $start, $return_count, $condition = null, $order = null)
		{
			# Params:
			# 		$start = starting row for limit clause
			# 		$return_count = number of records to fetch
			# 		$condition = example: where id = 99
			# 		$order = ordering field name
			
			if ($table && $start >= 0 && $return_count)
			{
				if ($condition)
				{
					if ($order)
					{
						$this->result = mysql_query("select * from $table where $condition order by $order limit $start, $return_count") or $this->setError(mysql_error(), mysql_errno());
						$this->query = "select * from $table where $condition order by $order limit $start, $return_count";
					}
					else
					{
						$this->result = mysql_query("select * from $table where $condition limit $start, $return_count") or $this->setError(mysql_error(), mysql_errno());
						$this->query = "select * from $table where $condition limit $start, $return_count";
					}
				}
				else
				{
					if ($order)
					{
						$this->result = mysql_query("select * from $table order by $order limit $start, $return_count") or $this->setError(mysql_error(), mysql_errno());
						$this->query = "select * from $table order by $order limit $start, $return_count";
					}
					else
					{
						$this->result = mysql_query("select * from $table limit $start, $return_count") or $this->setError(mysql_error(), mysql_errno());
						$this->query = "select * from $table limit $start, $return_count";
					}
				}

				if ($this->result)
				{
					$this->rows = intval(mysql_num_rows($this->result));
					// return the query resource for later processing
					
					
					return $this->result;
				}
			}
			else
			{
				print "Parameter Missing !!";
			}
		}	

		
		#################################################
		#				Utility Functions				#
		#################################################

		// Counts rows from last Select query
		public function count_select()
		{
			if ($this->rows)
			{
				return $this->rows;
			}
		}

		// Gets the number of affected rows after Operational query has executed
		public function count_affected()
		{
			if ($this->affected)
			{
				return $this->affected;
			}
		}

		// Checks whether a table has records		
		public function has_rows($table)
		{
			$rows = $this->count_all($table);
			
			if ($rows)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		// Checks whether or not a row exists with specified criteria
		public function row_exists($command)
		{
			# Params:
			# 		$command = query command

			if (!$command)
			{
				exit("No Query Command Specified !!");
			}
		
			$this->query = $command;
			$this->result = mysql_query($command) or $this->setError(mysql_error(), mysql_errno());

			if ($this->result)
			{
				if (mysql_num_rows($this->result))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}

		// Returns single fetched row
		public function fetch_row($command)
		{

			if (!$command)
			{
				exit("No Query Command Specified !!");
			}

			$this->query = $command;
			$this->result = mysql_query($command) or $this->setError(mysql_error(), mysql_errno());

			if ($this->result)
			{
				$this->rows = intval(mysql_num_rows($this->result));
				$this->row = mysql_fetch_object($this->result);
				
				
				return $this->row;
			}
		}
		
		
		// Returns single field value
		public function fetch_value($table, $field, $condition = null)
		{

			if (!$table || !$field)
			{
				exit("Arguments Missing !!");
			}

			$query = "select $field from $table";
			
			if ($condition != null)
			{
				$query = "select $field from $table where $condition";
			}
			
			$this->query = $query;
			$this->result = mysql_query($query) or $this->setError(mysql_error(), mysql_errno());

			if ($this->result)
			{
				$this->rows = intval(mysql_num_rows($this->result));
				$this->row = mysql_fetch_object($this->result);
				
				
				return $this->row->$field;
			}
		}
		
		
		// Returns the last run query, useful for debugging queries
		public function last_query($print_query = false)
		{
			if ($this->query)
			{
				return ($print_query == true) ? $this->alert_msg($this->query) : $this->query;
			}
		}
		
		// Gets today's date
		public function get_date($format = null)
		{
			# Params:
			#		$format = date format like Y-m-d
			
			if ($format)
			{
				$today = date($format);
			}
			else
			{
				$today = date("Y-m-d");
			}
			
			return $today;
		}
		
		// Gets currents time
		public function get_time($format = null)
		{
			# Params:
			#		$format = date format like H:i:s
			
			if ($format)
			{
				$time = date($format);
			}
			else
			{
				$time = date("H:i:s");
			}
			
			return $time;
		}

		// Adds slash to the string irrespective of the setting of getmagicquotesgpc
		public function escape($value)
		{
			if (get_magic_quotes_gpc())
			{
				$value = stripslashes($value);
			}

			if (!is_numeric($value))
			{
				$value = mysql_real_escape_string($value);
			}
			
			return trim($value);
		} 
		
		
		// This function can be used to discard any characters that can be used to manipulate the SQL queries or SQL injection

		/* EXAMPLE USE:
		
			if (is_valid($_REQUEST["username"]) === true && is_valid($_REQUEST["pass"]) === true)
			{
				//login now
			}
		*/
		
		public function is_valid($input)
		{
			$input = strtolower($input);
			
			if (str_word_count($input) > 1)
			{
				$loop = "true";
				$input = explode(" ",$input);
			}
			
			$bad_strings = array("'","--","select","union","insert","update","like","delete","1=1","or");
		
			if ($loop)
			{
				foreach($input as $value)
				{
					if (in_array($value, $bad_strings))
					{
					  return false;
					}
					else
					{
					  return true;
					}
				}
			}
			else
			{
				if (in_array($input, $bad_strings))
				{
				  return false;
				}
				else
				{
				  return true;
				}
			}
		}

	
		// lists tables of database
		public function list_tables()
		{
			$this->result = mysql_query("show tables");
			$this->query = "show tables";
			
			if ($this->result)
			{
				$tables = array();
				while($row = mysql_fetch_array($this->result))
				{
					$tables[] = $row[0];
				}
				
				foreach ($tables as $table)
				{
					print $table . "<br />";
				}
			}
		}


		// provides info about given table
		public function table_info($table)
		{
			if ($table)
			{
				$this->result = mysql_query("select * from $table");
				$this->query = "select * from $table";

				$fields = mysql_num_fields($this->result);
				$rows   = mysql_num_rows($this->result);
				$table = mysql_field_table($this->result, 0);

				print "	The '<strong>" . $table . "</strong>' table has <strong>" . $fields . "</strong> fields and <strong>" . $rows . "</strong>
						record(s) with following fields.\n<br /><ul>";

				for ($i=0; $i < $fields; $i++)
				{
					$type  = mysql_field_type($this->result, $i);
					$name  = mysql_field_name($this->result, $i);
					$len   = mysql_field_len($this->result, $i);
					$flags = mysql_field_flags($this->result, $i);
					
					print "<strong><li>" . $type . " " . $name . " " . $len . " " . $flags . "</strong></li>\n";
				}
				print "</ul>";
				
			}
			else
			{
				print "The table not specified !!";
			}
		}


		// displays any mysql errors generated
		public function display_errors()
		{
			if ($this->show_errors == false)
			{
				if ($this->emsg)
				{
					print '	<br /><br /><div style="background:#f6f6f6; padding:5px; font-size:13px; font-family:verdana; border:1px solid #cccccc;">
							<span style="color:#ff0000;">MySQL Error Number</span> : ' . $this->eno . '<br />
							<span style="color:#ff0000;">MySQL Error Message</span> : ' . $this->emsg . '</div>';
				}
				else
				{
					print '	<br /><br /><div style="background:#f6f6f6; padding:5px; font-size:13px; font-family:verdana; border:1px solid #cccccc;">
							<strong>No Erros Found !!</strong>
							</div>';
				}
			}
		}

		// to display success message
		public function success_msg($msg)
		{
			print '<br /><div align="center" style="background:#EEFDD7; padding:5px; font-size:13px; font-family:tahoma, verdana; border:1px solid #8DD607; margin:5px 0px 5px 0px;">
					<strong>' . $msg . '
					</strong></div><br />';
		}
	
		// to display failure message
		public function failure_msg($msg)
		{
			print '<br /><div align="center" style="background:#FFF2F2; padding:5px; font-size:13px; font-family:tahoma, verdana; border:1px solid #FF8080; margin:5px 0px 5px 0px;">
					<strong>' . $msg . '
					</strong></div><br />';
		}

		// to display general alert message
		public function alert_msg($msg)
		{
			print '<br /><div align="center" style="background:#FFFFCC; padding:5px; font-size:13px; font-family:tahoma, verdana; border:1px solid #CCCC33; margin:5px 0px 5px 0px;">
					<strong>' . $msg . '
					</strong></div><br />';
		}

	////////////////////////////////////////////////////////
	}

?>