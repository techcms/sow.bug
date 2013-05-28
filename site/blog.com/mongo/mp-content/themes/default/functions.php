<?php

/* EXAMPLE ACTION */
add_action('mp_footer','my_mp_footer');
function my_mp_footer(){
    ?>
    <script>
        jQuery(document).ready(function(){
            jQuery('nav#admin-navigation a#mp-login').live('click',function(e){
                e.preventDefault();
                jQuery('form#mp-login-form').toggle('slow');
            });
        });
    </script>
    <?php
}

/* EXAMPLE FILTERS */
add_filter('mp_meta_author','my_meta_author_info');
function my_meta_author_info(){
    return __('Mark Smalley') ;
}
add_filter('mp_meta_description','my_meta_description_info');
function my_meta_description_info(){
    return __('Powered by MongoPress');
}
add_filter('mp_page_title','my_page_title');
function my_page_title($title){
    $mp = mongopress_load_mp();
    $mp_perma = mongopress_load_perma();
    $options = $mp->options();
    $perma = $mp_perma->current();
    if($perma=='admin'){
        return sprintf(__('Admin Dashboard | %s'),$options['site_name']);
    }elseif($perma=='admin/add'){
        return sprintf(__('Add Object | %s'),$options['site_name']);
    }elseif($perma=='admin/media'){
        return sprintf(__('Media Gallery | %s'),$options['site_name']);
    }elseif($perma=='admin/options'){
        return sprintf(__('Misc Options | %s'),$options['site_name']);
    }else{
        return $title;
    }
}