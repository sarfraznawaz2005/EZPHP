<?php
	class home_controller extends controller
	{
		function index()
		{
			# let's find out the time this script runs
			$timer = $this->load->cls('timer');
			$timer->start();
			
			# set view variables
			$this->view->heading = 'Welcome To EZPHP';
			$this->view->content = 'EZPHP is an easy-to-use framework allowing you to develop applications much faster than doing it from scratch by providing you with ready-made libraries and utility classes. It comes with a mechanism to develop your applications in OOP (Object-Oriented-Programming) way even if you are not familiar with OOP. EZPHP aims to be light-weight, fast and easy to use. EZPHP is therefore targeted to those who want to get the framework complexities out of the their way and start writing the application fast from day one.<br /><br /><br /><br />The following table brings some records from database:';
			
			# some content coming from db
			$this->load->model('home');
			$this->home->getProducts();

			$timer->stop();
			$this->view->time_taken = $timer->result();
			
			# load the index view
			
			//$this->view->print_data();
			
			$this->view->render('index');
			//$this->view->render('index2');
		}
		
		// This function demonstrates how to read url segments
		function read_args($product_type, $category, $id)
		{
			# url example for this
			# http://yoursite.com/index.php?route=home/read_args/shirts/latest/100
			
			echo '<strong>Here is how to read the url arguments:</strong><br /><br />';

			// Direct Method
			echo "<strong>Direct Method:</strong><br /><br />";
			echo $product_type . "<br />";
			echo $category . "<br />";
			echo $id . "<br /><br />";

			// Indirect Methods
			echo "<strong>Indirect Methods:</strong><br />";
			echo "<pre>";
			
			echo '<br />--Method One--<br />';
			
			// Method One
			echo $this->uri->parts[2] . '<br />';
			echo $this->uri->parts[3] . '<br />';
			echo $this->uri->parts[4] . '<br />';
			
			echo '<br />--Method Two--<br />';
			
			// Method Two
			echo $this->uri->part(2) . '<br />';
			echo $this->uri->part(3) . '<br />';
			echo $this->uri->part(4) . '<br />';

			echo '<br />--Method Three--<br />';
			
			// Method Three
			$args = ezphp::ez_get('arguments');
			echo $args[0] . '<br />';
			echo $args[1] . '<br />';
			echo $args[2] . '<br />';

			echo '<br />--Method Four--<br />';			
			
			// Method Four
			echo $this->shirts . '<br />';
			echo $this->latest . '<br />';
			echo $this->{100} . '<br />';
		}

		// this is an example of private function which can't be served from URL
		
		function private_test()
		{
			echo 'Can you run me??';
		}

	}

?>
