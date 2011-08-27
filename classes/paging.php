<?php

     class paging
     {
		var $total_records=1;   ///Total Records returned by sql query
		var $records_per_page=10;    ///how many records would be displayed at a time
		var $page_name=""; ///page name on which the class is called
		var $start=0; 
		var $page=0;
		var $total_page=0;
		var $current_page;
		var $remain_page;
		var $show_prev_next=true;
		var $show_scroll_prev_next=false;
		var $show_first_last=false;
		var $show_disabled_links=true;
		var $scroll_page=0;
		var $qry_str="";
		var $link_para="";
		var $jsHandler="";
		
		/* returns boolean value if it is last page or not*/	
		function is_last_page()
		{
			return $this->page>=$this->total_page-1?true:false;
		}
		/* param : Void
		 returns boolean value if it is first page or not*/	
		function is_first_page()
		{
			return $this->page==0?true:false;
		}
		
		/**
		 * Input : Void
		 * Return: The current page 
		 */
		function current_page()
		{
			return $this->page+1;
		}
		
		/**
		 * Return the total pages.
		 */ 
		
		function total_page()
		{
			return $this->total_page==0?1:$this->total_page;
		}
		
		//@param : $show = if you want to show desabled links on navigation links.
		//
		function show_disabled_links($show=TRUE)	
		{
			$this->show_disabled_links=$show;
		}
		//@param : $link_para = if you want to pass any parameter to link
		//
		function set_link_parameter($link_para)
		{
			$this->link_para=$link_para;
		}
		
		function set_page_name($page_name)
		{
			$this->page_name=$page_name;
		}
		
		/**
		 * Function is used to set the javascript handler function
		 * @param $jsHandler: String Javascript function name
		 * @return  void; 
		 * @uses Function will use javascript handler function for pagging. It will pass the two parameters to 
		 * javascript function. First is page name and the second one is query string.
		 */ 
		function setJSHandler($jsHandler)
		{
			$this->jsHandler = $jsHandler;
		}
		
		//@param : str= query string you want to pass to links.
		function set_qry_string($str="",$usePost = true)
		{
			$this->qry_str=preg_replace("/&?page=\d*/","",$str);
			if($usePost)
			{
				if (isset($_POST))
				{
					foreach ($_POST as $key => $value)
						if(strpos($this->qry_str,"$key=")===false)
							$this->qry_str.="&$key=$value";
				}

				if (isset($_GET))
				{
					foreach ($_GET as $key => $value)
						if(strpos($this->qry_str,"$key=")===false)
							$this->qry_str.="&$key=$value";
				}
			}
			
			$this->qry_str=preg_replace("/&?page=\d*/","",$this->qry_str);
		}
		function set_scroll_page($scroll_num=0)
		{
			if($scroll_num!=0)
				$this->scroll_page=$scroll_num;
			else
				$this->scroll_page=$this->total_records;
		}
		function set_total_records($total_records)
		{
			if($total_records<0)
			  $total_records=0;
			$this->total_records=$total_records;
		}
		function set_records_per_page($records_per_page)
		{
			 if($records_per_page<=0)
			      $records_per_page=$this->total_records;
			 $this->records_per_page=$records_per_page;
		}
		/* @params
		* 	$page_name = Page name on which class is integrated. i.e. $_SERVER['PHP_SELF']
		*  	$total_records=Total records returnd by sql query.
		* 	$records_per_page=How many projects would be displayed at a time 
		*		$scroll_num= How many pages scrolled if we click on scroll page link. 
		* 				i.e if we want to scroll 6 pages at a time then pass argument 6.
		* 	$show_prev_next= boolean(true/false) to show prev Next Link
		* 	$show_scroll_prev_next= boolean(true/false) to show scrolled prev Next Link
		* 	$show_first_last= boolean(true/false) to show first last Link to move first and last page.
		*/
		
		function set_page_data($page_name,$total_records,$records_per_page=1,$scroll_num=0,$show_prev_next=true,$show_scroll_prev_next=false,$show_first_last=false)
		{
			$this->set_total_records($total_records);
			$this->set_records_per_page($records_per_page);
			$this->set_scroll_page($scroll_num);
			$this->set_page_name($page_name);
			$this->show_prev_next=$show_prev_next;
			$this->show_scroll_prev_next=$show_scroll_prev_next;
			$this->show_first_last=$show_first_last;
		}
		/* @params
		*  $user_link= if you want to display your link i.e if you want to user '>>' instead of [first] link then use
		 Page::get_first_page_nav(">>") OR for image
		 Page::get_first_page_nav("<img src='' alt='first'>") 
		 $link_para: link parameters i.e if you want ot use another parameters such as class.
		 Page::get_first_page_nav(">>","class=myStyleSheetClass")
		*/	   
		function get_first_page_nav($user_link="",$link_para="",$returnHtml=false)
		{
			if($this->total_page<=1)return;
			if(trim($user_link)=="") $user_link="[First]&nbsp;";
			
			if(empty($link_para)) $link_para = $this->link_para;
			
			$html = "";
			if(!$this->is_first_page()&& $this->show_first_last)
			{
				if(empty($this->jsHandler))
			    	$html = ' <a href="'.$this->page_name.'?page=0'.$this->qry_str.'" '.$link_para.'>'.$user_link.'</a> ';
			    else 
			    	$html = ' <a href="javascript:void(0);" onclick="'.$this->jsHandler.'(\''.$this->page_name.'\',\'page=0'.$this->qry_str.'\')" '.$link_para.'>'.$user_link.'</a> ';
			}
			elseif($this->show_first_last && $this->show_disabled_links)
			    $html = $user_link;
			
			if($returnHtml) return $html;
			
			echo $html;
			
		}
		
		function get_last_page_nav($user_link="",$link_para="",$returnHtml=false)
		{
			if($this->total_page<=1) return;
			if(trim($user_link)=="") $user_link="&nbsp;[Last]";

			if(empty($link_para)) $link_para = $this->link_para;
			
			$html = "";

			if(!$this->is_last_page()&& $this->show_first_last)
			{
				if(empty($this->jsHandler))
				    $html = ' <a href="'.$this->page_name.'?page='.($this->total_page-1).$this->qry_str.'" '.$link_para.'>'.$user_link.'</a> ';
			    else 
			    	$html = ' <a href="javascript:void(0);" onclick="'.$this->jsHandler.'(\''.$this->page_name.'\',\'page='.($this->total_page-1).$this->qry_str.'\')" '.$link_para.'>'.$user_link.'</a> ';
			}
			elseif($this->show_first_last && $this->show_disabled_links)
			    $html =  $user_link;

			if($returnHtml) return $html;
			
			echo $html;
		}
		function get_next_page_nav($user_link="",$link_para="",$returnHtml=false)
		{
			if($this->total_page<=1) return;
			if(trim($user_link)=="") $user_link=" Next &raquo;";

			if(empty($link_para)) $link_para = $this->link_para;

			$html = "";
			if(!$this->is_last_page()&& $this->show_prev_next)
			{
				if(empty($this->jsHandler))
				    $html = ' <a href="'.$this->page_name.'?page='.($this->page+1).$this->qry_str.'" '.$link_para.'>'.$user_link.'</a> ';
			    else 
			    	$html = ' <a href="javascript:void(0);" onclick="'.$this->jsHandler.'(\''.$this->page_name.'\',\'page='.($this->page+1).$this->qry_str.'\')" '.$link_para.'>'.$user_link.'</a> ';
			}
			elseif($this->show_prev_next && $this->show_disabled_links)
			    $html = $user_link;

			if($returnHtml) return $html;
			
			echo $html;
		}
		function get_prev_page_nav($user_link="",$link_para="",$returnHtml=false)
		{
			if($this->total_page<=1) return;
			if(trim($user_link)=="") $user_link="&laquo; Prev ";

			if(empty($link_para)) $link_para = $this->link_para;
			
			$html = "";
			if(!$this->is_first_page()&& $this->show_prev_next)
			{
				if(empty($this->jsHandler))
				    $html = ' <a href="'.$this->page_name.'?page='.($this->page-1).$this->qry_str.'" '.$link_para.'>'.$user_link.'</a> ';
			    else 
			    	$html = ' <a href="javascript:void(0);" onclick="'.$this->jsHandler.'(\''.$this->page_name.'\',\'page='.($this->page-1).$this->qry_str.'\')" '.$link_para.'>'.$user_link.'</a> ';
			}
			elseif($this->show_prev_next && $this->show_disabled_links)
			    $html = $user_link;

			if($returnHtml) return $html;
			
			echo $html;

		}
		function get_scroll_prev_page_nav($user_link="",$link_para="",$returnHtml=false)
		{
			
			if($this->scroll_page>=$this->total_page) return;
			if(trim($user_link)=="") $user_link="Prev[$this->scroll_page]";
			
			$html = "";
			if($this->page>$this->scroll_page &&$this->show_scroll_prev_next)
			{
				if(empty($this->jsHandler))
				    $html = ' <a href="'.$this->page_name.'?page='.($this->page-$this->scroll_page).$this->qry_str.'" '.$link_para.'>'.$user_link.'</a> ';
			    else 
			    	$html = ' <a href="javascript:void(0);" onclick="'.$this->jsHandler.'(\''.$this->page_name.'\',\'page='.($this->page-$this->scroll_page).$this->qry_str.'\')" '.$link_para.'>'.$user_link.'</a> ';
			}
			elseif($this->show_scroll_prev_next && $this->show_disabled_links)
			    $html = $user_link;

			if($returnHtml) return $html;
			
			echo $html;
		}
		function get_scroll_next_page_nav($user_link="",$link_para="",$returnHtml=false)
		{
			if($this->scroll_page>=$this->total_page)return;
			if(trim($user_link)=="") $user_link="Next[$this->scroll_page]";

			if(empty($link_para)) $link_para = $this->link_para;
			
			$html = "";
			if($this->total_page>$this->page+$this->scroll_page &&$this->show_scroll_prev_next)
			{
				if(empty($this->jsHandler))
				    $html = ' <a href="'.$this->page_name.'?page='.($this->page+$this->scroll_page).$this->qry_str.'" '.$link_para.'>'.$user_link.'</a> ';
			    else 
			    	$html = ' <a href="javascript:void(0);" onclick="'.$this->jsHandler.'(\''.$this->page_name.'\',\'page='.($this->page+$this->scroll_page).$this->qry_str.'\')" '.$link_para.'>'.$user_link.'</a> ';
			}
			elseif($this->show_scroll_prev_next && $this->show_disabled_links)
			    $html = $user_link;

			if($returnHtml) return $html;
			
			echo $html;

		}
		
		function get_number_page_nav($link_para="",$returnHtml=false)
		{
			if(empty($link_para)) $link_para = $this->link_para;
			
			$j=0;
			$scroll_page=$this->scroll_page;
			if($this->page>($scroll_page/2))
			  $j=$this->page-intval($scroll_page/2);
			if($j+$scroll_page>$this->total_page)
			  $j=$this->total_page-$scroll_page;
			
			if($j<0)
				$i=0;
			else
				$i=$j;
			
			$html = "";
			for(;$i<$j+$scroll_page && $i<$this->total_records;$i++)
			{
				if($i==$this->page)
					$html .= ($i+1);
				else
				{
					if(empty($this->jsHandler))
					    $html .= ' <a href="'.$this->page_name.'?page='.$i.$this->qry_str.'" '.$link_para.'>'.($i+1).'</a> ';
				    else 
				    	$html .= ' <a href="javascript:void(0);" onclick="'.$this->jsHandler.'(\''.$this->page_name.'\',\'page='.$i.$this->qry_str.'\')" '.$link_para.'>'.($i+1).'</a> ';
				}
			}

			if($returnHtml) return $html;
			
			echo $html;
		
		}
		
		function get_page_nav($link_para="",$returnHtml=false)
		{
			if($this->total_records<=0)	return false;
			if($this->total_records<=$this->records_per_page) return "";
			if(!empty($link_para)) $this->link_para = $link_para;
		
			$html = "";

			$this->calculate();
			$html.= $this->get_first_page_nav("",$this->link_para,$returnHtml);
			$html.= $this->get_scroll_prev_page_nav("",$this->link_para,$returnHtml);
			$html.= $this->get_prev_page_nav("",$this->link_para,$returnHtml);
			$html.= $this->get_number_page_nav($this->link_para,$returnHtml);
			$html.= $this->get_next_page_nav("",$this->link_para,$returnHtml);
			$html.= $this->get_scroll_next_page_nav("",$this->link_para,$returnHtml);
			$html.= $this->get_last_page_nav("",$this->link_para,$returnHtml);

			if($returnHtml) return $html;
			return;
		}

		function calculate()
		{
			$this->page=@$_GET['page'];
			if(!is_numeric($this->page))
			  $this->page=0;
			$this->start=$this->page*$this->records_per_page;
			$this->total_page=@intval($this->total_records/$this->records_per_page);
			if($this->total_records%$this->records_per_page!=0)
			  $this->total_page++;
		}

		function get_limit_query($qry="")
		{
			$this->calculate();
			return $qry." LIMIT $this->start,$this->records_per_page";
		}
     }
     
     
     /* Example 1
        $page=new Page();
        $page->set_total_records($total_records); //number of Total records
        $page->set_records_per_page(1); //number of records displays on a single page
        //$page->show_prev_next=false; //Shows Previous and Next Page links
        $page->show_scroll_prev_next=true; //Shows Previous and Next Page links
        $page->show_first_last=true; //Shows first and Last Page links
        $page->set_page_name('dsays2.php'); //set the page name on which paging has to be doen

        echo $qry=$page->get_limit_query($qry); //return the query with limits
        echo "<br />";
        $db->query($qry);
        while($row=$db->fetchObject())
        {
        echo $row->username."<br />";
        }
        $page->get_page_nav(); // Shows the nevigation bars;
     */
     
     /* Example 2
       $page=new Page();
		

       $page->set_page_data('dsays2.php',$total_records,7,0,false,false,true);

     */
?>
