<?php

namespace Leira_Auth\Public\Traits;

use Leira_Auth\Public\Contracts\Constrainable;
use Leira_Auth\Public\Messages\Bag;

/**
 * Adds validation behavior to a form field.
 *
 * This trait provides a message bag and a default validation flow.
 * Validation is performed using constraints when the consuming class
 * implements the HasConstraints contract.
 *
 * @since 1.0.0
 */
trait Validatable_Field{

	/**
	 * Bag of validation messages.
	 *
	 * Lazily initialized on first access.
	 *
	 * @var Bag|null
	 */
	protected ?Bag $messages = null;

	/**
	 * Retrieve validation messages.
	 *
	 * The message bag is lazily instantiated on first access.
	 *
	 * @return Bag Validation message bag
	 */
	public function messages(): Bag {
		if ( null === $this->messages ) {
			$this->messages = new Bag();
		}

		return $this->messages;
	}

	/**
	 * Validate the field input.
	 *
	 * Executes all configured constraints (if supported) and collects validation messages into the message bag.
	 *
	 * @param  mixed  $data  Raw field input to validate
	 *
	 * @return bool True if validation passes, false otherwise
	 */
	public function validate( $data ): bool {

		// Ensure the message bag is initialized and empty
		$this->messages()->clear();

		// Run constraint-based validation if supported
		if ( $this instanceof Constrainable ) {
			foreach ( $this->constraints() as $constraint ) {
				$constraint->validate( $data , $this);

				foreach ( $constraint->messages() as $message ) {
					$this->messages()->add( $message );
				}
			}
		}

		return empty( $this->messages()->all() );
	}
}
