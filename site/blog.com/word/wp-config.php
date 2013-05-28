<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'word');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'UAp43bn:@_-D1gVFvwZ1Ev&a7$599.N|Ea1gbWjs(rOZL=-.-TZc}-:D nN|StC2');
define('SECURE_AUTH_KEY',  '<A9I?:Q_28|,|0FWO83Ga|zIQeX|@~)4hN)!i(OIff|dN_fNESXDSj7,|,k=,R3z');
define('LOGGED_IN_KEY',    'g.wMP^i(ORd;I4K#1@_|RxQ--4l={{R3rv2lQ%:HD#9qm-g|LP-+,t,@w3543Il6');
define('NONCE_KEY',        'vB0O8OnYj1X,qwn;@)_^$(yJtv8$iRU9|sQp0=q*<SWb~|i^*3_1GX;=_{ ]:Z4T');
define('AUTH_SALT',        'jFH}4/~%7dBd`f7+?[Eo*HH(l(qCF7mWFu/=gjrms]/[h+ZW,N4^TMvPBp5S?V]u');
define('SECURE_AUTH_SALT', '_WF*SN.{ a7q y7<1j&(i,~,UL+7M)wu(hH&p:c]?Qw0aFE!Ajz0qB}J$-WNATX]');
define('LOGGED_IN_SALT',   ';Zf,?r54|(W%^-k^k`. TIOK+G8ZJC9h:=@gw+L#c<_ITz5,?$sGpOQer039PSyS');
define('NONCE_SALT',       '9|e]wW@O6IA&;o.%$Yd-TWCr@I}Reut$OWA.Q]nCO2|:Sh*yy=Mb<6`mh{:|6!1<');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'word_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
  define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
