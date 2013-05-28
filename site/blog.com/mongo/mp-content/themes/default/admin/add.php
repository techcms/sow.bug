<?php
/*
 *
 *  PLEASE NOTE THAT BY DEFAULT - FRONT-END ADMIN THEMES ARE NOT ACTIVE
 *  TO ACTIVATE FRONT-END ADMIN THEMES - YOU NEED TO RENAME index-sample.php
 *  IF THIS FILE IS CALLED "index.php" IT WILL BE ACTIVATED
 *  ONCE ACTIVATED, ALL ADMIN FUNCTIONS WILL RUN FROM THE THEME
 *
*/
get_template_part('admin/functions');
mp_themed_admin_page('add');
get_template_part('footer');