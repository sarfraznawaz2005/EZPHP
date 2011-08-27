To be able to run this sample demo app of EZPHP, please create a database by the name of 
"ezphpdb" and execute following quries in that. Finally edit the db settings in config/config.php file.
-----------------------------------------------------------------------------------------------

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `msg` text,
  PRIMARY KEY  (`id`)
);

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL auto_increment,
  `product_name` varchar(255) NOT NULL default '',
  `price` varchar(255) NOT NULL default '',
  `stock` varchar(255) NOT NULL default '',
  `discontinued` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`product_id`)
);

INSERT INTO `products` (`product_id`, `product_name`, `price`, `stock`, `discontinued`) VALUES
(1, 'Chai', '18', '39', '0'),
(2, 'Chang', '19', '17', '0'),
(3, 'Aniseed Syrup', '10', '13', '0'),
(4, 'Chef Antons Cajun Seasoning', '22', '53', '0'),
(5, 'Chef Anton Gumbo Mix', '21.35', '0', '1'),
(6, 'Grandma Boysenberry Spread', '25', '120', '0'),
(7, 'Uncle Bob Organic Dried Pears', '30', '15', '0'),
(8, 'Northwoods Cranberry Sauce', '40', '6', '0'),
(9, 'Mishi Kobe Niku', '97', '29', '1');

