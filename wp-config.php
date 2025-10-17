<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

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
define( 'AUTH_KEY',         'r((5a{~E&ut?kLzOebR*x%Q.rN7SlTT/SCo9_QnzO30we U}@#.CPBz~T-KGAi-m' );
define( 'SECURE_AUTH_KEY',  'hJ]79lPtF3wF: E&H*Cx9)PO-r1]!D27^MExkjNk}g]jl%X&$_7c[99N_1A#?dMx' );
define( 'LOGGED_IN_KEY',    'kit@baAS )=Z&dLP7|43% #* +f3bU#j-{%{}NDpHpcgLGgx+bZ3}U)[U3OnThw+' );
define( 'NONCE_KEY',        '{7DcdSurOIHFHpl@wT^V%7P%kw~KgKb{,j>G6|S3ao_>`NW(k44y@iP~u>TGJyNi' );
define( 'AUTH_SALT',        'l2V3]Dhpgt<-+b=b?83Yh-t9/3 2q@L!{?v[xJ$?oy0=>/@+2S{998hP0fJ^mQyx' );
define( 'SECURE_AUTH_SALT', 'X4}clF:i x>Fr:NQz**7r(8*XEYs15!B[ :r)fkLM8EZ[T:q~y_`0sG{Y#$~xr~+' );
define( 'LOGGED_IN_SALT',   'd2!LrAEa,(hTC&+cs^6 KQF3|fSHsM.;Ye[EaK#y!l7HmLPz?k9wa5S>wDHrz>Ry' );
define( 'NONCE_SALT',       'fZ,Da2VqZXb)eu{29o~]-fULX!}==(X;cKw)U Tpw1u_Woy41IHV&f+}iK0E!}`q' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'dku_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
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

