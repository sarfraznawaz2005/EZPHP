<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<link rel="stylesheet" href="<?php echo $ez_template_path; ?>css/style.css" type="text/css" />
</head>
<body>

	<div id="container">
		<?php include "includes/banner.php";?>
	
		<div id="outer">
			<div id="inner">
				<?php include "includes/left.php";?>
			<div id="content">
				<h2><?php echo $heading; ?></h2>
				<br /><br />
				<p><?php echo $content; ?></p>
				<br /><br />
				
				<?php 
				
				echo $ez_layout_content; ?>
				
				<div style="clear:both;">&nbsp;</div>
				
			</div><!-- end content -->
			</div><!-- end inner -->
		</div><!-- end outer -->
		<?php include "includes/footer.php";?>
	</div><!-- end container -->
</body>
</html>
