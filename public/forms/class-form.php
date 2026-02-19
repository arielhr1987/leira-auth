<?php

namespace Leira_Auth\Public\Forms;

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
	}

	/**
	 * Build form fields and defaults.
	 *
	 * @param  array  $options
	 *
	 * @return void
	 */
	public function build( array $options ): void {

	}

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
	public function fill( $data ) {

	}

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
			$this->messages()->add( __( 'Please fix the errors below.', 'leira-auth' ), Message::ERROR );
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
}
