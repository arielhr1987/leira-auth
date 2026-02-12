<?php

namespace Leira_Auth\Public\Contracts;

/**
 * Indicates that a field exposes validation constraints.
 *
 * Fields implementing this contract declare a set of constraints
 * that will be executed during validation.
 *
 * @since 1.0.0
 */
interface Constrainable{

	/**
	 * Retrieve field validation constraints.
	 *
	 * @return Constraint[] List of constraints applied to the field
	 */
	public function constraints(): array;
}
