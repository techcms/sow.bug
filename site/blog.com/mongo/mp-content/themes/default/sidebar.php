<?php
/* THEME SPECIFIC GLOBALS */
global $link_format, $featured_format, $article_format, $partners, $options;
/* HARD-CODED WIDGET AREA FOR NOW */
do_action('mp_default_sidebar_top');
?>
<div class="widget">
    <h3 class="widget-title header"><?php _e('Getting Started'); ?></h3>
    <ul>
        <?php mp_content($article_format); ?>
    </ul>
</div>
<div class="widget">
    <h3 class="widget-title header"><?php _e('Friends &amp Partners'); ?></h3>
    <ul>
        <?php mp_content($partners,true); ?>
    </ul>
</div>
<?php
/* HARD-CODED WIDGET AREA FOR NOW */
do_action('mp_default_sidebar_bottom');
?>