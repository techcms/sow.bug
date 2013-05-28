<?php
/* 

DEAR USER - NEVER EVER EVER LET ANYONE HAVE ACCESS TO THESE VALUES 

DO NOT COPY AND PASTE THESE INTO FORUMS - DO NOT EMAIL TO UNTRUSTED PEOPLE - KEEP IN A SECURE FOLDER (mp-settings)

*/

define('DB_USERNAME', '{%mongodb_username%}' );
define('DB_PASSWORD', '{%mongodb_password%}' );

// GENERATE NEW SITE SALT - http://mongopress.com/salt-generation.php
// PICK ANY TWO RANDOMLY GENERATED VALUES OR SIMPLY ADD YOUR OWN UNIQUE SALTS
define('SITE_SALT','{%site_salt%}');
define('COOKIE_SALT','{%cookie_salt%}');

define('NONCE_PRIVATE_TTL','{%private_ttl%}'); // roughly the time we allow for making actions in the admin panel - 1 hour
define('NONCE_PUBLIC_TTL','{%public_ttl%}');  // the time we allow for public actions - like media, logins. - 1 day
