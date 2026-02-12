<?php

namespace Leira_Auth\Public\Forms;

use Leira_Auth\Public\Fields\Checkbox;
use Leira_Auth\Public\Fields\Hidden;
use Leira_Auth\Public\Fields\Password;
use Leira_Auth\Public\Fields\Submit;
use Leira_Auth\Public\Fields\Text;
use Leira_Auth\Public\Constraints\Required;

/**
 * The login form implementation
 *
 * @since 1.0.0
 */
class Login extends Form{

	/**
	 * Class constructor
	 */
	public function __construct() {
		parent::__construct( 'login' );
	}

	/**
	 * Build the form
	 *
	 * @param  array  $options  Options to build the form. These are provided by shortcode or block attributes
	 *
	 * @return void
	 */
	public function build( array $options ): void {
		//Action
		$action = ( new Hidden( 'action' ) )
			->set_attribute( 'value', 'login' )
			->constraint( new Required() );

		//Username field
		$username = ( new Text( 'log' ) )
			->set_attribute( 'id', 'user_login' )
			->set_attribute( 'autocomplete', 'username' )
			->set_attribute( 'autocapitalize', 'off' )
			->set_attribute( 'size', 20 )
			->set_attribute( 'required', "" )
			->set_label( 'Username' )
			->constraint( new Required() );

		//Password field
		$password = ( new Password( 'pwd' ) )
			->set_attribute( 'id', 'user_pass' )
			->set_attribute( 'required', '' )
			->set_attribute( 'autocomplete', 'current-password' )
			->set_label( 'Password' )
			->set_description( 'The password to access this page' )
			->constraint( new Required() );

		//Remember me checkbox
		$remember_me = ( new Checkbox( 'rememberme' ) )
			->set_attribute( 'id', 'rememberme' )
			->set_attribute( 'value', 'forever' )
			->set_label( 'Remember me' );

		//Submit button
		//$submit = ( new Submit( 'Login' ) )

		$this
			->add_field( $action )
			->add_field( $username )
			->add_field( $password )
			->add_field( $remember_me );
	}


	/**
	 * Handle form submission
	 *
	 * @return void
	 */
	public function handle(): bool {
		//Handle login form
	}
}
