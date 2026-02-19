<?php

namespace Leira_Auth\Public\Sanitizers;

use Leira_Auth\Public\Contracts\Sanitizer;

/**
 * Boolean sanitizer / normalizer.
 *
 * Converts different truthy / falsy representations into a strict boolean.
 *
 * Accepted truthy string values: "1", "true", "yes", "on"
 *
 * Numeric values are cast using PHP boolean casting rules. Any unsupported value defaults to false.
 *
 * @package Leira_Auth\Public\Sanitizers
 */
class Sanitize_Boolean implements Sanitizer {

	/**
	 * Normalize a value into a boolean.
	 *
	 * @param mixed $value Raw input value.
	 * @return bool Normalized boolean value.
	 */
	public function normalize(mixed $value): mixed {

		if (is_bool($value)) {
			return $value;
		}

		if (is_string($value)) {

			$value = strtolower($value);

			return in_array($value, ['1','true','yes','on'], true);
		}

		if (is_numeric($value)) {
			return (bool) $value;
		}

		return false;
	}
}
