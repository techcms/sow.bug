<?php
/*
 *
 *  PLEASE NOTE THAT BY DEFAULT - FRONT-END ADMIN THEMES ARE NOT ACTIVE
 *  TO ACTIVATE FRONT-END ADMIN THEMES - YOU NEED TO RENAME THIS FILE
 *  IF THIS FILE IS CALLED "index.php" IT WILL BE ACTIVATED
 *  ONCE ACTIVATED, ALL ADMIN FUNCTIONS WILL RUN FROM THE THEME
 *
*/
get_template_part('admin/functions');
mp_themed_admin_page('dashboard');
get_template_part('footer');