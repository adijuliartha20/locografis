<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'locografis-live');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'uSrNsT<#:C(CTHqrF+HqIwhaky }2/PBrS~R50K32fx;=~EXrdL!NzL6$mfev`4)');
define('SECURE_AUTH_KEY',  'v;!u|g/Da-j&S&M-kJn:7FG#WlJbbhq=QKSi~+WLZXPE5Z7kFAbB<O6l]i7W)F[1');
define('LOGGED_IN_KEY',    'U)8.1H.}mwH$4b9oQ2<{pqxcvG)5B&@>.^Fo6]3]a(sw9^!~j&UQmP6~LvQVr-=.');
define('NONCE_KEY',        '%=*Y9,xc|q?M[N~s?m?g{_#pK>~^SWzsvHP(JXFXm@=}k#%-te,1Fj<<l0m>Ko5j');
define('AUTH_SALT',        'mp |$*J/s_3R]vlvU3eXs8|zSryA+)7p|{98mtd}:7{-:5F6.;8kE8KKdL,4@]L~');
define('SECURE_AUTH_SALT', 'A^Yj.rlO.N_`MWETaEXqKWFuM{q.]DCg5IYhUSz#=|KK=f]p?x+-U+z93YrOV+qe');
define('LOGGED_IN_SALT',   'KEBW{=Dd*L31YNPH/tX=PiXXw}4ub,5=~>Kxg#0dZw:*Zi5 BA8o_MCq*K4F~0}e');
define('NONCE_SALT',       'n-?A(exFFxaf?$bBus%/&*%:;)yV+pyg5E%P|~)tc,`nu:PO]T?s(|yXHp#{ 9o9');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
