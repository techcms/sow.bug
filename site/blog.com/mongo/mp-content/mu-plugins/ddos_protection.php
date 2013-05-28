<?php
add_action('mu_plugins_init','ddos_protection_rules');
add_action('mp_content_block_content_article_ddos_protection','mp_ddos_protection',1,5);

function mp_ddos_protection($id){
    $mp = mongopress_load_mp();
    $these_options = array(
        'action'    => 'get',
        'key'       => $id
    );
    $plugin_options = $mp->plugin_options($these_options);
    $this_enable = $plugin_options['ddos_protection'];
    ob_start();
    ?>
    <form id="plugin-<?php echo $id; ?>" class="mp-form mu-plugins" data-plugin="ddos_protection">
        <label for="protection_<?php echo $id; ?>"><?php _e('Enable DDOS protection'); ?></label> 
	<span class="input-wrapper radius5">
            <select name='protection_<?php echo $id; ?>' class='blanked these_values' data-key='ddos_protection' id='protection_<?php echo $id ?>' autocomplete="off">
                <option></option>
                <?php
                if(empty($this_enable)){ $this_enable = 'No'; }
                if ($this_enable == 'No') {
                    echo "<option value='No' selected>No</option>";
                    echo "<option value='Yes'>Yes</option>";
                } else {
                    echo "<option value='Yes' selected>Yes</option>";
                    echo "<option value='No'>No</option>";
                }
                ?>
            </select>
        </span>
	<input type="submit" id="submit_<?php echo $id; ?>" class="button" value="<?php _e('Save Plugin Settings'); ?>" />
        <?php mp_nonce_field('plugin-options','mp_nonce'); ?>
    </form>
	<div style="clear:both; display: block;"></div>
    <?php
    $options = ob_get_clean();
    return $options;
}



function ddos_protection_rules() {
    $mp = mongopress_load_mp();
    $these_options = array(
        'action'    => 'get',
        'key'       => 'ddos_protection'
    );

    $plugin_options = $mp->plugin_options($these_options);
    $this_enable = $plugin_options['ddos_protection'];

    if ($this_enable == 'yes') {
	//-------------------- start ddos protection setting -----------------------
     	// Put this to high number for live installation!
	$itime = 10;  // minimum number of seconds between one-visitor visits
     	$imaxvisit = 100;  // maximum visits in $itime x $imaxvisits seconds
	$banned = ($itime * $imaxvisit);  // minutes to banned

	// Get SHM dir first - if not - then use temp. -- also consider using -- if php5.3 (?) then shmop functions??

	$iplogdir = sys_get_temp_dir(); //detect php temp dir. on linux normally /tmp and windows C:\Windows\Temp\


	//---------------------- End of Initialization ---------------------------------------

	// - 4096 vs. -4 ? - ~16,000 possible implications of large numbers of files in the filesystem and the IO performance hit.

  	$ipfile = substr(md5($_SERVER["REMOTE_ADDR"]),-3);  // -3 means 4096 possible files
  	$oldtime = 0;
  	if (file_exists($iplogdir.'/'.$ipfile)) $oldtime = filemtime($iplogdir.'/'.$ipfile);

  	//Update times:
  	$time = time();
  	if ($oldtime<$time) $oldtime=$time;
  	$newtime = $oldtime+$itime;
  	
	//Check human or bot:
  	if ($newtime >= $time + $itime * $imaxvisit) {
    	//To block visitor:
    		touch($iplogdir.'/'.$ipfile,$time+$itime*($imaxvisit-1)+$banned);
    		clearstatcache();
		ob_start();
    		header("HTTP/1.0 503 Service Temporarily Unavailable");
    		header("Connection: close");
		ob_end_flush(); 

    		//don't send any email or writing log here because we don't want to flood our mailserver or hdd I/O
    		exit();
  	}

  	//Modify file time:
  	touch($iplogdir.'/'.$ipfile,$newtime);   
    }
}


//affandy - provide sys_get_temp_dir if on php < 5.2.1
if (!function_exists('sys_get_temp_dir')) {
  function sys_get_temp_dir() {
    // Try to get from environment variable
    if (!empty($_ENV['TMP'])) {
      return realpath($_ENV['TMP']);
    }
    else if (!empty($_ENV['TMPDIR'])) {
      return realpath($_ENV['TMPDIR']);
    }
    else if (!empty($_ENV['TEMP'])) {
      return realpath($_ENV['TEMP']);
    } else {
      // Detect by creating a temporary file
      // Try to use system's temporary directory
      // as random name shouldn't exist

        // this is BUGGY!
      $temp_file = tempnam(md5(uniqid(rand(), TRUE)), '');
      if ($temp_file) {
        $temp_dir = realpath(dirname($temp_file));
        unlink($temp_file);
        return $temp_dir;
      }
      else {
        return FALSE;
      }
    }
  }
}
