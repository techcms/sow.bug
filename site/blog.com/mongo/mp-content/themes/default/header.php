<?php

/* THEME SPECIFIC GLOBALS */
global $link_format, $featured_format, $article_format, $partners, $options, $single_format;
global $current_object_id, $current_object_title;

/* LOAD DEFAULT MONGO HEADER */
mp_get_header(true);

/* MP_GET_CONTENT ARRAYS SETTINGS - LISTED AS SUCH FOR EASE OF UNDERSTANDING */
$link_format = array(
    'type'      => 'links',
    'style'     => 'a'
);
$single_format = array(
    'type'      => false,
    'style'     => 'single',
    'can_query' => true
);
$article_format = array(
    'type'      => 'articles',
    'style'     => 'li'
);
$partners = array(
    'type'      => 'partners',
    'style'     => 'a'
);
/* FEATURED FORMAT WILL SHOWCASE AJAX LOADING */
$featured_format = array(
    'type'      => 'featured-articles',
    'style'     => 'array'
);

/* IN ORDER TO USE CORE FUNCTIONS */
$mp = mongopress_load_mp();

/* EXTRACT OPTIONS */
$options = $mp->options();

/* NOW ON TO THE HTML */
?>

<div id="admin-options">
    <?php 
    mp_login_form();
    $logged_in = $mp->is_logged_in();
    if($logged_in){
        echo '<nav id="admin-navigation"><a href="#" id="mp-login" class="radius5-bottom" '.mp_get_attr_filter('header.php','a','#','mp-login','radius5-bottom','').'>'.__('Logout / Admin').'</a></nav>';
    }else{
        echo '<nav id="admin-navigation"><a href="#" id="mp-login" class="radius5-bottom" '.mp_get_attr_filter('header.php','a','#','mp-login','radius5-bottom','').'>'.__('Login').'</a></nav>';
    }
    ?>
</div>

<span class="logo-wrapper">
    <?php do_action('mp_default_theme_header'); ?>
</span>
<h1 id="site-description">
    <?php
    if(!empty($current_object_title)){
        echo $current_object_title.' | '.$options['site_description'];
    }else{
        echo $options['site_description'];
    }
    ?></h1>