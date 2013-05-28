<?php

function get_option($w)
{
   return 800;
}

function site_url()
{
   return '%%%%%%%%';
}

function default_attachment()
{
   return array(
      '/wp-content/themes/dt-slash/images/noimage.jpg',
      1000,
      1000,
   );
}

define("ABSPATH", dirname(__FILE__).'/../../../');

include "functions/01_defines.php";
include "functions/10_resize_images.php";

?>
