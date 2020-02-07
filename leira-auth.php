<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
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
 * Description:       Allow your users to authenticate in your system from the frontend. Customize the look and feel of the frontend login, forgot and reset password forms.
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
 * The code that runs during plugin activation.
 * This action is documented in includes/class-leira-auth-activator.php
 */
function activate_leira_auth() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-leira-auth-activator.php';
	Leira_Auth_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-leira-auth-deactivator.php
 */
function deactivate_leira_auth() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-leira-auth-deactivator.php';
	Leira_Auth_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_leira_auth' );
register_deactivation_hook( __FILE__, 'deactivate_leira_auth' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-leira-auth.php';

/**
 * Helper method to get the main instance of the plugin
 *
 * @return Leira_Auth
 * @since    1.0.0
 * @access   global
 */
function leira_auth() {
	return Leira_Auth::instance();
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
