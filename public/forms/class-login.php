<?php

namespace Leira_Auth\Public\Forms;

use Leira_Auth\Public\Constraints\Required;
use Leira_Auth\Public\Fields\Checkbox;
use Leira_Auth\Public\Fields\Hidden;
use Leira_Auth\Public\Fields\Password;
use Leira_Auth\Public\Fields\Text;

/**
 * Login form implementation.
 *
 * @since 1.0.0
 */
class Login extends Form{

	/**
	 * Class constructor.
	 */
	public function __construct() {
		parent::__construct( 'login' );
	}

	/**
	 * Build login form fields.
	 *
	 * @param  array  $options
	 *
	 * @return void
	 */
	public function build( array $options ): void {
		$this->empty_fields();

		$label_username = (string) ( $options['label_username'] ?? __( 'Username', 'leira-auth' ) );
		$label_password = (string) ( $options['label_password'] ?? __( 'Password', 'leira-auth' ) );
		$label_remember = (string) ( $options['label_remember'] ?? __( 'Remember me', 'leira-auth' ) );
		$label_submit   = (string) ( $options['label_log_in'] ?? __( 'Log In', 'leira-auth' ) );

		$action = ( new Hidden( 'action' ) )
			->set_value( 'login' )
			->constraint( new Required() );

		$username = ( new Text( 'log' ) )
			->set_attribute( 'id', 'user_login' )
			->set_attribute( 'autocomplete', 'username' )
			->set_attribute( 'autocapitalize', 'off' )
			->set_attribute( 'size', 20 )
			->set_attribute( 'required', true )
			->set_label( $label_username )
			->constraint( new Required() );

		$password = ( new Password( 'pwd' ) )
			->set_attribute( 'id', 'user_pass' )
			->set_attribute( 'required', true )
			->set_attribute( 'autocomplete', 'current-password' )
			->set_label( $label_password )
			->constraint( new Required() );

		$remember_me = ( new Checkbox( 'rememberme' ) )
			->set_attribute( 'id', 'rememberme' )
			->set_attribute( 'value', 'forever' )
			->set_label( $label_remember );

		$this
			->add_field( $action )
			->add_field( $username )
			->add_field( $password )
			->add_field( $remember_me )
			->set_submit_label( $label_submit );

		$redirect = $options['redirect'] ?? '';
		if ( is_string( $redirect ) && '' !== $redirect ) {
			$this->add_field(
				( new Hidden( 'redirect_to' ) )->set_value( sanitize_text_field( $redirect ) )
			);
		}
	}

	/**
	 * Handle login request.
	 *
	 * @return bool
	 */
	public function handle(): bool {
		if ( ! parent::handle() ) {
			return false;
		}

		$credentials = [
			'user_login'    => (string) ( $this->get_field( 'log' )?->get_value() ?? '' ),
			'user_password' => (string) ( $this->get_field( 'pwd' )?->get_value() ?? '' ),
			'remember'      => ! empty( $this->get_field( 'rememberme' )?->get_value() ),
		];

		$user = wp_signon( $credentials, is_ssl() );
		if ( is_wp_error( $user ) ) {
			$this->messages()->add( __( 'Invalid username or password.', 'leira-auth' ) );

			return false;
		}

		do_action( 'leira_auth_login_success', $user, $this );

		$redirect_to = '';
		if ( isset( $_POST['redirect_to'] ) ) {
			$redirect_to = wp_validate_redirect( (string) wp_unslash( $_POST['redirect_to'] ), '' );
		}

		if ( '' === $redirect_to ) {
			$redirect_to = (string) apply_filters( 'leira_auth_login_success_redirect', home_url( '/' ), $user, $this );
		}

		wp_safe_redirect( $redirect_to );
		exit;
	}
}
