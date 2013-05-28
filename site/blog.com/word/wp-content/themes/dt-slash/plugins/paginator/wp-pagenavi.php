<?php

function wp_pagenavi($before = '', $after = '', $ul_class = 'paginator') {
	global $wpdb, $wp_query;
	//pagenavi_init();
	if (!is_single()) {
		$request = $wp_query->request;
		$posts_per_page = intval(get_query_var('posts_per_page'));
		$paged = intval(get_query_var('paged'));

		$num_pages = 5;
		if( function_exists('of_get_option') && of_get_option('layout_paginator_show_all_checkbox', false) ) {
			$num_pages = 9999;
		}

	   $pagenavi_options = array();
	   $pagenavi_options['pages_text'] = __('Page %CURRENT_PAGE% of %TOTAL_PAGES%','wp-pagenavi');
	   $pagenavi_options['current_text'] = '%PAGE_NUMBER%';
	   $pagenavi_options['page_text'] = '%PAGE_NUMBER%';
	   $pagenavi_options['first_text'] = __('&laquo; First','wp-pagenavi');
	   $pagenavi_options['last_text'] = __('Last &raquo;','wp-pagenavi');
	   $pagenavi_options['prev_text'] = '';
	   $pagenavi_options['next_text'] = '';
	   $pagenavi_options['dotright_text'] = __('','wp-pagenavi');
	   $pagenavi_options['dotleft_text'] = __('','wp-pagenavi');
	   $pagenavi_options['style'] = 1;
	   $pagenavi_options['num_pages'] = $num_pages;
	   $pagenavi_options['always_show'] = 0;
		
		$numposts = $wp_query->found_posts;
		$max_page = $wp_query->max_num_pages;
		/*
		$numposts = 0;
		if(strpos(get_query_var('tag'), " ")) {
		    preg_match('#^(.*)\sLIMIT#siU', $request, $matches);
		    $fromwhere = $matches[1];			
		    $results = $wpdb->get_results($fromwhere);
		    $numposts = count($results);
		} else {
			preg_match('#FROM\s*+(.+?)\s+(GROUP BY|ORDER BY)#si', $request, $matches);
			$fromwhere = $matches[1];
			$numposts = $wpdb->get_var("SELECT COUNT(DISTINCT ID) FROM $fromwhere");
		}
		$max_page = ceil($numposts/$posts_per_page);
		*/
		if(empty($paged) || $paged == 0) {
			$paged = 1;
		}
		$pages_to_show = intval($pagenavi_options['num_pages']);
		$pages_to_show_minus_1 = $pages_to_show-1;
		$half_page_start = floor($pages_to_show_minus_1/2);
		$half_page_end = ceil($pages_to_show_minus_1/2);
		$start_page = $paged - $half_page_start;
		if($start_page <= 0) {
			$start_page = 1;
		}
		$end_page = $paged + $half_page_end;
		if(($end_page - $start_page) != $pages_to_show_minus_1) {
			$end_page = $start_page + $pages_to_show_minus_1;
		}
		if($end_page > $max_page) {
			$start_page = $max_page - $pages_to_show_minus_1;
			$end_page = $max_page;
		}
		if($start_page <= 0) {
			$start_page = 1;
		}
		if($max_page > 1 || intval($pagenavi_options['always_show']) == 1) {
			$pages_text = str_replace("%CURRENT_PAGE%", number_format_i18n($paged), $pagenavi_options['pages_text']);
			$pages_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pages_text);
			echo $before.'<ul class="'.$ul_class.'">'."\n";
			switch(intval($pagenavi_options['style'])) {
				case 1:
					if(!empty($pages_text)) {
						//echo '<li class="frot">' . $pages_text . '</a>';
					}
					if ($start_page >= 2 && $pages_to_show < $max_page && 0) {
						$first_page_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pagenavi_options['first_text']);
						echo '<li><a href="'.clean_url(get_pagenum_link()).'" title="'.$first_page_text.'">&#8201;'.$first_page_text.'&#8201;</a></li>';
						if(!empty($pagenavi_options['dotleft_text'])) {
							echo '<li><a> '.$pagenavi_options['dotleft_text'].' </a></li>';
						}
					}
					if( get_previous_posts_link($pagenavi_options['prev_text']) != NULL ) {
						echo "<li class=\"page_". intval($paged - 1). " larr\">";
						ob_start();
						previous_posts_link($pagenavi_options['prev_text']);
						$d = ob_Get_clean();
						$d = str_replace($pagenavi_options['prev_text'], '<span>'.$pagenavi_options['prev_text'].'</span>', $d);
						echo $d;
						echo "</li>";
					}
					for($i = $start_page; $i <= $end_page; $i++) {						
						if($i == $paged) {
//							if($end_page != $i){
								$current_page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['current_text']);
								echo '<li class="page_'. $i. ' act"><a href="'.esc_url(get_pagenum_link($i)).'"> '.$current_page_text.' </a></li>';
/*							}else{
								$current_page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['current_text']);
								echo '<li class="act rarr"><a class="'.esc_url(get_pagenum_link($i)).'"> '.$current_page_text.' </a></li>';
							}
*/						} else {
							$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
							echo '<li class="page_'. $i. '"><a href="'.esc_url(get_pagenum_link($i)).'" title="'.$page_text.'"> '.$page_text.' </a></li>';
						}
					}
					if( get_next_posts_link($pagenavi_options['next_text'], $max_page) != NULL ) {
						echo "<li class=\"page_". intval($paged + 1). " rarr\">";
						ob_start();
						next_posts_link($pagenavi_options['next_text'], $max_page);
						$d = ob_Get_clean();
						$d = str_replace($pagenavi_options['next_text'], '<span>'.$pagenavi_options['next_text'].'</span>', $d);
						echo $d;
						echo "</li>";
					}
					if ($end_page < $max_page && 0) {
						if(!empty($pagenavi_options['dotright_text'])) {
							echo '<a> '.$pagenavi_options['dotright_text'].' </a>';
						}
						$last_page_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pagenavi_options['last_text']);
						echo '<a href="'.clean_url(get_pagenum_link($max_page)).'" title="'.$last_page_text.'"> '.$last_page_text.' </a>';
					}
					break;
				case 2;
					echo '<form action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" method="get">'."\n";
					echo '<select size="1" onchange="document.location.href = this.options[this.selectedIndex].value;">'."\n";
					for($i = 1; $i  <= $max_page; $i++) {
						$page_num = $i;
						if($page_num == 1) {
							$page_num = 0;
						}
						if($i == $paged) {
							$current_page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['current_text']);
							echo '<option value="'.clean_url(get_pagenum_link($page_num)).'" selected="selected" class="current">'.$current_page_text."</option>\n";
						} else {
							$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
							echo '<option value="'.clean_url(get_pagenum_link($page_num)).'">'.$page_text."</option>\n";
						}
					}
					echo "</select>\n";
					echo "</form>\n";
					break;
			}
			echo '</ul>'.$after."\n";
		}
	}
}

?>
