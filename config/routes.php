<?php

	$routes['test']     = 'blog/test';
	$routes['articles']     = 'blog/articles';
	$routes['article/:any'] = 'blog/any';
	$routes['page/:num']    = 'blog/num';
	$routes['products/:alpha']    = 'blog/get_product';
	
	$routes['show/([0-9]+)'] = 'blog/show/$1';
	$routes['show2/([a-z]+)'] = 'blog/show2/$1';
	
	$routes['journals'] = "blog";
	$routes['blog/joe'] = "blog/users/34";
	$routes['blog/:any'] = "blog/product_lookup";
	$routes['blogger/:num'] = "blog/product_lookup_by_id/$1";
	$routes['bloggy/([a-z]+)/(\d+)'] = "blog/bloggy/$1/$2";
	
?>