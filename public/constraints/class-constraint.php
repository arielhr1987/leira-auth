<?php

namespace Leira_Auth\Public\Constraints;

use Leira_Auth\Public\Contracts\Constraint as Constraint_Interface;

/**
 * A base validator implementation
 *
 * @since 1.0.0
 */
abstract class Constraint implements Constraint_Interface{

	/**
	 * Validation message to show to the user
	 *
	 * @var string The validation message
	 */
	protected string $message = '';

	/**
	 * Get the validation error message
	 *
	 * @return string The message
	 */
	public function get_message(): string {
		return $this->message;
	}

	/**
	 * Set the validation error message
	 *
	 * @param  string  $message  The error message
	 *
	 * @return $this
	 */
	public function set_message( $message ): self {
		$this->message = $message;

		return $this;
	}
}
