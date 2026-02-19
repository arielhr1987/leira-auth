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
		$this->options()
		     ->set( 'id', (string) ( $options['form_id'] ?? '' ) )
		     ->set( 'class', (string) ( $options['form_class'] ?? '' ) )
		     ->set( 'ajax', (bool) ( $options['ajax'] ?? false ) );

		//Redirect to
		$redirect_url = $options['redirect'] ?? '';
		if ( is_string( $redirect_url ) && '' !== $redirect_url ) {
			$redirect_to = new Hidden( 'redirect_to' );
			//$redirect_to->constraints->add()// URL encoded array only
			$redirect_to->options()->set( 'input_attr', [
				'value' => $redirect_url,
			] );
			$this->add( $redirect_to );
		}

		//Username or Email
		$username = new Text( 'log' );
		$username->constraints()
		         ->add( new Required() );
		$username->options()
		         ->set( 'label', (string) ( $options['label_username'] ?? __( 'Username or Email', 'leira-auth' ) ) )
		         ->set( 'input_attr', [
			         'id'           => (string) ( $options['id_username'] ?? 'user_login' ),
			         'class'        => (string) ( $options['username_class'] ?? '' ),
			         'autocomplete' => 'username',
			         'placeholder'  => (string) ( $options['username_placeholder'] ?? '' ),
			         'required'     => true
		         ] );
		$this->add( $username );

		//Password
		$password = new Password( 'pwd' );
		$password->constraints()
		         ->add( new Required() );
		$password->options()
		         ->set( 'label', (string) ( $options['label_password'] ?? __( 'Password', 'leira-auth' ) ) )
		         ->set( 'input_attr', [
			         'id'           => (string) ( $options['id_password'] ?? 'user_pass' ),
			         'class'        => (string) ( $options['password_class'] ?? '' ),
			         'autocomplete' => 'current-password',
			         'placeholder'  => (string) ( $options['password_placeholder'] ?? '' ),
			         'required'     => true
		         ] );
		$this->add( $password );

		//Remember me
		$remember = new Checkbox( 'rememberme' );
		$remember->constraints()
		         ->add( new Required() );
		$remember->options()
		         ->set( 'label', (string) ( $options['label_remember'] ?? __( 'Remember me', 'leira-auth' ) ) )
		         ->set( 'input_attr', [
			         'id'    => (string) ( $options['id_remember'] ?? 'rememberme' ),
			         'value' => 'forever',
		         ] );
		$this->add( $remember );

		//Submit
		$submit = new Submit( 'submit' );
		$submit->options()
		       ->set( 'input_attr', [
			       'id'    => (string) ( $options['id_submit'] ?? 'submit' ),
			       'class' => (string) ( $options['class_submit'] ?? '' ),
		       ] );
		$this->add( $submit );

		do_action( 'leira_auth_form_fields_login', $this, $options );
	}

	/**
	 * Handle login submission.
	 *
	 * @return bool
	 */
	public function handle(): bool {
		if ( ! parent::handle() ) {
			return false;
		}

		$credentials = [
			'user_login'    => (string) ( $this->get( 'log' )?->value() ?? '' ),
			'user_password' => (string) ( $this->get( 'pwd' )?->value() ?? '' ),
			'remember'      => ! empty( $this->get( 'rememberme' )?->value() ),
		];

		$user = wp_signon( $credentials, is_ssl() );
		if ( is_wp_error( $user ) ) {
			$this->messages()->add( __( 'Invalid username or password.', 'leira-auth' ) );

			return false;
		}

		do_action( 'leira_auth_login_success', $user, $this );

		$this->redirect_url = $this->resolve_redirect_url( $user );

		if ( wp_doing_ajax() ) {
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
		$redirect = wp_validate_redirect( (string) ( $this->get( 'redirect_to' )?->value() ?? '' ), '' );
		if ( '' === $redirect ) {
			$redirect = (string) apply_filters( 'leira_auth_login_success_redirect', home_url( '/' ), $user, $this );
		}

		return $redirect;
	}
}
