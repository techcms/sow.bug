<?php
add_action('mp_default_theme_header','default_theme_header_switcher');
add_action('mp_content_block_content_article_default_header','mp_content_block_content_article_default_header_options',1,5);
function mp_content_block_content_article_default_header_options($content,$type,$id,$class,$href){
    $mp = mongopress_load_mp();
    $these_options = array(
        'action'    => 'get',
        'key'       => $id
    );
    $plugin_options = $mp->plugin_options($these_options);
    $this_media_id = $plugin_options['media_id'];
    $this_href = $plugin_options['href'];
    ob_start();
    ?>
    <form id="plugin-<?php echo $id; ?>" class="mp-form mu-plugins" data-plugin="default_header">
        <label for="object_id_<?php echo $id; ?>"><?php _e('Header Image'); ?></label>
        <?php mp_media_dropdown('object_id_'.$id,false,'these_values','data-key="media_id"',$this_media_id,'image'); ?>
        <label for="href_<?php echo $id; ?>"><?php _e('URL for Header Link'); ?></label>
        <span class="input-wrapper radius5">
            <input class="blanked these_values" id="href_<?php echo $id; ?>" data-key="href" name="href_<?php echo $id; ?>" placeholder="<?php _e('Add a URL if you would like the header image to act as a link'); ?>" value="<?php echo $this_href; ?>" />
        </span>
        <input type="submit" id="submit_<?php echo $id; ?>" class="button" value="<?php _e('Save Plugin Settings'); ?>" />
        <?php mp_nonce_field('plugin-options','mp_nonce'); ?>
    </form>
	<div style="clear:both; display: block;"></div>
    <?php
    $options = ob_get_clean();
    return $options;
}

function default_theme_header_switcher(){
    $m = mongopress_load_m();
    $mp = mongopress_load_mp();
    $options = $mp->options();
    $db = $m->$options['db_name'];
    $these_options = array(
        'action'    => 'get',
        'key'       => 'default_header'
    );
    $plugin_options = $mp->plugin_options($these_options);
    $this_media_id = $plugin_options['media_id'];
    $this_href = $plugin_options['href'];
    $mp_media_object_mongo_id = new MongoID($this_media_id);
    $gridded_media = $mp->arrayed($db->fs->files->find(array("_id"=>$mp_media_object_mongo_id)));
    if($this_href){ echo '<a href="'.$this_href.'" '.mp_get_attr_filter('default_header.php','a',$this_href,'','','style="outline:0"').'>'; }
    if(!empty($gridded_media)){
    	$nonce = mp_create_nonce('media_view');
        mp_get_media($nonce, $this_media_id,'image',false,false,'logo');
    }else{
        //$images = mongopress_load_base64s();
        ?>
        <img src="<?php echo $options['root_url'].'mp-admin/images/mp-logo-wide.png'; ?>" id="default-logo" class="logo" <?php mp_attr_filter('default_header.php','img','mp-admin/images/mp-logo-wide.png','default-logo','logo',''); ?> />
        <?php
    } if($this_href){
        echo '</a>';
    }
}
