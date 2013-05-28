<?php
/* 

DEAR USER - NEVER EVER EVER LET ANYONE HAVE ACCESS TO THESE VALUES 

DO NOT COPY AND PASTE THESE INTO FORUMS - DO NOT EMAIL TO UNTRUSTED PEOPLE - KEEP IN A SECURE FOLDER (mp-settings)

*/

define('DB_USERNAME', '' );
define('DB_PASSWORD', '' );

// GENERATE NEW SITE SALT - http://mongopress.com/salt-generation.php
// PICK ANY TWO RANDOMLY GENERATED VALUES OR SIMPLY ADD YOUR OWN UNIQUE SALTS
define('SITE_SALT','VERY_SECRET_RANDOM_STRING');
define('COOKIE_SALT','NOT_SECRET_RANDOM_STRING');

define('NONCE_PRIVATE_TTL','3600'); // roughly the time we allow for making actions in the admin panel - 1 hour
define('NONCE_PUBLIC_TTL','86400');  // the time we allow for public actions - like media, logins. - 1 day