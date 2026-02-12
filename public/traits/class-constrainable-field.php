<?php

namespace Leira_Auth\Public\Traits;

use Leira_Auth\Public\Constraints\Constraints_Bag;
use Leira_Auth\Public\Contracts\Constraint;

/**
 * Provides support for attaching validation constraints to a field.
 *
 * This trait offers a small, safe API for managing constraints and is intended
 * to be used by fields that participate in validation.
 *
 * @since 1.0.0
 */
trait Constrainable_Field{

	/**
	 * Collection of field validation constraints.
	 *
	 * @var Constraint[]
	 */
	protected array $constraints = [];

	/**
	 * Retrieve all validation constraints assigned to the field.
	 *
	 * @return Constraint[]
	 */
	public function constraints(): array {
		return $this->constraints;
	}
}
