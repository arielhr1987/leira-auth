<?php

namespace Leira_Auth\Public\Constraints;

use Leira_Auth\Public\Fields\Field;

/**
 * Validator to determine if a field is required
 *
 * @since 1.0.0
 */
class Required extends Constraint{

	/**
	 * The validation message
	 *
	 * @var string
	 */
	protected string $message = 'The field is required.';

	/**
	 * The field validation
	 *
	 * @param  mixed  $value  The value to validate
	 * @param  Field  $field  The field implementation
	 *
	 * @return string|null
	 */
	public function validate( mixed $value, Field $field ): ?string {
		//false or 0 will be treated as a valid field value
		if ( $value === null || $value === '' ) {
			return $this->message;
		}

		return null;
	}
}
