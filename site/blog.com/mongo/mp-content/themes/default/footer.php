<div id="site-credits">
    <?php 
    $mp = mongopress_load_mp(); 
    $mp_options = $mp->options();
    $site_description = $mp_options['site_description'];
    printf(__('Proudly Powered by <a href="http://mongopress.com">MongoPress</a> - %s - Developed by <a href="http://laulima.com">Laulima</a>'), $site_description);
    ?>
</div>
<?php mp_get_footer(); ?>