<?php if (isset($_GET['msg'])) { ?>
<br /><br />
<div align="center" style="color:#FF0000; font-weight:bold;">
<?php
		switch ($_GET['msg'])
		{
			case '1':
				print 'Contact info was added successfully, Thanks !!';
				break;
			case 'captcha_error':
				print 'The verification code entered is not correct, please try again !!';
				break;
		}
 ?>
</div>
<br />
<?php } ?>

<?php

	if (!empty($this->session->data['validate_msg']))
	{
	?>
	<div align="center" style="color:#FF0000; font-weight:bold;">
	<?php
		print $this->session->data['validate_msg'];
		
		# now destroy the validate msg session
		unset($this->session->data['validate_msg']);
	?>
	<div style="clear:both;">&nbsp;</div>
	</div>
<?php } ?>

<form name="frmContact" method="post" action="index.php?route=contact_us/submit_contact" enctype="multipart/form-data">
<table align="center" width="40%" cellpadding="5" cellspacing="0" bgcolor="#f6f6f6" style="border:1px solid #cccccc;">
	<tr>
		<td style="font-weight:bold; color:#0066FF;">Name</td>
		<td style="font-weight:bold; color:#0066FF;">
			<input type="text" name="name" style="width:200px;" />
		</td>
	</tr>
	<tr>
		<td style="font-weight:bold; color:#0066FF;">Message</td>
		<td style="font-weight:bold; color:#0066FF;">
			<textarea name="msg" style="width:300px; height:100px;"></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="right">
		  Are You Human?<br />
		  <input type="text" name="captcha" value="<?php echo @$captcha; ?>" />
		  <br />
		  <?php if (@$error_captcha) { ?>
		  <span class="error"><?php echo $error_captcha; ?></span>
		  <?php } ?>
		  <img src="index.php?route=contact_us/captcha" /></div>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="15"></td>
	</tr>
	<tr>
		<td style="font-weight:bold; color:#0066FF;">&nbsp;</td>
		<td style="font-weight:bold; color:#0066FF;" align="right">
			<input type="submit" name="send" value="Send Message" />
		</td>
	</tr>
</table>
</form>
