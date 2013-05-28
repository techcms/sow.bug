<?php
add_action('mp_footer','mp_analytics_footer');
add_action('mp_after_admin_footer','mp_analytics_footer');
add_action('mp_content_block_content_article_analytics_footer','mp_content_block_content_article_analytics_footer_options',1,5);
function mp_content_block_content_article_analytics_footer_options($content,$type,$id,$class,$href){
    $mp = mongopress_load_mp();
    $these_options = array(
        'action'    => 'get',
        'key'       => $id
    );
    $plugin_options = $mp->plugin_options($these_options);
    $this_google_id = $plugin_options['google_id'];
    ob_start();
    ?>
    <form id="plugin-<?php echo $id; ?>" class="mp-form mu-plugins" data-plugin="analytics_footer">
        <label for="object_id_<?php echo $id; ?>"><?php _e('Google Analytics ID'); ?></label>
        <span class="input-wrapper radius5">
            <input class="blanked these_values" id="object_id_<?php echo $id; ?>" data-key="google_id" name="object_id_<?php echo $id; ?>" placeholder="<?php _e('Please manually add the relevant Object ID'); ?>" value="<?php echo $this_google_id; ?>" />
        </span>
        <input type="submit" id="submit_<?php echo $id; ?>" class="button" value="<?php _e('Save Plugin Settings'); ?>" />
        <?php mp_nonce_field('plugin-options','mp_nonce'); ?>
    </form>
	<div style="clear:both; display: block;"></div>
    <?php
    $options = ob_get_clean();
    return $options;
}
function mp_analytics_footer(){
    $mp = mongopress_load_mp();
    $these_options = array(
        'action'    => 'get',
        'key'       => 'analytics_footer'
    );
    $plugin_options = $mp->plugin_options($these_options);
    $this_google_id = $plugin_options['google_id'];
    if(!empty($this_google_id)){
        ?>
        <script type="text/javascript">
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', '<?php echo $this_google_id; ?>']);
            _gaq.push(['_trackPageview']);
            (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();
        </script>
        <?php
    }
}