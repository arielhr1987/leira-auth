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
	protected string $message = 'This field is required.';

	/**
	 * The field validation
	 *
	 * @param  mixed  $value  The value to validate
	 * @param  Field  $field  The field implementation
	 *
	 * @return bool
	 */
	public function validate( mixed $value, Field $field ): bool {
		//false or 0 will be treated as a valid field value
		return ! ( $value === null || $value === '' );
	}
}
