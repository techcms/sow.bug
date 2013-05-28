<?php

require_once dirname(__FILE__).'/simplepie.php';

function mp_fetch_feed($url='http://mongopress.org/feed/',$length=0,$start=0,$direct_link=true){
	$feed = new SimplePie();
	$feed->set_feed_url($url);
	$feed->init();
	// if single item, set start to item number and length to 1
	if(isset($_GET['item'])){
		$start = $_GET['item'];
		$length = 1;
	}
	// set item link to script uri
	$link = $_SERVER['REQUEST_URI'];
	echo '<ul class="rss-items">';
	// loop through items
	foreach($feed->get_items($start,$length) as $key=>$item){
		// set query string to item number
		$queryString = '?item=' . $key;
		// if we're displaying a single item, set item link to itself and set query string to nothing
		if(isset($_GET['item'])){
			$link = $item->get_link();
			$queryString = '';
		}
		// display item title and date
		echo '<li class="rss-item">';
			if($direct_link){
				echo '<a href="' . $item->get_link() . '" target="_blank">' . $item->get_title() . '</a>';
			}else{
				echo '<a href="' . $link . $queryString . '">' . $item->get_title() . '</a>';
			}
			echo ' <small>'.$item->get_date().'</small>';
			// if single item, display content
			if(isset($_GET['item'])){
				echo ' <small>'.$item->get_content().'</small><br>';
			}
		echo '</li>';
	}
	echo '</ul>';
}