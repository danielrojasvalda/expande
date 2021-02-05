<?php
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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'fhijthzh_wp436' );

/** MySQL database username */
define( 'DB_USER', 'fhijthzh_wp436' );

/** MySQL database password */
define( 'DB_PASSWORD', '-pd6K1[So3' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'ansotjkwdvdqecbdd87kc9gky63qt609wmvllsee3omgdj0aroz8ujottdkglvhw' );
define( 'SECURE_AUTH_KEY',  'tp4peyusz9yv9bhqnlykn156enqwt6haignjkl2hmwsf5ow77bqcpunzs6wf7idz' );
define( 'LOGGED_IN_KEY',    '8jwqe1nvfhygflucydxx0713wzdqckazgy3sn98wx2dnaugchmxbk1ayyimrl8rf' );
define( 'NONCE_KEY',        '7q0elwwseodmguj4yqgjv7ixoe5nuk1t4q7rtaecc84yybgev2rmkpgn9b15uagp' );
define( 'AUTH_SALT',        'pc02squ07ifmkaus9sudu6sa52edrc5uigfsqy7yzrmlsfrx7kfirt9lxnxgmqdd' );
define( 'SECURE_AUTH_SALT', 'yjp3kitk5osxx8itzgcz0zbh1hm8stwwtsjfnnjd619oahbtz55wadm0bb81wyik' );
define( 'LOGGED_IN_SALT',   'ynlxmzj6fcvxjo7fh8dlchsdknfh1ooyil7ybvhqu4mppqlmismuydtowt63tjut' );
define( 'NONCE_SALT',       'mhydiqnn6yghe936hcmup15d6ztnx5o54egmjcjlorwxgudqykieceytgxjvyjkf' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpjb_';

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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
