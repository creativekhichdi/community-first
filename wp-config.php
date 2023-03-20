<?php
/**
 * The base configuration for WordPress
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
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'new-community' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '._[;7iJ?0L5_zs^8{OmM]9wFe.}+DA}5!2_p^: XD%+A}d4nWsqJb`qz50.f:~^0' );
define( 'SECURE_AUTH_KEY',  'OqDy1&[!Q`KJ,gl1q8r?C_Thu[3pG8h]@9!}1V*EHHOp<d}59A4MGHET.G8&[-S_' );
define( 'LOGGED_IN_KEY',    'S:J5EiM.4j;-I{N10N0dx/:-[I%AMB99I~vOy2I `aBJ*>HgC;Au$a{}C$AV]oB~' );
define( 'NONCE_KEY',        ';&Gon7/i2 KE6`=7XrH`H_;:B{N`kpe^C5BnO}&bg*NDO9:&476,gO`pZzgx_CA0' );
define( 'AUTH_SALT',        'TJ0g>Wp~cSu*n_=?M>LfK|ztIc(iNeRJEIZ$b@[FotqC[[m:PQ2B&ev^@8RlQ^I4' );
define( 'SECURE_AUTH_SALT', 'e0ftcH:@4bYiYx@^9y-)=$IQ>IWAIbds-9uKY`9E3AN5Y5@.J7DUkZc4Y+I/_9K7' );
define( 'LOGGED_IN_SALT',   '6:J[G`KVIJB_l_wkPj1jXYyvF,A}lxrUR?Tn./G`6HHR{4H%)P,dP($NvdU,5Bn~' );
define( 'NONCE_SALT',       'U^9s26!(3Y}?V~r`q(y3M45UP~Wv,+um7 eKET;EvrK1_TP^^_($R4(y4$sRfq0!' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
