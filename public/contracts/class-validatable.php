<?php

namespace Leira_Auth\Public\Contracts;

use Leira_Auth\Public\Messages\Bag;

/**
 * Contract for fields that support validation.
 *
 * Implementations are responsible for validating incoming data
 * and exposing any messages produced during validation.
 *
 * @since 1.0.0
 */
interface Validatable{

	/**
	 * Validate the field data.
	 *
	 * Implementations should run all validation rules and populate
	 * validation messages when validation fails.
	 *
	 * @param  mixed  $data  Raw field input to validate
	 *
	 * @return bool True when validation passes, false otherwise.
	 */
	public function validate( mixed $data ): bool;

	/**
	 * Retrieve validation messages.
	 *
	 * @return Bag Validation messages generated during validation
	 */
	public function messages(): Bag;
}
