CONTENTS
    + Introduction
    + Features
    + Goals
    + Running Sample Application



Note: For full documentation, please check out the "docs" folder present inside the framework.


Introduction
=============================================================

EZPHP is an easy-to-use MVC-based framework allowing you to develop applications much faster than doing it from scratch by providing you with ready-made libraries and utility classes. It comes with a mechanism to easily develop your applications in OOP way even if you are not much familiar with it. EZPHP aims to be light-weight, fast and easy to use thereby targeted to those who want to get the framework complexities out of the their way and start writing the application fast from day one.

If you are looking to quickly develop a website, EZPHP provides you with easier MVC and OOP approach with handy library and helper classes to get the job done fast. Or if you don't want to learn or use some hard to use/learn frameworks available, EZPHP then most probably becomes your definite choice. 


Features
=============================================================

    Easy to Use
    Fast
    Light Weight
    Object Oriented Programming
    Model View Controller (MVC) Design Pattern
    Security And XSS Filtering
    Easily Extendible
    Very Short Learning Curve
    URI Routing
    Built-In MySQL Wrapper
    Data Validation
    Multiple Template System
    Emailer Class
    Image Manipulation
    Captcha Creation
    File Upload Class
    Paging Class
    Caching
    Search Engine Friendly URLs
    Timing Scripts
    RSS Generation
    Browser Detection
    And More Utility Classes...



Goals
=============================================================

The EZPHP has been designed with the following goals:

    Light Weight
    Fast
    Easy To Use



Running Sample Application:
=============================================================

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

