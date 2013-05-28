<?php
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

/* CHECK NONCE */
if(isset($_POST['nonce'])){ $nonce = sanitize_text_field($_POST['nonce']); }else{ $nonce = false; };
mp_json_nonce_check($nonce,'fetch_feed');

/* GET ATTRIBUTES */
if(isset($_POST['url'])){ $url = sanitize_text_field($_POST['url']); }else{ $url = 'http://labs.laulima.com/activity/feed/'; };
if(isset($_POST['limit'])){ $limit = (int)sanitize_text_field($_POST['limit']); }else{ $limit = 10; };

/* START TO CAPTURE CONTENTS */
ob_start();
@mp_fetch_feed($url, $limit);
$feed = ob_get_clean();

/* SEND BACK CONTENT */
echo $feed;