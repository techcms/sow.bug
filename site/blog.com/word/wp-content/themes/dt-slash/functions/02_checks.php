<?php
	
	function dt_admin_notice(){
		$msg = realpath(TMP_DIR2). '/uploads/dt-cache'.  __( ' could not be created. Please create it manually with user rights 777 (read/write access enabled).', LANGUAGE_ZONE );
		echo '<div class="error"><p>'. $msg. '</p></div>';
	}
	
   $realpath = realpath(TMP_DIR);
   $realpath = preg_replace('/^.*(wp-content\/.*)$/', '\\1/', $realpath);

   // Check if cache is writable
   if ( !file_exists( TMP_DIR ) )
   {
      @mkdir( TMP_DIR );
      @chmod( TMP_DIR, 0777 );
   }
   
   if ( !file_exists( TMP_DIR ) )
   {
      add_action('admin_notices', 'dt_admin_notice');
   }
   
   

?>
