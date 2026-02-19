<?php

namespace Leira_Auth\Public\Contracts;

/**
 * Normalizer interface
 */
interface Sanitizer{

	/**
	 * Process the value and return a normalized version
	 *
	 * @param  mixed  $value
	 *
	 * @return mixed
	 */
	public function normalize( mixed $value ): mixed;
}
