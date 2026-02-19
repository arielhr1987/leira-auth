<?php

namespace Leira_Auth\Public\Sanitizers;

use Leira_Auth\Public\Contracts\Sanitizer;

class Sanitize_Email implements Sanitizer{

	public function normalize( mixed $value ): mixed {
		if ( is_string( $value ) ) {
			return sanitize_email( $value );
		}

		return $value;
	}
}
