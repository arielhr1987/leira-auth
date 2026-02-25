<?php

namespace Leira_Auth\Public\Forms;

use Leira_Auth\Public\Fields\Hidden;
use Leira_Auth\Public\Form_Node;
use Leira_Auth\Public\Messages\Message;

/**
 * Base form implementation.
 *
 * @since 1.0.0
 */
class Form extends Form_Node{

	/**
	 * Determine if the form was submitted
	 *
	 * @var true
	 */
	protected bool $submitted;

	/**
	 * Constructor.
	 *
	 * @param  string  $name
	 */
	public function __construct( string $name ) {
		parent::__construct( $name );
		$this->clear();

		$action = new Hidden( 'action' );
		$action->options()->set( 'input_attr', [ 'value' => $name ] );
		$this->add( $action );

		$form_type = new Hidden( '_leira_auth_form' );
		$form_type->options()->set( 'input_attr', [ 'value' => $name ] );
		$this->add( $form_type );
	}

	/**
	 * Build form fields and defaults.
	 *
	 * @param  array  $options
	 *
	 * @return void
	 */
	public function build( array $options ): void {}

	/**
	 * Return field values.
	 *
	 * @return array
	 */
	public function data(): array {
		$data = [];
		foreach ( $this->all() as $field ) {
			$data[ $field->name() ] = $field->value();
		}

		return $data;
	}

	/**
	 * Fill the form with the data provided
	 *
	 * @param  array  $data
	 *
	 * @return void
	 */
	public function fill( $data ) {}

	/**
	 * Validate submitted payload.
	 *
	 * @param  mixed  $data
	 *
	 * @return bool
	 */
	public function validate( mixed $data ): bool {
		$data  = is_array( $data ) ? $data : [];
		$valid = true;

		foreach ( $this->all() as $field ) {
			if ( ! method_exists( $field, 'validate' ) ) {
				continue;
			}

			$name  = $field->name();
			$value = $data[ $name ] ?? null;
			if ( ! $field->validate( $value ) ) {
				$valid = false;
			}
		}

		return $valid;
	}

	/**
	 * Handle current request.
	 *
	 * @return bool
	 */
	public function handle(): bool {
		$this->messages()->clear();

		$method = strtoupper( $_SERVER['REQUEST_METHOD'] ?? '' );
		if ( 'POST' !== $method ) {
			return false;
		}

		$data  = is_array( $_POST ) ? wp_unslash( $_POST ) : [];
		$valid = $this->validate( $data );
		if ( ! $valid && ! $this->messages()->has() ) {
			$this->messages()->add( new Message( __( 'Please fix the errors below.', 'leira-auth' ) ) );
		}

		return $valid;
	}

	/**
	 * Submit the form.
	 * Set the form data, normalize, sanitize and validate it
	 *
	 * @param  array  $values  The values to submit
	 *
	 * @return void
	 */
	public function submit( $values ): void {
		$this->submitted = true;

		if ( ! is_array( $values ) ) {
			$values = [];
		}

		foreach ( $this->fields->all() as $name => $child ) {
			$value = $values[ $name ] ?? null;
			$child->submit( $value );
		}
	}

	/**
	 * Export a current form snapshot
	 *
	 * @return array
	 */
	public function snapshot() {
		//form messages
		$messages = [];
		foreach ( $this->messages() as $message ) {
			$messages[] = [
				'text' => $message->text(),
				'type' => $message->type(),
			];
		}
		// form fields
		$fields = [];
		foreach ( $this->all() as $field ) {
			foreach ( $field->messages() as $message ) {
				$is_restorable = (bool) $field->options()->get( 'restorable', false );
				if ( $is_restorable ) {
					$fields[ $field->name() ]['value'] = $field->value();
				}
				$fields[ $field->name() ]['messages'][] = [
					'text' => $message->text(),
					'type' => $message->type(),
				];
			}
		}

		return compact( 'messages', 'fields' );
	}

	/**
	 * Restore a previous version of the form
	 *
	 * @param  array  $data
	 *
	 * @return void
	 */
	public function restore( $data ) {
		//Convert array to messages
		$array_to_messages = function ( $messages, $data ) {
			if ( is_array( $data ) ) {
				foreach ( $data as $value ) {
					$text = $value['text'] ?? '';
					if ( empty( $text ) ) {
						continue;
					}
					$type = $value['type'] ?? Message::ERROR;
					$type = is_string( $type ) ? strtolower( $type ) : Message::ERROR;
					if ( ! in_array( $type, [ Message::ERROR, Message::SUCCESS ] ) ) {
						$type = Message::ERROR;;
					}
					$messages->add( new Message( $text, $type ) );
				}
			}
		};

		//restore form messages
		$messages = $data['messages'] ?? [];
		$array_to_messages( $this->messages(), $messages );

		//Restore field messages
		$fields = $data['fields'] ?? [];;
		foreach ( $fields as $key => $value ) {
			$field = $this->get( $key );
			if ( ! $field ) {
				continue;
			}
			$is_restorable = (bool) $field->options()->get( 'restorable', false );
			if ( $is_restorable ) {
				$input_attr = $field->options()->get( 'input_attr', [] );
				$field->options()->set( 'input_attr', $input_attr['value'] = $value );
			}
			$array_to_messages( $field->messages(), $value['messages'] ?? '' );
		}
	}

	/**
	 * Determine if the current form is ajax
	 *
	 * @return bool
	 */
	public function is_ajax() {
		return (bool) $this->options()->get( 'ajax', false );
	}

	/**
	 * Set form ajax option
	 *
	 * @param  bool  $value  If the form is ajax enabled or not
	 *
	 * @return void
	 */
	public function ajax( $value = true ) {
		$this->options()->set( 'ajax', $value );
	}
}
