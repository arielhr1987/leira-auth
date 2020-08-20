<?php

require_once plugin_dir_path( __FILE__ ) . 'class-leira-auth-form.php';


/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Leira_Auth
 * @subpackage Leira_Auth/public
 * @author     Ariel <arielhr1987@gmail.com>
 * @since      1.0.0
 */
class Leira_Auth_Form_Login extends Leira_Auth_Form{

	/**
	 * Leira_Auth_Form_Login constructor.
	 *
	 * @param array $args
	 *
	 * @since 1.0.0
	 */
	public function __construct( $args ) {

		$default_args = array(
			'form_id'              => 'form_login',
			'username_id'          => 'form_login_log',
			'username_label'       => __( 'Username or Email', ' leira_auth' ),
			'username_description' => '',
			'username_placeholder' => '',
			'password_id'          => 'form_login_pwd',
			'password_label'       => __( 'Password', ' leira_auth' ),
			'password_description' => '',
			'password_placeholder' => '',
			'remember_id'          => 'form_login_rememberme',
			'remember_label'       => __( 'Remember me', ' leira_auth' ),
			'remember_description' => '',
			'submit_id'            => 'form_login_submit',
			'submit_label'         => __( 'Log in', ' leira_auth' ),
			'redirect'             => '',
			'css'                  => ''
		);

		$args = shortcode_atts( $default_args, $args );

		/**
		 * Setup form
		 */
		$this->set_attr( 'method', 'post' );
		$this->set_attr( 'autocomplete', 'off' );
		$this->set_attr( 'id', $args['form_id'] );

		/**
		 * Username field
		 */
		$username = new Leira_Auth_Form_Field_Text();
		$username->set_name( 'log' )
		         ->set_attr( 'id', $args['username_id'] )
		         ->set_attr( 'placeholder', $args['username_placeholder'] )
		         ->set_attr( 'required' )
		         ->set_label( $args['username_label'] )
		         ->set_description( $args['username_description'] )
		         ->set_priority( 10 );
		$this->add_field( $username );

		/**
		 * Password field
		 */
		$password = new Leira_Auth_Form_Field_Password();
		$password->set_name( 'pwd' )
		         ->set_attr( 'id', $args['password_id'] )
		         ->set_attr( 'placeholder', $args['password_placeholder'] )
		         ->set_attr( 'required' )
		         ->set_label( $args['password_label'] )
		         ->set_description( $args['password_description'] )
		         ->set_priority( 20 );
		$this->add_field( $password );

		/**
		 * Remember me field
		 */
		$remember = new Leira_Auth_Form_Field_Checkbox();
		$remember->set_name( 'rememberme' )
		         ->set_value( 'forever' )
		         ->set_attr( 'id', $args['remember_id'] )
		         ->set_label( $args['remember_label'] )
		         ->set_description( $args['remember_description'] )
		         ->set_priority( 30 );
		$this->add_field( $remember );

		/**
		 * Submit field
		 */
		$submit = new Leira_Auth_Form_Field_Action();
		$submit->set_attr( 'id', $args['submit_id'] )
		       ->set_label( $args['submit_label'] )
		       ->set_priority( 40 );
		$this->add_field( $submit );
	}

}
