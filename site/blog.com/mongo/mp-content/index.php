<?php
require_once(dirname(dirname(__FILE__)).'/config.php');
$mp = mongopress_load_mp();
$options = $mp->options();
$root_url = $options['root_url'];
echo '<script>window.location="http://'.$_SERVER['HTTP_HOST'].$options['root_url'].'"</script>';