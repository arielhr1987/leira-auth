<?php

namespace Leira_Auth\Public\Constraints;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Collection of validation constraints.
 *
 * This class represents an ordered list of constraint instances and provides
 * a small, safe API for managing them. Multiple constraints of the same class
 * are allowed.
 *
 * @since 1.0.0
 */
class Constraints_Bag implements IteratorAggregate, Countable {

	/**
	 * Ordered list of constraint instances.
	 *
	 * @var array<Constraint>
	 */
	protected array $constraints = [];

	/**
	 * Add a validation constraint to the collection.
	 *
	 * Constraints are appended in the order they are added.
	 *
	 * @param Constraint $constraint Constraint instance
	 *
	 * @return self
	 */
	public function add( Constraint $constraint ): self {
		$this->constraints[] = $constraint;
		return $this;
	}

	/**
	 * Remove all constraints from the collection.
	 *
	 * @return self
	 */
	public function clear(): self {
		$this->constraints = [];
		return $this;
	}

	/**
	 * Retrieve all constraints in insertion order.
	 *
	 * @return list<Constraint>
	 */
	public function all(): array {
		return array_values( $this->constraints );
	}

	/**
	 * Retrieve an iterator for the constraints.
	 *
	 * @return Traversable<Constraint>
	 */
	public function getIterator(): Traversable {
		return new ArrayIterator( $this->all() );
	}

	/**
	 * Get the number of constraints in the collection.
	 *
	 * @return int
	 */
	public function count(): int {
		return count( $this->constraints );
	}
}
