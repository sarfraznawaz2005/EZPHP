<table align="center" width="97%" cellpadding="5" cellspacing="0" bgcolor="#f6f6f6" style="border:1px solid #cccccc;">
	<caption align="center" style="font-weight:bold; font-size:18px; color:#006600;">Our Products</caption>
	
	<tr>
		<td style="font-weight:bold; color:#0066FF;">Product Name</td>
		<td style="font-weight:bold; color:#0066FF;">Price</td>
		<td style="font-weight:bold; color:#0066FF;">Stock</td>
		<td style="font-weight:bold; color:#0066FF;">Active</td>
	</tr>

	<?php
	
		# load the pagination class
		$page = $this->load->cls('paging');

		//paging start
		$num = $this->db->count_select();
		$total_records=$num;
		$record_per_page=3;
		$scroll=10;
		$total_pages = ceil($num/$record_per_page);								
	
		$page->set_page_data($_SERVER['PHP_SELF'],$total_records,$record_per_page,$scroll,true,true,false);
		$page->set_qry_string("page=".@$_GET["page"]);
		$page->set_link_parameter("class = 'site_message'");
		$result=mysql_query($page->get_limit_query($this->db->last_query()));

		if ((@$_GET["page"] <= -1) || (!is_numeric(@$_GET["page"])))
		{
			$pagenum = 1;
			$min_records = ceil(($pagenum*$record_per_page) - ($record_per_page - 1));
			$max_records = ceil(($pagenum*$record_per_page));
			if ($num < $max_records) {$max_records = $num;}
		}
		else
		{
			$pagenum = @$_GET["page"] + 1;						
			$min_records = ceil(($pagenum*$record_per_page) - ($record_per_page - 1));
			$max_records = ceil(($pagenum*$record_per_page));
			if ($num < $max_records) {$max_records = $num;}
		}
		//paging end				

		$counter = 0;

		while($row = mysql_fetch_array($result))
		{
			$counter++;
			$color = "#cccccc";
			
			if (($counter % 2) == 0)
			{
				$color = "#f6f6f6";
			}
	?>
	<tr>
		<td bgcolor="<?php echo $color; ?>"><?php echo $row['product_name']; ?></td>
		<td bgcolor="<?php echo $color; ?>"><?php echo $row['price']; ?></td>
		<td bgcolor="<?php echo $color; ?>"><?php echo $row['stock']; ?></td>
		<td bgcolor="<?php echo $color; ?>"><?php print ($row['discontinued'] == '1') ? "No" : "Yes"; ?></td>
	</tr>
	<?php } ?>
	
	<tr>
		<td colspan="4" height="15"></td>
	</tr>
	
	<tr>
		<td colspan="4">
			<div style="float:left;">
				<?php echo "\nShowing results <strong>" . $min_records . "</strong> to <strong class = 'site_message'>" . $max_records . "</strong> of <strong class = 'site_message'>" . $num . "</strong>\n";?>
			</div>
			<div style="float:right;">
				<?php print $page->get_page_nav(); ?>
			</div>
		</td>
	</tr>

<!--
	<tr>
		<td colspan="4" height="15"></td>
	</tr>
	<tr>
		<td colspan="4">
			<?php
					# resize the image and then show it
					$image = $this->load->cls('image', $ez_full_path . "images/girl.jpg");
					
					$img = $image->resize(100, 100);
			?>
			
			<div align="center">
				<img src="<?php echo $ez_template_path . 'images/' . $img;?>" />
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="4" height="15"></td>
	</tr>
-->
</table>