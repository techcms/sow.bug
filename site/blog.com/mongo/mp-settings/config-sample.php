<?php

/* THESE ARE OUR OPTIONS / CONFIGURATION SETTINGS */
define('MONGODB_HOST','');
define('MONGODB_NAME', 'mongopress' );
define('SITE_NAME', 'MongoPress' );
define('SITE_DESCRIPTION', 'The High-Performance, Object-Based NoSQL CMS' );
define('BASE_URL_DIRECTORY', '' );
define('DB_PORT', '27017' );
define('COLLECTION_PREFIX',''); // Allows multiple installations on one DB
/* END OF OPTIONS */

/* AT PRESENT - THIS IS THE ONLY WAY TO SWITCH THEMES */
define('MONGOPRESS_THEME','default');

// RESERVED NAMESPACES FOR PERMA QUERIES AND SEARCHES
define('QUERY_PERMA','mp');
define('SEARCH_PERMA','search');

/* TO DEBUG OR NOT TOP DEBUG - THAT IS THE QUESTION */
define('MP_DEBUG',false);

// MISC OPTIONS THAT WILL SOON HAVE AN INTERFACE
define('SKIP_HT',false);
define('OBJS_PP',25);
define('MONGODB_REPLICAS',false);
define('COOKIE_TTL','session'); // session/permanent or use a integer in seconds for inactivity, 7200 = 2hrs

// CUSTOM SLUGS
define('ADMIN_SLUG','admin');
define('MEDIA_SLUG','media');

