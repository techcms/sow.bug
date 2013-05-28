<?php
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

$mp = mongopress_load_mp();
$options = $mp->options();

$server = $_SERVER['HTTP_HOST'];
$domain_arr = explode(':', $server); // gets rid of ports! important in dev situations.
$cookie_domain = $domain_arr[0];

if (isset($_COOKIE)) {
    foreach($_COOKIE as $cookie_name => $cookie_value) {
	// only unset mongopress cookies - mp_..., use far past date for server time discrepencies vs. browser
	if (substr($cookie_name,0,3) == 'mp_') {
            // Note to Affandy - if changing cookie settings
            // Please double-check IE and Chrome on http://localhost/
            // These browsers cannot handle cookies on localhost
            /* THIS NEEDS TO BE RE-EVALUATED FOR 0.2
            setcookie($cookie_name, '', mktime(12,0,0,1, 1, 1980), '/',$cookie_domain);
            */
            setcookie($cookie_name, '', mktime(12,0,0,1, 1, 1980), '/');
	}
    }
}

$progress['success']=true;
mp_json_send($progress);