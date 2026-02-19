<?php


namespace Leira_Auth\Public\Sanitizers;

use Leira_Auth\Public\Contracts\Sanitizer;

/**
 * Trim normalize implementation
 */
class Sanitize_Text implements Sanitizer{

	/**
	 * Trim the value
	 *
	 * @param  mixed  $value
	 *
	 * @return mixed
	 */
	public function normalize( mixed $value ): mixed {
		if (is_string($value)) {

			// WordPress native safe text sanitization
			return sanitize_text_field($value);
		}

		if (is_array($value)) {
			return array_map(function ($v) {
				return is_string($v) ? sanitize_text_field($v) : $v;
			}, $value);
		}

		return $value;
	}
}
