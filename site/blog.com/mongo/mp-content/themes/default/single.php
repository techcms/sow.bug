<?php get_template_part('header'); 
global $link_format, $single_format; 
?>

<div id="site-wrapper" class="radius5">
    <nav id="primary-navigation"><?php mp_content($link_format); ?></nav>
    <div id="primary-content"><?php mp_content($single_format); ?></div>
    <div id="secondary-content"><?php get_template_part('sidebar'); ?></div>
</div>

<?php get_template_part('footer'); ?>
