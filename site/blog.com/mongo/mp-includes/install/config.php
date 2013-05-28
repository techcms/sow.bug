<?php

/* THESE ARE OUR OPTIONS / CONFIGURATION SETTINGS */
define('MONGODB_HOST','{%mongodb_server%}');
define('MONGODB_NAME', '{%mongodb_name%}' );
define('SITE_NAME', '{%site_name%}' );
define('SITE_DESCRIPTION', '{%site_description%}' );
define('BASE_URL_DIRECTORY', '{%base_url_dir%}' );
define('DB_PORT', '{%mongodb_port%}' );
define('COLLECTION_PREFIX','{%collection_prefix%}');

/* END OF OPTIONS */

/* AT PRESENT - THIS IS THE ONLY WAY TO SWITCH THEMES */
define('MONGOPRESS_THEME','{%mongopress_theme%}');

// RESERVED NAMESPACES FOR PERMA QUERIES AND SEARCHES
define('QUERY_PERMA','{%query_perma%}');
define('SEARCH_PERMA','{%search_perma%}');

/* TO DEBUG OR NOT TOP DEBUG - THAT IS THE QUESTION */
define('MP_DEBUG',{%mp_debug%});

// MISC OPTIONS THAT WILL SOON HAVE AN INTERFACE
define('SKIP_HT',{%skip_ht%});
define('OBJS_PP',{%objs_pp%});
define('MONGODB_REPLICAS',{%mongodb_replicas%});
define('COOKIE_TTL','{%cookie_ttl%}'); // session/permanent or use a integer in seconds for inactivity, 7200 = 2hrs

// CUSTOM SLUGS
define('ADMIN_SLUG','{%admin_slug%}');
define('MEDIA_SLUG','{%media_slug%}');

?>
