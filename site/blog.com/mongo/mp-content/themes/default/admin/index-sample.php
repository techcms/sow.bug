<?php
/*
 *
 *  PLEASE NOTE THAT BY DEFAULT - FRONT-END ADMIN THEMES ARE NOT ACTIVE
 *  TO ACTIVATE FRONT-END ADMIN THEMES - YOU NEED TO RENAME THIS FILE
 *  IF THIS FILE IS CALLED "index.php" IT WILL BE ACTIVATED
 *  ONCE ACTIVATED, ALL ADMIN FUNCTIONS WILL RUN FROM THE THEME
 *
*/
get_template_part('header');
$mp = mongopress_load_mp();
$mp_options = $mp->options();
$logged_in = $mp->is_logged_in();
$table_options = array(
    'allow_edits'   => false
);
if($logged_in){
    ob_start();
    mp_objects_table($table_options);
    $content = ob_get_clean();
}else{
    $content = '<p style="text-align:center;font-weight:bold;">'.__('Unidentified object in the imperial vortex!').'</p>';
    $content.= '<p style="text-align:center;">'.__('( You do not have permission to view the contents of this page )').'</p>';
}
?>

<div id="site-wrapper" class="radius5">
    <div id="primary-content" class="full">
        <nav id="primary-navigation">
            <a href="<?php echo $mp_options['admin_url']; ?>" class="current"><?php _e('Admin Dashboard'); ?></a>
            <a href="<?php echo $mp_options['admin_url']; ?>add/"><?php _e('Add Object'); ?></a>
            <a href="<?php echo $mp_options['admin_url']; ?>media/"><?php _e('Media Gallery'); ?></a>
            <a href="<?php echo $mp_options['admin_url']; ?>options/"><?php _e('Misc Options'); ?></a>
            <a href="<?php echo $mp_options['root_url']; ?>"><?php printf(__('Return to %s'), $mp_options['site_name']); ?></a>
        </nav>
        <?php echo $content; ?>
    </div>
</div>

<?php get_template_part('footer'); ?>