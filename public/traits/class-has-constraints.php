<?php

namespace Leira_Auth\Public\Traits;

use Leira_Auth\Public\Contracts\Constraint;

/**
 * Shared constraints state/access.
 *
 * @since 1.0.0
 */
trait Has_Constraints{

	/**
	 * Get node constraints.
	 *
	 * @return array<int, Constraint>
	 */
	public function get_constraints(): array {
		$constraints = $this->get( 'constraints', [] );
		if ( ! is_array( $constraints ) ) {
			return [];
		}

		return array_values(
			array_filter(
				$constraints,
				static fn( mixed $constraint ): bool => $constraint instanceof Constraint
			)
		);
	}

	/**
	 * Set node constraints.
	 *
	 * @param  array<int, Constraint>  $constraints
	 *
	 * @return self
	 */
	public function set_constraints( array $constraints ): self {
		$this->set(
			'constraints',
			array_values(
				array_filter(
					$constraints,
					static fn( mixed $constraint ): bool => $constraint instanceof Constraint
				)
			)
		);

		return $this;
	}

}
