<?php

namespace Leira_Auth\Public\Forms;

use Leira_Auth\Public\Contracts\Form;
use Leira_Auth\Public\Fields\Password;
use Leira_Auth\Public\Forms\Form as Base_Form;
use Leira_Auth\Public\Messages\Message;

/**
 * A form factory class
 *
 * @since 1.0.0
 */
class Factory{

	/**
	 * Registered form types
	 *
	 * @var array<string, class-string<Form>>
	 */
	protected array $registry = [];

	/**
	 * Class constructor
	 */
	public function __construct() {
		/**
		 * Core forms registration
		 */
		$this->register( 'login', Login::class );
//		$this->register('register', RegisterForm::class);
//		$this->register('forgot',   ForgotPasswordForm::class);

		/**
		 * Allow plugins to register their own forms
		 */
		do_action( 'leira_auth_register_forms', $this );
	}

	/**
	 * Register a form type
	 *
	 * @param  string  $type  The name of the form, the type should match the form name
	 * @param  string  $formClass  The class name to instantiate the form
	 *
	 * @return void
	 */
	public function register( string $type, string $formClass ): void {

		if ( ! is_subclass_of( $formClass, Form::class ) ) {
			return;
		}

		$this->registry[ $type ] = $formClass;
	}

	/**
	 * Get all the registered forms
	 *
	 * @return array
	 */
	public function registry(): array {
		return $this->registry;
	}

	/**
	 * Create a form instance
	 *
	 * @param  string  $type  The form time [login, forgot, register...]
	 * @param  array  $options  The options to configure the form
	 *
	 * @return null|Form The form instance
	 */
	public function create( string $type, array $options = [] ): ?Form {

		if ( ! isset( $this->registry[ $type ] ) ) {
			return null;
		}

		$formClass = $this->registry[ $type ];

		/** @var Form $form */
		$form = new $formClass();

		/**
		 * Allow plugins to alter options before configuration
		 */
		$options = apply_filters( 'leira_auth_form_options', $options, $type, $form );

		if ( method_exists( $form, 'build' ) ) {
			//make sure "build method" is implemented
			$form->build( $options );
		}

		if ( method_exists( $form, 'load_flash' ) ) {
			//$form->load_flash( [] );
			//load previous data
		}

		/**
		 * Allow plugins to modify the built form
		 */
		do_action( 'leira_auth_form_built', $form, $type );

		return $form;
	}

	/**
	 * Restore previous submitted form values
	 *
	 * @param  Form  $form  Teh form to restore
	 *
	 * @return void
	 */
	public function restore( Form $form ): void {

		$data = leira_auth()->flash->get( 'leira-auth' );

		//messages
		$messages = $data['messages'] ?? [];
		foreach ( $messages as $message ) {
			$text = $message['text'] ?? '';
			$type = $message['type'] ?? Message::ERROR;
			$form->messages()->add( $text, $type );
		}

		//fields
		$fields = $data['fields'] ?? [];
		foreach ( $fields as $name => $field ) {
			$field = $form->get_field( $name );
			if ( ! $field ) {
				continue;
			}
			//value
			$value = $field['value'] ?? null;
			$field->set_value( $value );

			$messages = $field['messages'] ?? [];
			foreach ( $messages as $message ) {
				$text = $message['text'] ?? '';
				$type = $message['type'] ?? Message::ERROR;
				$field->messages()->add( $text, $type );
			}
		}
	}

	/**
	 * Persist a submitted form
	 *
	 * @param  Form  $form  The form to persist
	 *
	 * @return void
	 */
	public function persist( Form $form ): void {
		$data = [
			'messages' => [],
			'fields'   => [],
		];

		//messages
		foreach ( $form->messages()->all() as $message ) {
			$data['messages'][] = [
				'text' => $message->text(),
				'type' => $message->type(),
			];
		}

		//fields
		$fields = array_filter( $form->fields(), function ( $field ) {
			//Only persist fields
			//TODO: do not persist sensitive fields
			return ! $field instanceof Password;
		} );
		foreach ( $fields as $field ) {
			$data['fields'][ $field->name() ] = [
				'value'    => $field->value(),
				'messages' => array_map( function ( $message ) {
					return [
						'text' => $message->text(),
						'type' => $message->type(),
					];
				}, $field->messages()->all() ),
			];
		}

		//flash data
		leira_auth()->flash->add( 'leira-auth', $data );
	}
}

