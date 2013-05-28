<?php

function dt_setup_ajax(){
	// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
	global $post;
	wp_localize_script(
		'dt_scripts',
		'dt_ajax',
		array(
			'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
			'tax_kboom' => $post?$post->ID:''
		)
	);
}
add_action('wp_enqueue_scripts', 'dt_setup_ajax');

// portfolio part
function dt_ajax_portfolio_filter(){
	$cat_slug = isset( $_POST['post_ctagory_slug'] )?$_POST['post_ctagory_slug']:'';
	$item_ids = isset( $_POST['item_ids'] )?$_POST['item_ids']:array();
	$dt_paged = isset( $_POST['dt_paged'] )?$_POST['dt_paged']:null;
	$tax_arr = isset( $_POST['tax_arr'] )?intval( $_POST['tax_arr'] ):null;
	
	$args = array(
		'filter'	=> $cat_slug,
		'item_ids'	=> $item_ids,
		'paged'		=> $dt_paged,
		'post_id'	=> $tax_arr
	);
				
	$result = dt_get_filtered_potfolio( $args );
	// generate the response
    $response = json_encode(
		array(
			'success'		=>true ,
			'category'		=>$cat_slug,
			'html_content'	=>$result['html'],
			'spare'			=>(array)$result['spare'],
			'paginator'		=>$result['paginator']
		)
	);

	// response output
    header( "Content-Type: application/json" );
    echo $response;

    // IMPORTANT: don't forget to "exit"
    exit;
}

function dt_get_filtered_potfolio( array $o ){	
	global $paged, $wp_query;
	$paginator = '';
	
	if( !$paged = $o['paged'] )
		$paged = 1;
	
	$port_terms = get_post_meta( $o['post_id'], 'show_portf', true );
	
	$args = array(
        'post_type'			=> 'portfolio',
        'paged'				=> $paged,
        'post_status'       => 'publish'
    );
	
	if ( $port_terms )
		$args['posts_per_page'] = $port_terms['number_portf'];
	
	// if don'nt have a category
	if( 'none' == $o['filter']){
		$tax = get_terms( 'portfolio-category', array('fields' =>'ids') );
		$args['tax_query'] = array(	
								array(
									'taxonomy'	=>'portfolio-category',
									'field'		=>'id',
									'terms'		=>$tax,
									'operator'	=>'NOT IN'
								)
							);
	// if has category
	}elseif( 'all' != $o['filter'] ){
		$args['tax_query'] = array(	
									array(	'taxonomy'	=>'portfolio-category',
											'field'		=>'slug',
											'terms'		=>$o['filter']
										)
								);
	// show all
	}else{
		if( $port_terms ) {
			$args['tax_query'] = array(
									array(
										'taxonomy'=>'portfolio-category',
										'field'=>'id',
										'terms'=>current( $port_terms ),
										'operator' => ( 'only' == key($port_terms) )?'IN':'NOT IN'
									)
								);
		}
	}

	$wp_query = new Wp_Query( $args );
	ob_start();
	if( $wp_query->have_posts() ){
		global $post;
		while( $wp_query->have_posts() ){
			$wp_query->the_post();
			
			if( in_array( $post->ID, $o['item_ids'] )){
				unset( $o['item_ids'][array_search($post->ID, $o['item_ids'])] );
				continue;
			}
			
			get_template_part('content-masonry', 'portfolio');
		}
	}
	$html = ob_get_clean();
	
	ob_start();
	if( function_exists('wp_pagenavi') ) wp_pagenavi();
	else wp_link_pages();
	$paginator = ob_get_clean();
	
	return array( 'html' => $html, 'spare'	=>$o['item_ids'], 'paginator' =>$paginator  );
}

add_action( 'wp_ajax_nopriv_dt_ajax_portfolio_filter', 'dt_ajax_portfolio_filter' );
add_action( 'wp_ajax_dt_ajax_portfolio_filter', 'dt_ajax_portfolio_filter' );