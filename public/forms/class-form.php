<?php

namespace Leira_Auth\Public\Forms;

use Leira_Auth\Public\Contracts\Field;
use Leira_Auth\Public\Contracts\Form as FormInterface;
use Leira_Auth\Public\Contracts\Stateful;
use Leira_Auth\Public\Contracts\Validatable;
use Leira_Auth\Public\Messages\Bag;

/**
 * The form class to handle submissions
 *
 * @since 1.0.0
 */
class Form implements FormInterface, Validatable, Stateful{

	/**
	 * @var string The form name
	 */
	protected string $name;

	/**
	 * The fields in the form
	 *
	 * @var Field[]
	 */
	protected array $fields = [];

	/**
	 * The form messages
	 *
	 * @var Bag
	 */
	protected Bag $messages;

	/**
	 * Class constructor
	 *
	 * @param  string  $name  The name of the field
	 */
	public function __construct( string $name ) {
		$this->name     = $name;
		$this->messages = new Bag();
	}

	/**
	 * Add the field to the form
	 *
	 * @param  Field  $field  The field to add to the form
	 *
	 * @return $this
	 */
	public function add_field( Field $field ): self {
		$this->fields[ $field->get_name() ] = $field;
		$field->set_form( $this );

		return $this;
	}

	/**
	 * Get a field given its name
	 *
	 * @param  string  $name  The name of the field to get
	 *
	 * @return Field|null The field object or null if it does not exist
	 */
	public function get_field( string $name ): ?Field {
		return $this->fields[ $name ] ?? null;
	}

	/**
	 * Remove a field from the form
	 *
	 * @param  string  $name  The name of the field to remove
	 *
	 * @return $this
	 */
	public function remove_field( string $name ): self {
		unset( $this->fields[ $name ] );

		return $this;
	}

	/**
	 * Remove all fields from the form.
	 *
	 * This method clears the internal field collection, effectively resetting
	 * the form structure. It does not affect form messages or state.
	 *
	 * @return self
	 */
	public function empty_fields(): self {
		$this->fields = [];

		return $this;
	}

	/**
	 * Get the fields in the form
	 *
	 * @return Field[]
	 */
	public function fields(): array {
		return $this->fields;
	}

	/**
	 * Get the submitted data
	 * @return array The submitted form data
	 */
	public function data(): array {
		$data = [];
		foreach ( $this->fields() as $field ) {
			$data[ $field->get_name() ] = $field->get_value();
		}

		return $data;
	}

	/**
	 * Validate the form
	 *
	 * @param  mixed  $data  The data to validate
	 *
	 * @return bool
	 */
	public function validate( mixed $data ): bool {
		//todo: improve data validation
		$valid = true;
		foreach ( $this->fields() as $name => $field ) {
			$value = $data[ $name ] ?? null;
			if ( ! $field->validate( $value ) ) {
				$valid = false;
			}
		}

		return $valid;
	}

	/**
	 * Get the list of errors
	 * These are form level error, not field errors
	 *
	 * @return Bag The errors
	 */
	public function messages(): Bag {
		return $this->messages;
	}

	/**
	 * Handle the form
	 *
	 * @return bool
	 */
	public function handle(): bool {
		//Bail if not a POST submission
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
			return false;
		}

		//Bail if invalid
		if ( ! wp_verify_nonce( $_POST['_wpnonce'] ?? '', $this->name ) ) {
			$this->messages()->add( __( 'Invalid form submission.', 'leira-auth' ) );

			return false;
		}

		$valid = true;

		foreach ( $this->fields as $name => $field ) {
			$value = $_POST[ $name ] ?? null;

			if ( ! $field->validate( $value ) ) {
				$valid = false;
			}
		}

		if ( ! $valid ) {
			$this->messages()->add( __( 'Please fix the errors below.', 'leira-auth' ) );
		}

		return $valid;
	}

	/**
	 * Render the form
	 *
	 * @return string The form HTML
	 */
	public function render(): string {
		$html = [];

		// Open form
		$html[] = '<form method="post" class="leira-auth-form">';

		// Form-level errors
		$messages = $this->messages()->all();
		$messages = array_filter( $messages, function ( $message ) {
			return $message->is_error();
		} );
		if ( ! empty( $messages ) ) {
			$html[] = '<div class="alert alert-danger">';
			if ( count( $messages ) == 1 ) {
				$html[] = $messages[0];
			} else {
				foreach ( $messages as $message ) {
					$html[] = '<div>' . esc_html( $message->text() ) . '</div>';
				}
			}
			$html[] = '</div>';
		}

		// Fields
		foreach ( $this->fields as $field ) {
			$html[] = $field->render();
		}

		// Submit button
		$html[] = '<button type="submit" class="btn btn-primary">Submit</button>';

		// Close form
		$html[] = '</form>';

		$html = array_filter( $html, 'trim' );
		$html = array_filter( $html );

		return implode( PHP_EOL, $html );
	}

	/**
	 * Build the form
	 *
	 * @param  array  $options  Options to build the form
	 *
	 * @return void
	 */
	public function build( array $options ) {
		//TODO: add nonce
	}

	/**
	 * Get the current object state.
	 *
	 * @return array
	 */
	public function state(): array {
		// TODO: Implement state() method.
		return [];
	}

	/**
	 * Restore a previous object state.
	 *
	 * @param  array  $state  The state to restore
	 *
	 * @return void
	 */
	public function restore( array $state ): void {
		// TODO: Implement restore() method.
	}
}
