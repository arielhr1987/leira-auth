<?php

namespace Leira_Auth\Public\Sanitizers;

use Leira_Auth\Public\Contracts\Sanitizer;

class Sanitize_Integer implements Sanitizer {

	public function normalize(mixed $value): mixed {

		if ($value === '' || $value === null) {
			return null;
		}

		return (int) $value;
	}
}
