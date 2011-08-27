<?php
	
	/*
		Example Use:

			$rss = $this->load_helper('rss', 'Title', 'http://www.example.com', 'Description here...');

			$item1 = array(
				'title'=>'My Title', 
				'link'=>'http://www.example.com', 
				'description'=>'Some description goes right here.', 
				'pubDate'=>date(DATE_RSS),
				'image'=>array('link'=>'http://www.example.com', 'url'=>'http://www.example.com/images/image.jpg', 'title'=>'Title'),
				'language'=>'en');
		
			$rss->addItem($item1);

			echo $rss;
	*/

	class rss extends DomDocument
	{
		private $channel;

		public function __construct($title, $link, $description)
		{
			// call the parent constructor
			parent::__construct();

			// format the created XML
			$this->formatOutput = true;

			// craete the root element
			$root = $this->appendChild($this->createElement('rss'));

			// set to rss2
			$root->setAttribute('version', '2.0');

			// set the channel node
			$this->channel = $root->appendChild($this->createElement('channel'));

			// set the title link and description elements 
			$this->channel->appendChild($this->createElement('title', $title));
			$this->channel->appendChild($this->createElement('link', $link));
			$this->channel->appendChild($this->createElement('description', $description));
		}


		public function addItem($items)
		{
			// create an item
			$item = $this->createElement('item');
			foreach($items as $element=>$value)
			{
				switch($element)
				{
					// create sub elements here
					case 'image':
					case 'skipHour':
					case 'skipDay':
					$im = $this->createElement('image');
					$this->channel->appendChild($im);
					
					foreach( $value as $sub_element=>$sub_value )
					{
						$sub = $this->createElement($sub_element, $sub_value);
						$im->appendChild( $sub );
					}
					break;

					case 'title':
					case 'pubDate':
					case 'link':
					case 'description':
					case 'copyright':
					case 'managingEditor':
					case 'webMaster':
					case 'lastbuildDate':
					case 'category':
					case 'generator':
					case 'docs':
					case 'language':
					case 'cloud':
					case 'ttl':
					case 'rating':
					case 'textInput':
					case 'source':
					$new = $item->appendChild($this->createElement($element, $value));
					break;
				}
			}
			// append the item to the channel
			$this->channel->appendChild($item);

			// allow chaining 
			return $this;
		}

		/***
		 *
		 * @create the XML
		 *
		 * @access public
		 *
		 * @return string The XML string
		 *
		 */
		public function __toString()
		{
			return $this->saveXML();
		}
	}

?>
