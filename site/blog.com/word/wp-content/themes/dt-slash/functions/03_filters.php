<?php

// filter prevent loading gallery after save uploaded images
function dt_f_album_aftos( $post, $attachments ) {
    if( !empty($_GET['dt_custom']) ) {
        if( isset($_GET['tab']) && 'type' == $_GET['tab']) {
            unset($_POST['save']);
        }
    }
    return $post;
}
add_filter( 'attachment_fields_to_save', 'dt_f_album_aftos', 99, 2 );

/* custom media uploader filter */
function dt_media_upload_form_url_filter( $form_action_url, $type ) {
	if( !empty($_GET['dt_custom']) ) {
		$form_action_url .= '&dt_custom=1';
	}
	return $form_action_url;
}
add_filter('media_upload_form_url', 'dt_media_upload_form_url_filter', 99, 2);

function dt_posts_parents_where( $where ) {
	global $wpdb;
	global $dt_where_filter_param;
	
	$where .= sprintf( " AND %s.post_parent IN(%s)", $wpdb->posts, strip_tags( $dt_where_filter_param ) );
	return $where;
}

// excerpts details
function new_excerpt_more($more) {
	return '';
}
add_filter('excerpt_more', 'new_excerpt_more');

function dt_filter_gallery_sc($output, $attr) {
	global $post, $wp_locale;
	$exclude_def = '';
	if( $hide_in_gal = get_post_meta( $post->ID, 'hide_in_gal', true ) && ('gallery' == get_post_format($post->ID)) ) {
		$exclude_def = get_post_thumbnail_id( $post->ID );
	}
	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}
		
	extract(shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post->ID,
		'itemtag'    => 'li',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => $exclude_def
	), $attr));

	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty($include) ) {
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	}

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
			$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		return $output;
	}

	if( isset($attr['link']) && ('file' == $attr['link']) ) {
		$hs_class = " hs_me";
	}else {
		$hs_class = "";
	}
	
	$itemtag = tag_escape($itemtag);
	$columns = intval($columns);
	$size_class = sanitize_html_class( $size );
	
	$output = "<ul class='gall_std{$hs_class} gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";

	$i = 0;
	foreach ( $attachments as $img_id => $attachment ) {
		$class = 'post-gal-item';
		$description = $d_class = $onclick = '';
		if( isset($attr['link']) && ('file' == $attr['link']) ) {
			$href = wp_get_attachment_image_src($img_id, 'full');
			$href = $href?current($href):'#';
			$class .= " with-hs";
			$onclick = ' onclick="return hs.expand(this, {slideshowGroup: \'group-'. $id. '\'})"';
		}else {
			$href = get_permalink($img_id);
		}
		
		if( $attachment->post_excerpt ) {
			$description = '<p class="wp-caption-text">'.wptexturize($attachment->post_excerpt).'</p>';
			$d_class .= ' wp-caption ';
		}
		
		$alt = esc_attr(get_post_meta($attachment->ID,'_wp_attachment_image_alt', true ));
		$src = wp_get_attachment_image_src($img_id, $size);
		
		$link = "<a class='{$class}' href='{$href}'{$onclick}>
		<img src='{$src[0]}' alt='{$alt}' width='{$src[1]}' height='{$src[2]}'/><i></i>
		</a>";
		
		$output .= "<{$itemtag} class='shadow_light gallery-item{$d_class}' style='width: {$src[1]}px;'>";
		$output .= $link;
		$output .= $description;
		$output .= "</{$itemtag}>";
	}

	$output .= "</ul>\n";

	return $output;
}
add_filter('post_gallery', 'dt_filter_gallery_sc', 10, 2);

function dt_bquote( $content ) {
	$up = <<<HDO
	<div class="blockquote_bg status">
        <blockquote>
			<span class="quotes-l">
				<span class="quotes-r">
HDO;
	$down = <<<HDO
				</span>
			</span>
		</blockquote>
	</div>
HDO;
	$content = str_replace('<blockquote>', $up, $content);
	$content = str_replace('</blockquote>', $down, $content);
	return $content;
}
add_filter('the_content', 'dt_bquote', 99, 1);

// remove protected: from title
function dt_protected_title_format_filter( $format ) {
	return '%s';
}
add_filter('protected_title_format', 'dt_protected_title_format_filter');

// password form filter
function dt_password_form() {
    global $post, $paged;
    $http_referer = wp_referer_field( false );
    $wp_ver = explode('.', get_bloginfo('version'));
    $wp_ver = array_map( 'intval', $wp_ver );
    
    if( $wp_ver[0] < 3 || ( 3 == $wp_ver[0] && $wp_ver[1] <= 3 ) ) {
        $form_action = esc_url( get_option('siteurl') . '/wp-pass.php' );
    }else {
        $form_action = esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ); 
    }
  
	$label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
    $o = '<div class="form-protect"><form class="protected-post-form get-in-touch uniform" action="'. $form_action. '" method="post">'. $http_referer. '
    <div>' . __( "To view this protected post, enter the password below:", LANGUAGE_ZONE ) . '</div>
    <label for="' . $label . '">' . __( "Password:", LANGUAGE_ZONE ) . '&nbsp;&nbsp;&nbsp;</label><div class="i_h"><input name="post_password" id="' . $label . '" type="password" size="20" /></div>
	<a title="Submit" class="button go_submit" onClick="dt_submit_pass(this); return false;" href="#"><span class="but-r"><span>' . __( "Submit", LANGUAGE_ZONE ). '</span></span></a>
    </form></div>
    ';
    return $o;
}
add_filter( 'the_password_form', 'dt_password_form' );

// disable comments to attachments
function dt_disable_attachment_comments( $open, $post_id ) {
	$post = get_post( $post_id );
	if ( 'attachment' == $post->post_type )
		$open = false;
	return $open;
}