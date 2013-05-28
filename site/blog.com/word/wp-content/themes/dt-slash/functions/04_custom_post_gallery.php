<?php
/* post type for gallery */
$labels = array(
	'name' => _x('Photo Albums', 'post type general name', 'dt'),
	'singular_name' => _x('Photo Album', 'post type singular name', 'dt'),
	'add_new' => _x('Add New', 'post type new', 'dt'),
	'add_new_item' => __('Add New Album', 'dt'),
	'edit_item' => __('Edit Album', 'dt'),
	'new_item' => __('New Album', 'dt'),
	'view_item' => __('View Album', 'dt'),
	'search_items' => __('Search for Albums', 'dt'),
	'not_found' =>  __('No Albums Found', 'dt'),
	'not_found_in_trash' => __('No Albums Found in Trash', 'dt'), 
	'parent_item_colon' => '',
	'menu_name' => 'Photo Albums'

);
$args = array(
	'labels' => $labels,
	'public' => false,
	'publicly_queryable' => false,
	'show_ui' => true, 
	'show_in_menu' => true, 
	'query_var' => true,
	'rewrite' => false,
	'capability_type' => 'post',
	'has_archive' => false, 
	'hierarchical' => false,
	'menu_position' => 20,
	'menu_icon'		=>get_template_directory_uri(). '/images/admin_ico_gallery.png',
	'supports' => array( 'title', 'thumbnail', 'excerpt'/*, 'editor' */) // decomment this to enable WISWIG
); 
register_post_type('dt_gallery',$args);

// metaboxez
// WP 3.0+
add_action( 'add_meta_boxes', 'dt_gallery_box' );

// backwards compatible
//add_action( 'admin_init', 'dt_gallery_box', 1 );

/* Do something with the data entered */
add_action( 'save_post', 'dt_gallery_list_save' );
add_action( 'save_post', 'dt_gallery_order_options_save' );

function dt_gallery_box() {
	add_meta_box(
		'gallery-order',
		__( 'Gallery order options', 'dt' ),
		'dt_gallery_order_options',
		'dt_gallery',
		'side',
		'low'
	);
	
	add_meta_box(
		'gallery-admin',
		_x( 'Gallery', 'backend albums metabox uploader', LANGUAGE_ZONE ),
		'dt_gallery_admin_box',
		'dt_gallery',
		'normal',
		'low'
	);
	
	if ( isset($_GET['post']) ) {
		$post_id = $_GET['post'];
	} elseif ( isset($_POST['post_ID']) ) {
		$post_id = $_POST['post_ID'];
	} else
		return;

	$template_file = get_post_meta($post_id,'_wp_page_template',TRUE);

	// check for a template type
	if ( ($template_file == 'gallery.php') || ($template_file == 'photos.php') ) {
		add_meta_box(
			'gallery-list',
			_x( 'Gallery options', 'backend albums layout', LANGUAGE_ZONE ),
			'dt_gallery_list_box',
			'page',
			'side',
			'high'
		);
	}
	
}

function dt_gallery_list_box( $post ) {
	echo '<script>var dt_admin = {box: "#gallery-list"};</script>';
	echo '<script src="' . get_template_directory_uri() . '/js/admin_gallery.js"></script>';
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'gallery_nonce' );
	$all_ids = array();
	$query = new Wp_Query( 'post_type=dt_gallery&posts_per_page=-1&post_status=publish' );
	$posts = $query->posts;
	$filter = get_post_meta( $post->ID, 'galery_filter', true );
	$number_portf = ( isset($filter['posts_per_page']) && $filter['posts_per_page'] )?$filter['posts_per_page']:'';
	if(isset($filter['only']) && is_array($filter['only']) ) $only = $filter['only']; else $only = array();
	if(isset($filter['except']) && is_array($filter['except']) ) $except = $filter['except']; else $except = array();
	if( empty($only) && empty($except) ) $all = ' checked="checked"'; else $all ='';
	echo '<p>' . __( 'Show Albums:', 'dt' ) . '<br></p>';
	echo '<div class="showhide"><label><input name="show_type_gal" value="all"' . $all . ' type="radio">' . __( 'All', 'dt' ) . '</label><br></div>';
	echo '<div class="showhide"><label><input name="show_type_gal" value="only"' . (empty($only)?'':' checked="checked"') . ' type="radio">' . __( 'Only...', 'dt' ) . '</label><br>';
		echo '<div style="margin-left: 20px; margin-bottom: 8px; display: none;" class="list">';
			if( $posts ){
				foreach( $posts as $post ){
					$all_ids[] = $post->ID;
					echo '<label><input name="show_gal[only][]" value="' . $post->ID . '" type="checkbox"' . (in_array($post->ID, $only)?' checked':'') . '>' . $post->post_title . '</label><br>';
				}
			}
		echo '</div>';
	echo '</div>';
	echo 	'<div class="showhide"><label><input name="show_type_gal" value="except"' . (empty($except)?'':' checked="checked"') . ' type="radio">' . __( 'Except...', 'dt' ) . '</label><br>
			<div style="margin-left: 20px; margin-bottom: 8px; display: none;" class="list">';
			if( $posts ){
				foreach( $posts as $post ){
					echo '<label><input name="show_gal[except][]" value="' . $post->ID . '" type="checkbox"' . (in_array($post->ID, $except)?' checked':'') . '>' . $post->post_title . '</label><br>';
				}
			}
		echo '</div>';
	echo '</div>';
	echo '<input type="hidden" name="show_gal[all]" value="'. serialize( $all_ids ). '"/>';
	echo '<p>';
	echo '<input type="text" id="dt_gallery_postsonpage" size="4" name="number_portf" value="'. $number_portf. '"/>';
	echo '<label for="dt_gallery_postsonpage">'. __( 'Number of posts on this page( if empty - uses standard setting )', LANGUAGE_ZONE ). '</label>';
	echo '</p>';
}

function dt_gallery_list_save( $post_id ) {
	// verify if this is an auto save routine. 
	// If it is our form has not been submitted, so we dont want to do anything
  
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;

	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times

	if ( !isset( $_POST['gallery_nonce'] ) || !wp_verify_nonce( $_POST['gallery_nonce'], plugin_basename( __FILE__ ) ) )
		return;

  
	// Check permissions
	if ( !current_user_can( 'edit_page', $post_id ) )
	return;
	
	$mydata = null;
	switch( $_POST['show_type_gal'] ) {
		case 'only':
			if( isset($_POST['show_gal']['only']) ) {
				$mydata['only'] = $_POST['show_gal']['only'];
			}
			break;
		case 'except':
			if( isset($_POST['show_gal']['except']) ) {
				$mydata['except'] = $_POST['show_gal']['except'];
			}
		default:
			if( isset($_POST['show_gal']['all']) ) {
				$mydata['all'] = unserialize( $_POST['show_gal']['all'] );
			}
	}
	
	$mydata['posts_per_page'] = intval($_POST['number_portf']);
	
	update_post_meta( $post_id, 'galery_filter', $mydata );
}

function dt_gallery_order_options( $post ) {
	$box_name = 'dt_gallery_order';
	$defaults = array(
		'orderby'	=> 'menu_order',
		'order'		=> 'ASC'
	);
	$opts = get_post_meta( $post->ID, '_'.$box_name, true );
	$opts = wp_parse_args( $opts, $defaults );
	
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), $box_name. '_nonce' );
	
	$p_orderby = array(
		'ID'        => _x( 'Order by ID', 'backend orderby', LANGUAGE_ZONE ),
		'author'    => _x( 'Order by author', 'backend orderby', LANGUAGE_ZONE ),
		'title'     => _x( 'Order by title', 'backend orderby', LANGUAGE_ZONE ),
		'date'      => _x( 'Order by date', 'backend orderby', LANGUAGE_ZONE ),
		'modified'  => _x( 'Order by modified', 'backend orderby', LANGUAGE_ZONE ),
		'rand'      => _x( 'Order by rand', 'backend orderby', LANGUAGE_ZONE ),
		'menu_order'=> _x( 'Order by menu', 'backend orderby', LANGUAGE_ZONE )
	);
	
	$p_order = array(
		'ASC'   => _x( 'Ascending', 'backend order', LANGUAGE_ZONE ),
		'DESC'	=> _x( 'Descending', 'backend order', LANGUAGE_ZONE )
	);
	?>
	<p>
		<?php foreach( $p_order as $value=>$desc ): ?>
		<label><input type="radio" value="<?php echo esc_attr($value); ?>" name="<?php echo esc_attr($box_name); ?>_order" <?php checked($value == $opts['order']); ?> />&nbsp;<?php echo $desc; ?></label>
		<?php endforeach; ?>
	</p>
	<p>
		<select name="<?php echo esc_attr($box_name); ?>_orderby">
		<?php foreach( $p_orderby as $value=>$desc ): ?>
		<option value="<?php echo esc_attr($value); ?>" <?php selected($value == $opts['orderby']); ?>><?php echo $desc; ?></option>
		<?php endforeach; ?>
		</select>
	</p>
	<?php
}

function dt_gallery_order_options_save( $post_id ) {
	$box_name = 'dt_gallery_order';
	// verify if this is an auto save routine. 
	// If it is our form has not been submitted, so we dont want to do anything
  
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;

	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times

	if ( !isset( $_POST[$box_name. '_nonce'] ) || !wp_verify_nonce( $_POST[$box_name. '_nonce'], plugin_basename( __FILE__ ) ) )
		return;

  
	// Check permissions
	if ( !current_user_can( 'edit_page', $post_id ) )
		return;
	
	$mydata = null;

	if( isset($_POST[$box_name. '_orderby']) ) {
		$mydata['orderby'] = $_POST[$box_name. '_orderby'];
	}
	
	if( isset($_POST[$box_name. '_order']) ) {
		$mydata['order'] = $_POST[$box_name. '_order'];
	}
	
//				$mydata['hide_thumbnail'] = isset( $_POST[$box_name . '_hide_thumb'] );
	
	update_post_meta( $post_id, '_'.$box_name, $mydata );
}
			
function dt_gallery_admin_box( $post ) {

    $tab = 'type';
    $args = array(
        'post_type'			=>'attachment',
        'post_status'		=>'inherit',
        'post_parent'		=>$post->ID,
        'posts_per_page'	=>1
    );
    $attachments = new Wp_Query( $args );

    if( !empty($attachments->posts) ) {
        $tab = 'dt_gallery_media';
    }
    
    $u_href = get_admin_url();
    $u_href .= '/media-upload.php?post_id='. $post->ID;
    $u_href .= '&width=670&height=400&dt_custom=1&tab='.$tab;
?>
    <iframe id="dt-albums-uploader" src="<?php echo esc_url($u_href); ?>" width="100%" height="560">The Error!!!</iframe>
	<?php dt_uploader_style_script('dt-albums-uploader'); ?>	
<?php
}

function dt_album_media_form( $errors ) {
    global $redir_tab, $type;

    $redir_tab = 'dt_gallery_media';
    media_upload_header();
    
    $post_id = intval($_REQUEST['post_id']);
    $form_action_url = admin_url("media-upload.php?type=$type&tab=dt_gallery_media&post_id=$post_id");
    $form_action_url = apply_filters('media_upload_form_url', $form_action_url, $type);
    $form_class = 'media-upload-form validate';
    
    if ( get_user_setting('uploader') )
        $form_class .= ' html-uploader';
?>	
    <script type="text/javascript">
    <!--
    jQuery(function($){
        var preloaded = $(".media-item.preloaded");
        if ( preloaded.length > 0 ) {
            preloaded.each(function(){prepareMediaItem({id:this.id.replace(/[^0-9]/g, '')},'');});
            updateMediaForm();
        }
    });
    -->
    </script>
    <div id="sort-buttons" class="hide-if-no-js">
    <span>
    <?php _e('All Tabs:'); ?>
    <a href="#" id="showall"><?php _e('Show'); ?></a>
    <a href="#" id="hideall" style="display:none;"><?php _e('Hide'); ?></a>
    </span>
    <?php _e('Sort Order:'); ?>
    <a href="#" id="asc"><?php _e('Ascending'); ?></a> |
    <a href="#" id="desc"><?php _e('Descending'); ?></a> |
    <a href="#" id="clear"><?php _ex('Clear', 'verb'); ?></a>
    </div>
    <form enctype="multipart/form-data" method="post" action="<?php echo esc_attr($form_action_url); ?>" class="<?php echo $form_class; ?>" id="gallery-form">
    <?php wp_nonce_field('media-form'); ?>
    <?php //media_upload_form( $errors ); ?>
    <table class="widefat" cellspacing="0">
    <thead><tr>
    <th><?php _e('Media'); ?></th>
    <th class="order-head"><?php _e('Order'); ?></th>
    <th class="actions-head"><?php _e('Actions'); ?></th>
    </tr></thead>
    </table>
    <div id="media-items">
    <?php add_filter('attachment_fields_to_edit', 'media_post_single_attachment_fields_to_edit', 10, 2); ?>
    <?php $_REQUEST['tab'] = 'gallery'; ?>
    <?php echo get_media_items($post_id, $errors); ?>
    <?php $_REQUEST['tab'] = 'dt_gallery_media';?>
    </div>

    <p class="ml-submit">
    <?php submit_button( __( 'Save all changes' ), 'button savebutton', 'save', false, array( 'id' => 'save-all', 'style' => 'display: none;' ) ); ?>
    <input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
    <input type="hidden" name="type" value="<?php echo esc_attr( $GLOBALS['type'] ); ?>" />
    <input type="hidden" name="tab" value="<?php echo esc_attr( $GLOBALS['tab'] ); ?>" />
    </p>
    </form>

	<div style="display: none;">
    <input type="radio" name="linkto" id="linkto-file" value="file" />
    <input type="radio" checked="checked" name="linkto" id="linkto-post" value="post" />
    <select id="orderby" name="orderby">
    	<option value="menu_order" selected="selected"><?php _e('Menu order'); ?></option>
        <option value="title"><?php _e('Title'); ?></option>
        <option value="post_date"><?php _e('Date/Time'); ?></option>
        <option value="rand"><?php _e('Random'); ?></option>
    </select>
    <input type="radio" checked="checked" name="order" id="order-asc" value="asc" />
    <input type="radio" name="order" id="order-desc" value="desc" />
    <select id="columns" name="columns">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3" selected="selected">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
   	</select>
	</div>
<?php
}

// custom mediauploader tab action
function dt_a_album_mu() {
    $errors = array();

    if ( !empty($_POST) ) {
        $return = media_upload_form_handler();

        if ( is_string($return) )
            return $return;
        if ( is_array($return) )
            $errors = $return;
    }

    wp_enqueue_style( 'media' );
    wp_enqueue_script('admin-gallery');
    
    return wp_iframe( 'dt_album_media_form', $errors );
}
add_action( 'media_upload_dt_gallery_media', 'dt_a_album_mu' );

// media uploader for gallery filter
function dt_f_album_mu($tabs) {
	if( 'dt_gallery' == get_post_type($_REQUEST['post_id']) && !empty($_GET['dt_custom']) ) {
		global $wpdb;
        
        if( isset($tabs['library']) ) {
			unset($tabs['library']);
		}
		
        if( isset($tabs['gallery']) ) {
			unset($tabs['gallery']);
		}
        
        if( isset($tabs['type_url']) ) {
			unset($tabs['type_url']);
		}
        
        $post_id = intval($_REQUEST['post_id']);
  
        if ( $post_id ) {
            $attachments = intval( $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'attachment' AND post_status != 'trash' AND post_parent = %d", $post_id ) ) );
        }
        
        if ( empty($attachments) ) {
            unset($tabs['gallery']);
            return $tabs;
        }
    
		if( !isset($tabs['dt_gallery_media'])) {
			$tabs['dt_gallery_media'] = sprintf(__('Images (%s)'), "<span id='attachments-count'>$attachments</span>");
		}
        
        if( isset($tabs['type']) ) {
            $tabs['type'] = 'Upload';
        }
	}
	return $tabs;
}
add_filter('media_upload_tabs', 'dt_f_album_mu', 99 );
?>