<?php

namespace Leira_Auth\Public;

use WP_Error;
use WP_User;

/**
 * Class Login
 * Handles user login functionality
 *
 * @package Public
 * @since 1.0.0
 */
class Login extends Form{

	/**
	 * Block name
	 *
	 * @var string - The block name including namespace.
	 */
	protected $name = 'leira-auth/login';

	/**
	 * Register the login block
	 *
	 * @return void
	 */
	public function init() {
		$block = plugin_dir_path( __DIR__ ) . 'blocks/login/block.json';
		register_block_type( $block, [
			'render_callback' => array( $this, 'shortcode' ),
		] );
	}

	/**
	 * Shortcode handler for [custom_login_form]
	 *
	 * @param  array  $atts  Shortcode attributes.
	 *
	 * @return string HTML output of the login form or message.
	 * @since 1.0.0
	 */
	public function shortcode( $atts = array() ) {
		// Parse shortcode attributes (optional, e.g., redirect)
		$args = shortcode_atts(
			$this->get_defaults_attributes(), //Default attributes from block/shortcode
			$atts,                            // User-defined attributes in block/shortcode
			'custom_login_form' // The shortcode name (for context)
		);

		// If a user is already logged in, show a message instead
		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();

			//return '<p>You are logged in as <strong>' . esc_html( $current_user->display_name ) . '</strong>. <a href="' . esc_url( wp_logout_url( get_permalink() ) ) . '">Log out?</a></p>';
		}

		$args['echo'] = false; // Ensure echo is false to return the form as a string

		$html = '<div class="leira-auth-login-form">';
		$html .= wp_login_form( $args );
		//$html .= ( new Forms\Login() )->render();
		$html .= '</div>';

		/**
		 * Apply filters on HTML output
		 */
		apply_filters( 'leira_auth_login_form_html', $html, $args );

		return $html;
	}

	/**
	 * Filter the default arguments for the login form.
	 * https://developer.wordpress.org/reference/hooks/login_form_defaults/
	 *
	 * @param  array  $defaults  - The default arguments for wp_login_form().
	 *
	 * @return array - Modified default arguments.
	 * @since 1.0.0
	 */
	public function login_form_defaults( $defaults ) {
		//TODO: Only apply this if is our shortcode/block or move to shortcode method
		//$defaults['required_username'] = true;
		//$defaults['required_password'] = true;

		return $defaults;
	}

	/**
	 * Adds error messages to the top of the login form based on URL parameters.
	 * https://developer.wordpress.org/reference/hooks/login_form_top/
	 *
	 * @param  string  $html  - The original HTML of the login form.
	 * @param  array  $args  - The arguments passed to wp_login_form().
	 *
	 * @return string - The modified HTML with error messages prepended.
	 * @since 1.0.0
	 */
	public function login_form_top( $html, $args ) {
		//Login error
		$error = '';
		// Check for login errors in the URL
		if ( isset( $_GET['login'] ) ) {
			$msg      = sanitize_text_field( wp_unslash( $_GET['login'] ) );
			$msg      = strtolower( $msg );
			$messages = array(
				'failed' => __( 'Invalid username or password.', 'leira-auth' ),
				'false'  => __( 'You are logged out.', 'leira-auth' ),
			);
			if ( isset( $messages[ $msg ] ) ) {
				$error = $messages[ $msg ];
			} else {
				$error = __( 'An unknown error occurred. Please try again.', 'leira-auth' );
			}
		}
		//We have a message to show
		if ( ! empty( $error ) ) {
			$error = '<p class="leira-auth-login-message">' . esc_html( $error ) . '</p>';
		}

		return $error . $html;
	}

	/**
	 * Filter the bottom of the login form to add forgot password and register links.
	 * https://developer.wordpress.org/reference/hooks/login_form_bottom/
	 *
	 * @param  string  $html  - The original HTML of the login form.
	 * @param  array  $args  - The arguments passed to wp_login_form().
	 *
	 * @return string - The modified HTML.
	 * @since 1.0.0
	 */
	public function login_form_bottom( $html, $args ) {

		//Forgot Password Link
		$forgot_password_url   = wp_lostpassword_url();
		$show_forgot_password  = $args['show_forgot_password'] ?? false;
		$show_forgot_password  = boolval( $show_forgot_password ) && ! empty( $forgot_password_url );
		$forgot_password_label = $args['forgot_password_label'] ?? __( 'Forgot your password?', 'leira-auth' );
		$forgot_password_link  = '';
		if ( $show_forgot_password ) {
			$forgot_password_link = '<a href="' . esc_url( $forgot_password_url ) . '">' . esc_html( $forgot_password_label ) . '</a>';
		}

		//Register Link
		$register_url   = wp_registration_url();
		$show_register  = $args['show_register'] ?? false;
		$show_register  = boolval( $show_register ) && get_option( 'users_can_register' ) && ! empty( $register_url );
		$register_label = $args['register_label'] ?? __( 'Register', 'leira-auth' );
		$register_link  = '';
		if ( $show_register ) {
			$register_link = '<a href="' . esc_url( $register_url ) . '">' . esc_html( $register_label ) . '</a>';
		}

		if ( ! empty( $forgot_password_link ) || ! empty( $register_link ) ) {
			$html .= '<p class="leira-auth-login-links">';
			if ( ! empty( $forgot_password_link ) ) {
				$html .= $forgot_password_link;
			}
			if ( ! empty( $register_link ) ) {
				$html .= $register_link;
			}
			$html .= '</p>';
		}

		return $html;
	}

	/**
	 * Filter the login URL to point to a custom login page.
	 * The login URL is changed only for front-end requests.
	 * https://developer.wordpress.org/reference/hooks/login_url/
	 *
	 * @param  string  $login_url  - The original login URL.
	 * @param  string  $redirect  - The redirect URL after login.
	 * @param  bool  $force_reauth  - Whether to force reauthentication.
	 *
	 * @return string - The modified login URL.
	 * @since 1.0.0
	 */
	public function login_url( $login_url, $redirect, $force_reauth ) {
		if ( is_admin() || isset( $_REQUEST['interim-login'] ) ) {
			// Return the default login url if we are in the admin area or interim login is requested
			return $login_url;
		}

		return site_url( 'login' ); //TODO: Change to custom login page URL
	}

	/**
	 * Redirects back to the login form with an error message on the failed login.
	 * https://developer.wordpress.org/reference/hooks/wp_login_failed/
	 *
	 * @param  string  $username  - The username used in the failed login attempt.
	 * @param  WP_Error  $error  - A WP_Error object with the authentication failure details.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function login_failed( $username, $error ) {
		if ( ! $this->is_admin_referrer() ) {
			// Add query param to indicate failure
			wp_safe_redirect( add_query_arg( 'login', 'failed', wp_get_referer() ) );
			exit;//TODO: exit, die(), or wp_die()?
		}
	}

	/**
	 * Handle login failures and redirect the user back with the error messages displayed
	 * This method is the latest to be executed before rendering the wp-login page.
	 * https://developer.wordpress.org/reference/hooks/wp_login_errors/
	 *
	 * @param  WP_Error  $errors  - The login fail errors
	 * @param  string  $redirect_to  - The URL to redirect the user after a successful login
	 *
	 * @return WP_Error
	 */
	public function login_errors( $errors, $redirect_to ) {
		if ( is_wp_error( $errors ) ) {
			//determine if there is an error when logging in
		}

		return $errors;
	}

	/**
	 * Redirects back to the login form if the username or password is empty.
	 * https://developer.wordpress.org/reference/hooks/authenticate/
	 *
	 * @param  WP_User|WP_Error  $user  The WP_User object or WP_Error on failure.
	 * @param  string  $username  The username from the login form.
	 * @param  string  $password  The password from the login form.
	 *
	 * @return WP_User|WP_Error The original $user object or WP_Error.
	 * @since 1.0.0
	 */
	public function verify_user_pass( $user, $username, $password ) {

		if ( ! is_wp_error( $user ) ) {
			if ( empty( $username ) || empty( $password ) ) {

				if ( ! $this->is_admin_referrer() ) {
					wp_safe_redirect( add_query_arg( 'login', 'empty', wp_get_referer() ) );
					exit;//TODO: exit, die(), or wp_die()?
				}
			}
		}

		if ( is_wp_error( $user ) ) {
			// Store the error in a global or transient
			//global $my_login_error;
			//$my_login_error = $user->get_error_message();
		}

		return $user;
	}

	/**
	 * Check if the referrer is from the admin area
	 *
	 * @return bool - True if the referrer is from admin, false otherwise.
	 * @since 1.0.0
	 */
	protected function is_admin_referrer() {
		$referrer = wp_get_referer();

		if ( empty( $referrer ) ) {
			return false;
		}

		return str_contains( $referrer, 'wp-login' ) || str_contains( $referrer, 'wp-admin' );
	}

	//wp_login_errors
}
