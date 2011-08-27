<?php

	class contact_us_controller extends controller
	{
		function index()
		{
			# let's find out the time this script runs
			$timer = $this->load->cls('timer');
			$timer->start();
		
			# set view variables
			$this->view->heading = 'Contact Us';
			$this->view->content = 'Please use the following form to contact us.';

			$timer->stop();
			$this->view->time_taken = $timer->result();
			
			# load the contact us view
			$this->view->render('contact_us');
		}
		
		function submit_contact()
		{
			$name = $this->input->post('name');
			$msg = $this->input->post('msg');
			$captcha = $this->input->post('captcha');

			# load the validation class
			$validator = $this->load->cls('validator');
			
			# make sure that form was filled
			if ($validator->validate($name) == true && $validator->validate($msg) == true)
			{
				# validate: make sure that only alphabetic chars are entered
				if ($validator->validate($name, 'alnum') == false)
				{
					$vmsg = 'Name must only contain alphabetic characters !!<br />';
				}
				
				if ($validator->validate($msg, 'alnum') == false)
				{
					$vmsg .= 'Message must only contain alphabetic characters !!<br />';
				}
				
				$this->session->data['validate_msg'] = @$vmsg;
				# validation code ends here
				
				# make sure that captcha code entered was correct
				
				if ($captcha != $this->session->data['captcha'])
				{
					$this->uri->redirect('contact_us', '&msg=captcha_error');
					return false;
				}
				
				# insert the contact info into the db
				$this->load->model('contact_us');
				$return_value = $this->contact_us->insertContact();
				
				if ($return_value)
				{
					# load mail libray for sending the email
					/*
					$mail = $this->load->cls('mail');
					
					$mail->setTo('user@user.com');
					$mail->setFrom('user@user.com');
					$mail->setSender($name);
					$mail->setSubject('Contact Form Submission');
					$mail->setText(strip_tags(html_entity_decode($msg)));
					$mail->send();
					*/
					
					# unset the error message sessoin
					unset ($this->session->data['validate_msg']);
					
					# now redirect to the same page again
					$this->uri->redirect('contact_us', '&msg=1');
					return true;
				}
			}
			else
			{
				$vmsg = 'Make sure that you fill in all the required fields !!';
				$this->session->data['validate_msg'] = $vmsg;
				
				$this->uri->redirect('contact_us');
				return false;
			}
		}

		# create the captcha
		function captcha()
		{
			$captcha = $this->load->cls('captcha');
			$this->session->data['captcha'] = $captcha->getCode();
			$captcha->showImage();
		}
		
	}
	
?>
