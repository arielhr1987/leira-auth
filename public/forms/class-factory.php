<?php

namespace Leira_Auth\Public\Forms;

use Leira_Auth\Public\Contracts\Stateful;
use Leira_Auth\Public\Fields\Password;

/**
 * Form factory.
 *
 * @since 1.0.0
 */
class Factory{

	/**
	 * Registered form types.
	 *
	 * @var array<string, class-string<Form>>
	 */
	protected array $registry = [];

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->register( 'login', Login::class );
//		$this->register( 'register', RegisterForm::class );
//		$this->register( 'forgot', ForgotPasswordForm::class );

		do_action( 'leira_auth_register_forms', $this );
	}

	/**
	 * Register a form type.
	 *
	 * @param  string  $type
	 * @param  string  $form_class
	 *
	 * @return void
	 */
	public function register( string $type, string $form_class ): void {
		if ( ! is_subclass_of( $form_class, Form::class ) ) {
			return;
		}

		$this->registry[ $type ] = $form_class;
	}

	/**
	 * Get all registered forms.
	 *
	 * @return array<string, class-string<Form>>
	 */
	public function registry(): array {
		return $this->registry;
	}

	/**
	 * Create a form instance.
	 *
	 * @param  string  $type
	 * @param  array  $options
	 *
	 * @return Form|null
	 */
	public function create( string $type, array $options = [] ): ?Form {
		if ( ! isset( $this->registry[ $type ] ) ) {
			return null;
		}

		$form_class = $this->registry[ $type ];

		/** @var Form $form */
		$form = new $form_class();

		$options = apply_filters( 'leira_auth_form_options', $options, $type, $form );
		if ( method_exists( $form, 'set_options' ) ) {
			$form->set_options( $options );
		}
		$form->build( $options );

		do_action( 'leira_auth_form_built', $form, $type );

		return $form;
	}

	/**
	 * Restore previously submitted form values from flash.
	 *
	 * @param  Form  $form
	 *
	 * @return void
	 */
	public function restore( Form $form ): void {
		if ( ! $form instanceof Stateful ) {
			return;
		}

		$flash = leira_auth()->flash;
		if ( ! $flash ) {
			return;
		}

		$data = $flash->get( 'leira-auth', [] );
		if ( ! is_array( $data ) ) {
			return;
		}

		$form->restore( $data );
	}

	/**
	 * Persist submitted form state into flash.
	 *
	 * @param  Form  $form
	 *
	 * @return void
	 */
	public function persist( Form $form ): void {
		if ( ! $form instanceof Stateful ) {
			return;
		}

		$flash = leira_auth()->flash;
		if ( ! $flash ) {
			return;
		}

		$data = $form->state();
		if ( ! is_array( $data ) ) {
			return;
		}

		$fields_state = $data['fields'] ?? [];
		if ( ! is_array( $fields_state ) ) {
			$fields_state = [];
		}

		// Do not persist sensitive fields.
		foreach ( $form->all() as $field ) {
			if ( $field instanceof Password ) {
				unset( $fields_state[ $field->name() ] );
			}
		}
		$data['fields'] = $fields_state;

		$flash->add( 'leira-auth', $data );
	}
}
