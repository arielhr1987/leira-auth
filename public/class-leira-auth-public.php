<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://leira.dev
 * @since      1.0.0
 *
 * @package    Leira_Auth
 * @subpackage Leira_Auth/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Leira_Auth
 * @subpackage Leira_Auth/public
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Auth_Public{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_filter( 'leira_auth_form_render', function( $form ) {
			/**
			 * @var Leira_Auth_Form $form
			 */
			$form->set_method( 'get' );
			$nodes = &$form->get_nodes();
			//$form->remove(0);
			//unset($nodes[0]);
			//unset($form->get_nodes()[0]);
			$the_node = false;
			foreach ( $nodes as $n ) {
				if ( $n->get_name() == 'rememberme' ) {
					$the_node = $n;
				}
			}
			$form->replace( 't', $the_node );
			$nodes = $form->get_nodes();
		} );

	}

	/**
	 *
	 */
	public function add_shortcodes() {

		add_shortcode( 'leira-auth-login', function() {
			require_once __DIR__ . '/test/class-leira-auth-form.php';

			$form = new Leira_Auth_Form();

			$f = new Leira_Auth_Form_Element( 'form' );
			$form->appendChild( $f );

			$f->setAttribute( 'action', '/' );

			$div = new Leira_Auth_Form_Element( 'div', 'Hello' );
			$f->appendChild( $div );

			$nodes = $form->query( 'div' );

			foreach ($nodes as $node){
				$str = $node->__toString();
			}

			return $form;


			require_once __DIR__ . '/class-leira-auth-form.php';
			$form = new Leira_Auth_Form();

			$field_username = new Leira_Auth_Form_Field();
			$field_username->set_id( 'uname_field' )
			               ->set_name( 'uname' )
			               ->set_label( 'Username or Email' )
			               ->set_description( 'Your Username or Email provided at registration' );
			$form->append( $field_username );

			$field_password = new Leira_Auth_Form_Field();
			$field_password->set_id( 'password_field' )
			               ->set_name( 'password' )
			               ->set_label( 'Password' )
			               ->set_type( 'password' );
			$form->append( $field_password );

			$field_remember = new Leira_Auth_Form_Field();
			$field_remember->set_type( 'checkbox' )
			               ->set_name( 'rememberme' )
			               ->set_label( 'Remember me' );
			$form->append( $field_remember );

			$field_dropdown = new Leira_Auth_Form_Field();
			$field_dropdown->set_name( 'dd' )
			               ->set_type( 'dropdown' )
			               ->set_label( 'Hello' )
			               ->set_value( 'c' )
			               ->set_options( array(
				               'a' => 'A',
				               'b' => 'B',
				               'c' => 'C',
				               'd' => 'D',
			               ) );
			$form->append( $field_dropdown );
			echo $form;

//			function phpinfo_array() {
//				ob_start();
//				phpinfo( //INFO_ALL
//				         //INFO_GENERAL
//				         //INFO_CREDITS
//				         INFO_CONFIGURATION
//				         //-INFO_MODULES
//				         //-INFO_ENVIRONMENT
//				         //-INFO_VARIABLES
//				         //-INFO_LICENSE
//				);
//				$info_arr   = array();
//				$info_lines = explode( "\n", strip_tags( ob_get_clean(), "<tr><td><h2>" ) );
//				$cat        = "General";
//				foreach ( $info_lines as $line ) {
//					// new cat?
//					preg_match( "~<h2>(.*)</h2>~", $line, $title ) ? $cat = $title[1] : null;
//					if ( preg_match( "~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val ) ) {
//						$info_arr[ $cat ][ $val[1] ] = $val[2];
//					} elseif ( preg_match( "~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val ) ) {
//						$info_arr[ $cat ][ $val[1] ] = array( "local" => $val[2], "master" => $val[3] );
//					}
//				}
//
//				return $info_arr;
//			}
//
//			$t = phpinfo_array();
//			$a = 0;
//			phpinfo(INFO_CONFIGURATION);
		} );

		add_shortcode( 'leira-auth-logout', function() {

		} );

		add_shortcode( 'leira-auth-forgot', function() {

		} );

		add_shortcode( 'leira-auth-reset', function() {

		} );

		add_shortcode( 'leira-auth-register', function() {

		} );

		add_shortcode( 'leira-auth-activate', function() {
			//check user meta key
		} );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Leira_Auth_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Leira_Auth_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/leira-auth-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Leira_Auth_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Leira_Auth_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/leira-auth-public.js', array( 'jquery' ), $this->version, false );

	}

}
