<?php

namespace Leira_Auth\Public\Sanitizers;

use Leira_Auth\Public\Contracts\Sanitizer;

class Normalize_Float implements Sanitizer {

	public function normalize(mixed $value): mixed {

		if ($value === '' || $value === null) {
			return null;
		}

		return (float) $value;
	}
}
