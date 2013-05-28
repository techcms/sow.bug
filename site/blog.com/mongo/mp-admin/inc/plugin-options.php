<?php
global $mp_plugins;
if(is_array($mp_plugins['mu'])){
    echo '<p style="padding-top:15px;">'.__('You currently have the following <strong>mu-plugins</strong> installed and activated:').'</p>';
    echo '<div class="select_wrapper">';
        echo '<select id="mp-mu-plugins" autocomplete="off" name="mp_mu_plugins">';
            echo '<option value="none">'.__('--- --- Select Plugin --- ---').'</option>';
            foreach($mp_plugins['mu'] as $mu_plugin => $use){
                if($use){
                    $friendly_name = sanitize_title_with_dashes(substr($mu_plugin, 0, -4));
                    echo '<option value="'.$friendly_name.'">'.$friendly_name.'</option>';
                }
            }
        echo '</select>';
    echo '</div>';
    foreach($mp_plugins['mu'] as $mu_plugin => $use){
        if($use){
            $friendly_name = sanitize_title_with_dashes(substr($mu_plugin, 0, -4));
            echo mp_content_block('article',$friendly_name,'hidden mu-plugin-options-panel',false,false);
        }
    }
}else{
    echo '<p style="padding-top:15px;">'.__('You do not have any valid <strong>mu-plugins</strong> accessible. Once valid plugins are added to the mu-plugins folder and if those plugins have options, they will show-up here.').'</p>';
}
?>