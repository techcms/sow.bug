<?php
add_action('mp_default_sidebar_top','download_counter_widget');
add_action('mp_content_block_content_article_download_counter','mp_content_block_content_article_download_counter_options',1,5);
function mp_content_block_content_article_download_counter_options($content,$type,$id,$class,$href){
    $mp = mongopress_load_mp();
    $these_options = array(
        'action'    => 'get',
        'key'       => $id
    );
    $plugin_options = $mp->plugin_options($these_options);
    $this_media_id = $plugin_options['media_id'];
    $this_start = $plugin_options['start'];
    $this_intro = $plugin_options['intro'];
    $this_extra = $plugin_options['extra'];
    ob_start();
    ?>
    <form id="plugin-<?php echo $id; ?>" class="mp-form mu-plugins" data-plugin="download_counter">
        <label for="object_id_<?php echo $id; ?>"><?php _e('ZIP to Download'); ?></label>
        <?php mp_media_dropdown('object_id_'.$id,false,'these_values','data-key="media_id"',$this_media_id,'download'); ?>
        <label for="start_<?php echo $id; ?>"><?php _e('Start Count'); ?></label>
        <span class="input-wrapper radius5">
            <input class="blanked these_values" id="start_<?php echo $id; ?>" data-key="start" name="start_<?php echo $id; ?>" placeholder="<?php _e('If upgrading the Media ID you can start from a specific view or download count'); ?>" value="<?php echo $this_start; ?>" />
        </span>
        <label for="extra_<?php echo $id; ?>"><?php _e('Intro Text'); ?></label>
        <span class="input-wrapper radius5">
            <textarea class="blanked these_values" id="intro_<?php echo $id; ?>" data-key="intro" name="intro_<?php echo $id; ?>" placeholder="<?php _e('Add additional before the download link / counter'); ?>"><?php echo $this_intro; ?></textarea>
        </span>
        <label for="extra_<?php echo $id; ?>"><?php _e('Extra HTML After Widget'); ?></label>
        <span class="input-wrapper radius5">
            <textarea class="blanked these_values" id="extra_<?php echo $id; ?>" data-key="extra" name="extra_<?php echo $id; ?>" placeholder="<?php _e('Add additional text after the download counter'); ?>"><?php echo $this_extra; ?></textarea>
        </span>
        <input type="submit" id="submit_<?php echo $id; ?>" class="button" value="<?php _e('Save Plugin Settings'); ?>" />
        <?php mp_nonce_field('plugin-options','mp_nonce'); ?>
    </form>
	<div style="clear:both; display: block;"></div>
    <?php
    $options = ob_get_clean();
    return $options;
}
function download_counter_widget(){
	// All this is loaded - on every page? - even when the download is not enabled.
    $m = mongopress_load_m();
    $mp = mongopress_load_mp();
    $options = $mp->options();
    $db = $m->$options['db_name'];
    $these_options = array(
        'action'    => 'get',
        'key'       => 'download_counter'
    );
    $plugin_options = $mp->plugin_options($these_options);
    $this_media_id = $plugin_options['media_id'];
    $this_start = $plugin_options['start'];
    $this_intro = $plugin_options['intro'];
    $this_extra = $plugin_options['extra'];
    $grid = $db->getGridFS();
    $mp_media_object_mongo_id = new MongoID($this_media_id);
    $gridded_media = $mp->arrayed($db->fs->files->find(array("_id"=>$mp_media_object_mongo_id)));
    $filename = $gridded_media[0]['filename'];
    $widget_title = sprintf(__('Download %s'),$filename);
    $widget_intro = __('AJAX Rocket Ready to Launch - Are You...?');
    $button_contents = '<span>'.sprintf(__('Download<br />%s'),$filename).'</span>';
    $rocket_contents = sprintf(__('Download %s'),$filename);
    $button_id = 'mp_download_button';
    $rocket_id = 'mp_download_rocket';
    $use_rocket = true;
    if(!empty($gridded_media)){
    	$nonce = mp_create_nonce('media_view');
        mp_enqueue_style_theme('download_counter', $options['root_url'].'mp-content/mu-plugins/download_counter/download_counter.css');
        ?>
        <div class="widget first_child">
            <h3 class="widget-title header"><?php echo $widget_title; ?></h3>
            <p class="widget-commentary"><?php echo $this_intro; ?></p>
            <p class="download-button">
                <?php mp_get_media($nonce, $this_media_id, 'download', $button_contents, $button_id, false, $this_start); ?>
                <?php 
                if($use_rocket){
                    mp_get_media($nonce, $this_media_id, 'download', $rocket_contents, $rocket_id, false, $this_start);
                }
                ?>
            </p>
            <p class="download-counter"><?php printf(__('downloaded %s times and counting'),number_format((string)$gridded_media[0]['downloads'])); ?></p>
            <?php if($this_extra){ echo $this_extra; } ?>
        </div>
        <?php
    }
}
