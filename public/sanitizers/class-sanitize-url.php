<?php

namespace Leira_Auth\Public\Sanitizers;

use Leira_Auth\Public\Contracts\Sanitizer;

class Sanitize_Url implements Sanitizer {

	public function normalize(mixed $value): mixed {

		if (is_string($value)) {
			return esc_url_raw($value);
		}

		return $value;
	}
}
