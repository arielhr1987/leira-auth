<?php

namespace Leira_Auth\Public\Constraints;

use Leira_Auth\Public\Fields\Field;

/**
 * Validator to determine if a field is a valid email address
 *
 * @since 1.0.0
 */
class Email extends Constraint{

	/**
	 * The validation message
	 *
	 * @var string
	 */
	protected string $message = 'Invalid email address.';

	/**
	 * Validate an email
	 *
	 * @param  mixed  $value
	 * @param  Field  $field
	 *
	 * @return bool
	 */
	public function validate( mixed $value, Field $field ): bool {
		return (bool) is_email( $value );
	}
}
