<?php

namespace Leira_Auth\Public\Contracts;

/**
 * Constraint interface
 *
 * Defines the contract for validation constraints that can be applied to form fields.
 *
 * @package Leira_Auth\Public\Contracts
 * @since 1.0.0
 */
interface Constraint {

	/**
	 * Validate a field value against this constraint.
	 *
	 * Performs validation on the provided value within the context of its field and form.
	 * Returns an error message if validation fails, or null if the value is valid.
	 *
	 * @param  mixed  $value  The field value to validate
	 * @param  Field  $field  The field being validated
	 *
	 * @return string|null  Error message if validation fails, or null if valid
	 */
	public function validate( mixed $value, Field $field ): ?string;
}
