<?php

namespace Leira_Auth\Includes;

use Leira_Auth\Admin\Settings;
use Leira_Auth\Public\Controller;
use Leira_Auth\Public\Forms\Factory;
use Leira_Auth\Public\Login;
use Leira_Auth\Public\Flash;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Leira_Auth
 * @subpackage Leira_Auth/includes
 * @author     Ariel <arielhr1987@gmail.com>
 *
 * @property Settings $settings The admin settings
 * @property Login $login The login form
 * @property Factory $forms The forms factory
 * @property Flash $flash The Flash instance
 */
class Plugin{

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Singleton instances
	 *
	 * @since    1.0.0
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * Instances
	 *
	 * @var array
	 */
	protected $instances = array();

	/**
	 * The Singleton method
	 *
	 * @return self
	 * @since  1.0.0
	 * @access public
	 */
	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function __construct() {
		if ( defined( 'LEIRA_AUTH_VERSION' ) ) {
			$this->version = LEIRA_AUTH_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'leira-auth';
	}

	/**
	 * Register all the hooks related to the admin area functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	protected function define_admin_hooks() {

		if ( is_admin() ) {
			$this->settings = new Settings();

			add_action( 'admin_menu', [ $this->settings, 'admin_menu' ] );

			add_action( 'admin_init', [ $this->settings, 'register_settings' ] );
		}

//		$plugin_admin = new Leira_Auth_Admin( $this->get_plugin_name(), $this->get_version() );
//		$this->loader->set( 'admin', $plugin_admin );
//
//		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
//
//		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all the hooks related to the public-facing functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	protected function define_public_hooks() {

		add_action( 'init', [ $this, 'load_plugin_textdomain' ] );

		$login = new Login( 'login' );
		//Register block
		//add_action( 'init', array( $login, 'init' ) );
		//add_action( 'enqueue_block_editor_assets', array( $login, 'enqueue_block_editor_assets' ) );
		//Shortcode for the custom login form
		//TODO: use correct shortcode name
		//add_shortcode( 'custom_login_form', array( $login, 'shortcode' ) );
		//Handle login redirection, fires after a user login has failed.
		//add_action( 'wp_login_failed', array( $login, 'login_failed' ), 10, 2 );
		//Handle login errors and redirections on login failure
		//add_filter( 'wp_login_errors', array( $login, 'login_errors' ), 10, 2 );
		//
		//add_filter( 'authenticate', array( $login, 'verify_user_pass' ), 60, 3 );
		//Custom login form defaults
		//add_filter( 'login_form_defaults', array( $login, 'login_form_defaults' ) );
		//Show login error messages
		//add_filter( 'login_form_top', array( $login, 'login_form_top' ), 10, 2 );
		//Show forgot password and register links
		//add_filter( 'login_form_bottom', array( $login, 'login_form_bottom' ), 10, 2 );
		//Change login URL
		add_filter( 'login_url', array( $login, 'login_url' ), 10, 3 );

		$controller = new Controller();
		//Initialize class instances
		add_action( 'plugin_loaded', array( $controller, 'plugin_loaded' ) );
		//Register blocks
		add_action( 'init', array( $controller, 'init' ) );
		//Handle form submission
		add_action( 'wp', array( $controller, 'handle' ) );
		//one shortcode to rule them all
		add_shortcode( 'leira_auth', array( $controller, 'shortcode' ) );


//		$plugin_public = new Leira_Auth_Public( $this->get_plugin_name(), $this->get_version() );
//		$this->loader->set( 'public', $plugin_public );
//
//		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
//
//		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
//
//		$this->loader->add_action( 'init', $plugin_public, 'add_shortcodes' );
//
//		$this->loader->add_action( 'template_redirect', $plugin_public, 'submit' );


	}

	/**
	 * Run the loader to execute all the hooks with WordPress.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function run() {
		$this->define_public_hooks();
		$this->define_admin_hooks();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 * @access    public
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 * @access    public
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access    public
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			$this->get_plugin_name(),
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages/'
		);
	}

	/**
	 * Gets an instance from the loader
	 *
	 * @param  string  $key
	 *
	 * @return mixed|null The instance
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function __get( $key ) {
		return $this->instances[ $key ] ?? null;
	}

	/**
	 * Sets an instance in the loader
	 *
	 * @param  string  $key  The instance name
	 * @param  mixed  $value  The actual value to set
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function __set( $key, $value ) {
		$this->instances[ $key ] = $value;
	}

}
