<?php

/**
 * The plugin bootstrap file
 *
 * WordPress reads this file to generate the plugin information in the plugin
 * admin area. This file also includes all the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://leira.dev
 * @since             1.0.0
 * @package           Leira_Auth
 *
 * @wordpress-plugin
 * Plugin Name:       Leira Auth
 * Plugin URI:        https://wordpress.org/plugins/leira-auth/
 * Description:       Allow your users to authenticate in your system from the frontend. Customize the look and feel of the frontend login, forgot, and reset password forms.
 * Version:           1.0.0
 * Author:            Ariel
 * Author URI:        https://leira.dev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       leira-auth
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'LEIRA_AUTH_VERSION', '1.0.0' );


/**
 * Register the plugin's autoloader
 */
require_once plugin_dir_path( __FILE__ ) . 'includes' . DIRECTORY_SEPARATOR . 'autoload.php';

/**
 * Helper method to get the main instance of the plugin
 *
 * @return Leira_Auth\Includes\Plugin
 * @since    1.0.0
 * @access   global
 */
function leira_auth() {
	return Leira_Auth\Includes\Plugin::instance();
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
leira_auth()->run();
