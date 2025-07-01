<?php
/**
 * The base configuration for WordPress - Hostinger Production
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - Get these from your Hostinger control panel ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u993113260_miriam' );

/** Database username */
define( 'DB_USER', 'u993113260_elhanan' );

/** Database password */
define( 'DB_PASSWORD', 'Nani5276@' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 * Generate new ones for production: https://api.wordpress.org/secret-key/1.1/salt/
 */
define('AUTH_KEY',         '=iX.- rn<]5j>W X1tR]:M&(e~ 9jT*A[M)wOSHo)--^Ee]/TnIGF:v6sh6q[jj4');
define('SECURE_AUTH_KEY',  '<WL`lKh8,|jQ ,)M>n=hz6YJ6YBjg%A$.euoablNcC$A=1`GSE[ak)R%(cr1 DI*');
define('LOGGED_IN_KEY',    '7os8ky!kO^|<{o+TW]_B M;Hbjn+L~m-V}bL-p(t,05bWG?},-|.t6FuY8{(LCLN');
define('NONCE_KEY',        'cS_;b6CR <pKLrs>:WNDn*3,G.gQ~Hi82@xvvmEQTwKW$ej7ysbw,StvtoiP/t)n');
define('AUTH_SALT',        'ScEAj<hjapX6^;_Q.~c4nvK#st-I#`!p~s,wI,6,94CkmD]<^Zyvl|~sy6UQsBTn');
define('SECURE_AUTH_SALT', '2o2:PSGz!gF{n8Z9QMec`PqC+4)gVvg|DLZSdWo>pwAC^`a0D&2o0/2QZR4S=B|d');
define('LOGGED_IN_SALT',   '2[H,1:2]^_[Q.8kP6v]D+6oDK|PYjDfuY4l{i0k%t]+dp/ETrU|Akv#107!( g,X');
define('NONCE_SALT',       'V$7}^ IYgt,{wA>sr^f1:S*T?<(gJw^zd;t.GG8Y~(41Pp:0,O9G #=.l)E|$p5=');
/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * WordPress site URLs - Update these to your domain
 */
define( 'WP_HOME', 'https://miriamkryshevski.com/' );
define( 'WP_SITEURL', 'https://miriamkryshevski.com/' );

/**
 * Security settings
 */
define( 'DISALLOW_FILE_EDIT', true );
define( 'WP_POST_REVISIONS', 3 );
define( 'AUTOMATIC_UPDATER_DISABLED', false );

/**
 * Performance optimizations
 */
define( 'WP_MEMORY_LIMIT', '256M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );

/**
 * For developers: WordPress debugging mode.
 * Set to true temporarily to debug issues
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', true );

define( 'WP_ENVIRONMENT_TYPE', 'production' );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
