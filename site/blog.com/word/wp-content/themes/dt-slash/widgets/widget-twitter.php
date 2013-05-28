<?php

class DT_Twitter_Widget extends WP_Widget {
	function DT_Twitter_Widget() {
		$widget_ops = array('classname' => 'dt_twitter_feed', 'description' => __('Displays your tweets', LANGUAGE_ZONE) );
		$this->WP_Widget('dt_twitter_feed', __(THEME_TITLE.'Twitter', LANGUAGE_ZONE), $widget_ops);	
	}

	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);	
		$username = $instance['username'];
		$limit = $instance['number'];
		$link = $instance['link'];
		
		echo $before_widget;

	    if(!empty($title)) { echo $before_title . $title . $after_title; };
		
		$feed = "http://search.twitter.com/search.atom?q=from:" . $username . "&rpp=" . $limit;

		$twitterFeed = wp_remote_fopen($feed);
		
		$this->pw_parse_feed($twitterFeed);
		?>
	    <div class="follow-link"><a href="http://twitter.com/<?php echo $username; ?>"><?php echo $link; ?></a></div>
	  	<?php  
		
		echo $after_widget; 
	}
	
	function pw_parse_feed($feed) {
		$feed = str_replace("&lt;", "<", $feed);
		$feed = str_replace("&gt;", ">", $feed);
		$feed = str_replace("&quot;", '"', $feed);
		$clean = explode("<content type=\"html\">", $feed);
		
		$amount = count($clean) - 1;
			
		for ($i = 1; $i <= $amount; $i++) {
			$cleaner = explode("</content><updated>", $clean[$i]);
			$trulyclean = explode('</updated>', $cleaner[1]);
			$href_clean = explode('</published><link type="text/html" href="', $clean[$i-1]);
			
			$href = esc_url(current(explode('" rel="alternate"/>', $href_clean[1])));
			$time = date_parse(str_replace(array("T", "Z"), ' ', $trulyclean[0]));
			$time = gmmktime($time['hour'], $time['minute'], $time['second'], $time['month'], $time['day'], $time['year']);
			$ago = $this->ago($time);
			//$time = date(get_option( 'date_format' ). ' /i/n '. get_option( 'time_format' ), $time);
			
			echo "<div class='post'>";
			echo str_replace("&amp;", "&", $cleaner[0]);			
			echo "<div class='goto_post twit'>";
			echo "<a class='ico_link date' href='$href'>";
			echo $ago;
			echo "</a>";
			echo "</div>";
			echo "</div>";
		}
	}
	
	function ago($time) {
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");

		$now = time();
        $difference = $now - $time;
        $tense = "ago";

		for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			$difference = ceil($difference/$lengths[$j]);
		}

		$difference = ceil($difference);

		if($difference != 1) {
			$periods[$j].= "s";
		}

		return "$difference $periods[$j] $tense";
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'username' => '', 'link' => '', 'number' => '3' ) );
		$title = strip_tags($instance['title']);
		$username = strip_tags($instance['username']);
		$number = strip_tags($instance['number']);
		$link = strip_tags($instance['link']);
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', LANGUAGE_ZONE); ?>: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Username', LANGUAGE_ZONE); ?>: <input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo esc_attr($username); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of Twitts', LANGUAGE_ZONE); ?>: <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr($number); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('Text Link', LANGUAGE_ZONE); ?>: <input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo esc_attr($link); ?>" /></label></p>
		<?php

	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['username'] = strip_tags($new_instance['username']);
		$instance['link'] = strip_tags($new_instance['link']);
		$instance['number'] = strip_tags($new_instance['number']);
		return $instance;
	}
}
register_widget('DT_Twitter_Widget');