<?php

namespace Leira_Auth\Public\Forms;

use Leira_Auth\Public\Constraints\Required;
use Leira_Auth\Public\Fields\Checkbox;
use Leira_Auth\Public\Fields\Hidden;
use Leira_Auth\Public\Fields\Password;
use Leira_Auth\Public\Fields\Submit;
use Leira_Auth\Public\Fields\Text;

/**
 * Login form implementation.
 *
 * @since 1.0.0
 */
class Login extends Form{

	/**
	 * Last computed success redirect URL.
	 *
	 * @var string
	 */
	protected string $redirect_url = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( 'login' );
	}

	/**
	 * Build login fields from options.
	 *
	 * @param  array  $options
	 *
	 * @return void
	 */
	public function build( array $options ): void {
		//Form options
		$this->set( 'id', (string) ( $options['form_id'] ?? '' ) )
		     ->set( 'class', (string) ( $options['form_class'] ?? '' ) )
		     ->set( 'ajax', (bool) ( $options['ajax'] ?? false ) )
		     ->set( 'forgot_password_show', $options['forgot_password_show'] ?? true )
		     ->set( 'register_login', $options['show_register'] ?? true )
		     ->set( 'forgot_password_label', (string) ( $options['forgot_password_label'] ?? '' ) )
		     ->set( 'register_label', (string) ( $options['register_label'] ?? '' ) );

		//Redirect to
		$redirect_url = isset( $options['redirect'] ) ? esc_url_raw( (string) $options['redirect'] ) : '';
		if ( !empty($redirect_url) && wp_http_validate_url( $redirect_url ) ) {
			$redirect_to = new Hidden( 'redirect_to', [
				'value'   => $redirect_url,
				'default' => $redirect_url,
			] );
			$this->add_field( $redirect_to );
		}

		//Username or Email
		$username = new Text( 'log', [
			'id'           => $options['username_id'] ?? 'user_login',
			'label'        => $options['username_label'] ?? __( 'Username or Email', 'leira-auth' ),
			'class'        => $options['username_class'] ?? '',
			'placeholder'  => $options['username_placeholder'] ?? '',
			'autocomplete' => 'username',
			'constraints'  => [ new Required() ]
			//'required'     => true,
		] );
		$this->add_field( $username );

		//Password
		$password = new Password( 'pwd', [
			'id'           => $options['password_id'] ?? 'user_pass',
			'label'        => $options['password_label'] ?? __( 'Password', 'leira-auth' ),
			'class'        => $options['password_class'] ?? '',
			'placeholder'  => $options['password_placeholder'] ?? '',
			'autocomplete' => 'current-password',
			'constraints'  => [ new Required(), ]
			//'required'     => true,
		] );
		$this->add_field( $password );

		//Remember me
		$remember = new Checkbox( 'rememberme', [
			'id'      => $options['remember_id'] ?? 'rememberme',
			'label'   => $options['remember_label'] ?? __( 'Remember me', 'leira-auth' ),
			'value'   => 'forever',
			'checked' => $options['remember_default'] ?? false,
		] );
		$this->add_field( $remember );

		//Submit
		$submit = new Submit( 'submit', [
			'id'          => $options['submit_id'] ?? 'wp-submit',
			'label'       => $options['submit_text'] ?? __( 'Log In', 'leira-auth' ),
			'class'       => $options['submit_class'] ?? '',
			'group_class' => $this->submit_group_class( $options )
		] );
		$this->add_field( $submit );

		// Form is ready
		do_action( 'leira_auth_form_fields_login', $this, $options );
	}

	/**
	 * Handle login submission.
	 *
	 * @return bool
	 */
	public function handle(): bool {

		$credentials = [
			'user_login'    => (string) ( $this->get_field( 'log' )?->get_value() ?? '' ),
			'user_password' => (string) ( $this->get_field( 'pwd' )?->get_value() ?? '' ),
			'remember'      => ! empty( $this->get_field( 'rememberme' )?->get_value() ),
		];

		$user = wp_signon( $credentials, is_ssl() );
		if ( is_wp_error( $user ) ) {
			$this->add_message( __( 'Invalid username or password.', 'leira-auth' ) );

			return false;
		}

		do_action( 'leira_auth_login_success', $user, $this );

		$this->redirect_url = $this->resolve_redirect_url( $user );

		$is_ajax_request = wp_doing_ajax()
			|| 'xmlhttprequest' === strtolower( (string) ( $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '' ) );
		if ( ! $is_ajax_request ) {
			$accept = strtolower( (string) ( $_SERVER['HTTP_ACCEPT'] ?? '' ) );
			$is_ajax_request = str_contains( $accept, 'application/json' );
		}
		if ( $is_ajax_request ) {
			return true;
		}

		wp_safe_redirect( $this->redirect_url );
		exit;
	}

	/**
	 * Get the last computed redirect URL.
	 *
	 * @return string
	 */
	public function redirect_url(): string {
		return $this->redirect_url;
	}

	/**
	 * Resolve successful login redirect.
	 *
	 * @param  \WP_User  $user
	 *
	 * @return string
	 */
	protected function resolve_redirect_url( \WP_User $user ): string {
		$redirect = wp_validate_redirect( (string) ( $this->get_field( 'redirect_to' )?->get_value() ?? '' ), '' );
		if ( '' === $redirect ) {
			$redirect = (string) apply_filters( 'leira_auth_login_success_redirect', home_url( '/' ), $user, $this );
		}

		return $redirect;
	}

	/**
	 * Build submit button group classes.
	 *
	 * @param  array<string, mixed>  $options
	 *
	 * @return string
	 */
	protected function submit_group_class( array $options ): string {
		$base_class = trim( (string) ( $options['submit_group_class'] ?? 'wp-block-buttons is-layout-flex' ) );
		if ( '' === $base_class ) {
			$base_class = 'wp-block-buttons is-layout-flex';
		}

		$justification = strtolower( (string) ( $options['submit_justification'] ?? $options['submit_alignment'] ?? 'left' ) );
		$justification_class = match ( $justification ) {
			'center' => 'is-content-justification-center',
			'right' => 'is-content-justification-right',
			'space-between' => 'is-content-justification-space-between',
			default => 'is-content-justification-left',
		};

		$orientation = strtolower( (string) ( $options['submit_orientation'] ?? 'horizontal' ) );
		$orientation_class = 'vertical' === $orientation ? 'is-vertical' : '';

		$allow_wrap = true;
		if ( array_key_exists( 'submit_allow_wrap', $options ) ) {
			$allow_wrap = (bool) $options['submit_allow_wrap'];
		}
		$wrap_class = $allow_wrap ? '' : 'is-nowrap';

		return trim( implode( ' ', array_filter( [ $base_class, $justification_class, $orientation_class, $wrap_class ] ) ) );
	}
}
