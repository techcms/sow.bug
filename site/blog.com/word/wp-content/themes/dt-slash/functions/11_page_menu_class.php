<?php
class Dt_Custom_Walker_Page extends Walker_Page {
    private $dt_menu_parents = array();
    private $dt_last_elem = 1;
    private $dt_count = 1;
    private $dt_is_first = true;
    
    function __construct( $options = array() ) {
        if( method_exists('Walker_Page','__construct') ){
            parent::__construct();
        }
    
        $pages = get_pages('sort_column=menu_order,post_title');
        if( $pages ){
            $this->dt_pages = count($pages);
            $prev = 0;
            foreach( $pages as $page ){
                // nonclicable parent menu items
                if( $prev != $page->post_parent && $page->post_parent){
                    $this->dt_menu_parents[] = $page->post_parent;
                    $prev = $page->post_parent;
                }
                // last menu item
                if( !$page->post_parent ){
                    $this->dt_last_elem = $page->ID;
                }
            }
            $this->dt_menu_parents = array_unique( $this->dt_menu_parents );
        }
    }
    
    function start_lvl(&$output, $depth, $args) {
        $output .= $args['dt_submenu_wrap_start'];
        $this->dt_is_first = true;
    }
    
    function start_el(&$output, $page, $depth, $args, $current_page) {
            extract($args, EXTR_SKIP);

            $css_class = array('page_item', 'page-item-'.$page->ID);
            $first_class = '';
           	$page_act_class = '';

            if ( !empty($current_page) ) {
                    $_current_page = get_page( $current_page );
                    _get_post_ancestors($_current_page);
                    if ( isset($_current_page->ancestors) && in_array($page->ID, (array) $_current_page->ancestors) ){
                            $css_class[] = 'current_page_ancestor';
                            $css_class[] = $dt_act_class;
                    }
                    if ( $page->ID == $current_page ){
                            $css_class[] = 'current_page_item';
                            $css_class[] = $dt_act_class;
                            $page_act_class = $dt_act_class;
                    }elseif ( $_current_page && $page->ID == $_current_page->post_parent ){
                            $css_class[] = 'current_page_parent';
                    }
            } elseif ( $page->ID == get_option('page_for_posts') ) {
                    $css_class[] = 'current_page_parent';
            }

            // cute little home in begining
/*            if( 1 == $this->dt_count ) {                
                $dt_title = __('Home', LANGUAGE_ZONE);
                $output .= str_replace(
                    array( '%ITEM_HREF%', '%ITEM_TITLE%', '%ITEM_CLASS%', '%ESC_ITEM_TITLE%', '%IS_FIRST%' ),
                    array( home_url(), $dt_title, 'home', $dt_title, $first_class ),
                    $args['dt_item_wrap_start'] . $args['dt_item_wrap_end']
                );
            }
 */
 /*
            if( $this->dt_is_first ) {
                $css_class[] = 'first';
                $first_class = 'class="first"';
            }
*/

            $attr = '';
            $dt_is_parent = in_array( $page->ID, $this->dt_menu_parents );

            // nonclicable parent menu items
            if( $dt_is_parent && !$args['parent_clicable'] ){
                $css_class[] = 'click-auto';
                $attr = '" onclick="JavaScript: return false;"';
                $attr .= ' style="cursor: default;';
            }
            
			// last menu item
			if( $page->ID == $this->dt_last_elem ){
				$css_class[] = 'last';
			}
			$this->prev_depth = $depth;
			
            $css_class = implode(' ', apply_filters('page_css_class', $css_class, $page));
            
            $dt_title = apply_filters( 'the_title', $page->post_title, $page->ID );
			
 /*           if( $dt_is_parent ) {
                $dt_title .= '<span></span>';
            }
*/	
            $output .= str_replace(
                array( '%ITEM_HREF%', '%ITEM_TITLE%', '%ESC_ITEM_TITLE%', '%ITEM_CLASS%', '%IS_FIRST%', '%DEPTH%', '%ACT_CLASS%' ),
                array(
                    get_permalink($page) . $attr,
                    $link_before . $dt_title . $link_after,
                    '',//' title="'. esc_attr( wp_strip_all_tags( $page->post_title ) ). '"',
                    $css_class,
					$first_class,
					$depth+1,
					$page_act_class
                ),
                $args['dt_item_wrap_start']
            );
                        
            if ( !empty($show_date) ) {
                    if ( 'modified' == $show_date )
                            $time = $page->post_modified;
                    else
                            $time = $page->post_date;
                    $output .= " " . mysql2date($date_format, $time);
            }
            
            $this->dt_count++;
    }
    
    function end_lvl(&$output, $depth, $args) {
        $output .= $args['dt_submenu_wrap_end'];
    }
    
    function end_el(&$output, $page, $depth, $args) {
        $output .= $args['dt_item_wrap_end'];
        $this->dt_is_first = false;
    }
}
