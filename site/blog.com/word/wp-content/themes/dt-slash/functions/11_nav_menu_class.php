<?php
class Dt_Walker_Nav_Menu extends Walker_Nav_Menu {
    private $dt_options = array();
    private $dt_menu_parents = array();
    private $dt_last_elem = 1;
    private $dt_count = 1;
    private $dt_is_first = true;

    function __construct( $options = array() ) {
        if( method_exists('Walker_Nav_Menu','__construct') ){
            parent::__construct();
        }
        
        if( is_array($options) ){
            $this->dt_options = $options;
        }
        
        $theme_location = isset($this->dt_options['theme_location'])?$this->dt_options['theme_location']:'';
        if ( $theme_location && ( $locations = get_nav_menu_locations() ) && isset( $locations[ $theme_location ] ) ) {
            $menu = wp_get_nav_menu_object( $locations[ $theme_location ] );
            if( $menu ) {
                $menu_items = wp_get_nav_menu_items($menu->term_id);
                $prev = 0;
                foreach( $menu_items as $item ){
                    // nonclicable parent menu items
                    if( $prev != $item->menu_item_parent && $item->menu_item_parent ){
                        $this->dt_menu_parents[] = $item->menu_item_parent;
                        $prev = $item->menu_item_parent;
                    }
                    // last menu item
                    if( !$item->menu_item_parent ){
                        $this->dt_last_elem = $item->ID;
                    }
                }
                $this->dt_menu_parents = array_unique( $this->dt_menu_parents );
            }
        }
    }
    
    function start_lvl(&$output, $depth, $args) {
        $output .= $args->dt_submenu_wrap_start;
        $this->dt_is_first = true;
    }
    
    function end_lvl(&$output, $depth, $args) {
        $output .= $args->dt_submenu_wrap_end;
    }
    
    function start_el(&$output, $item, $depth, $args) {

            global $wp_query;
            $class_names = $value = '';
            $classes = empty( $item->classes ) ? array() : (array) $item->classes;
            $classes[] = 'menu-item-' . $item->ID;
            $first_class = '';
           	$act_class = '';

            // current element
            if( in_array( 'current-menu-item',  $classes) ||
                in_array( 'current-menu-parent',  $classes) ||
                in_array( 'current-menu-ancestor',  $classes)
            ){
				$classes[] = $args->dt_act_class;
            }

            if( in_array( 'current-menu-item',  $classes) )
				$act_class = $args->dt_act_class;

          /* 
            // cute little home in begining
            if( 1 == $this->dt_count ) {
                $dt_title = __('Home', LANGUAGE_ZONE);
                $output .= str_replace(
                    array( '%ITEM_HREF%', '%ITEM_TITLE%', '%ITEM_CLASS%', '%ESC_ITEM_TITLE%', '%IS_FIRST%' ),
                    array( home_url(), $dt_title, 'home', $dt_title, $first_class ),
                    $args->dt_item_wrap_start . $args->dt_item_wrap_end
                );
            }
           */
		   
		   /*
            if( $this->dt_is_first ) {
                $classes[] = 'first';
                $first_class = 'class="first"';
            }
*/			
			// last element
			if( $item->ID == $this->dt_last_elem ){
				$classes[] = 'last';
			}
			
            $dt_is_parent = in_array( $item->ID, $this->dt_menu_parents );

            // nonclicable parent menu items
            $attributes = '';
			
			$attributes .= !empty( $item->target ) ? '" target="'. esc_attr( $item->target ) : '';
			$attributes .= !empty( $item->xfn ) ? '" rel="'. esc_attr( $item->xfn ) : '';
			
            if( !$args->parent_clicable && $dt_is_parent ){
                $classes[] = 'click-auto';
                $attributes .= '" onclick="JavaScript: return false;"';
                $attributes .= ' style="cursor: default;';
            }

            $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
            $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
            $id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

            $dt_title = apply_filters( 'the_title', $item->title, $item->ID ); 
/*            if( $dt_is_parent ) {
                $dt_title .= '<span></span>';
            }
*/
            $output .= str_replace(
                array( '%ITEM_HREF%', '%ITEM_TITLE%', '%ESC_ITEM_TITLE%', '%ITEM_CLASS%', '%IS_FIRST%', '%DEPTH%', '%ACT_CLASS%' ),
                array(
                    esc_attr($item->url) . $attributes,
                    $args->link_before . $dt_title . $args->link_after,
                    !empty($item->attr_title) ? ' title="'. esc_attr( $item->attr_title ). '"':'',
                    esc_attr($class_names),
					$first_class,
					$depth+1,
					$act_class
                ),
                $args->dt_item_wrap_start
            );
            $this->dt_count++;
/*
            $item_output = isset($args->before)?$args->before:$args['before'];
            
            $item_output .= '<a'. $attributes .'>';
            $item_output .= (isset($args->link_before)?$args->link_before:'') . apply_filters( 'the_title', $item->title, $item->ID ) . (isset($args->link_after)?$args->link_after:'');
            $item_output .= '</a>';
            
            $item_output .= isset($args->after)?$args->after:'';
*/            
            //$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    function end_el(&$output, $item, $depth, $args) {
        $output .= $args->dt_item_wrap_end;
        $this->dt_is_first = false;
    }
}
