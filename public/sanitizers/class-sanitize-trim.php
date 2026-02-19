<?php


namespace Leira_Auth\Public\Sanitizers;

use Leira_Auth\Public\Contracts\Sanitizer;

/**
 * Trim normalize implementation
 */
class Sanitize_Trim implements Sanitizer{

	/**
	 * Trim the value
	 *
	 * @param  mixed  $value
	 *
	 * @return mixed
	 */
	public function normalize( mixed $value ): mixed {
		if ( is_string( $value ) ) {
			return trim( $value );
		}

		return $value;
	}
}
