<?php

namespace Leira_Auth\Public\Contracts;

use Leira_Auth\Public\Fields\Field;

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
	 * Returns true when the value is valid and false when it fails validation.
	 *
	 * @param  mixed  $value  The field value to validate
	 * @param  Field  $field  The field being validated
	 *
	 * @return bool
	 */
	public function validate( mixed $value, Field $field ): bool;

	/**
	 * Get the validation message for failed assertions.
	 *
	 * @return string
	 */
	public function get_message(): string;
}
