<?php

namespace Leira_Auth\Public\Contracts;

/**
 * Capability interface for objects that expose mutable attributes.
 *
 * Implementations may choose to restrict or protect specific attributes
 * (for example, "name" or "value") from being modified or removed.
 *
 * This interface defines the contract only; enforcement rules are the
 * responsibility of the implementing class or trait.
 *
 * @since 1.0.0
 */
interface Attributable{

	/**
	 * Retrieve all attributes associated with the object.
	 *
	 * @return array<string, mixed> Attribute key-value pairs
	 */
	public function attributes(): array;

	/**
	 * Set or update a single attribute.
	 *
	 * Implementations MAY prevent mutation of reserved attributes
	 * such as "name" or "value".
	 *
	 * @param string $key   Attribute name
	 * @param mixed  $value Attribute value
	 *
	 * @return void
	 */
	public function set_attribute( string $key, mixed $value ): void;

	/**
	 * Retrieve a single attribute value.
	 *
	 * @param string $key Attribute name
	 *
	 * @return mixed|null Attribute value if present, otherwise null
	 */
	public function get_attribute( string $key ): mixed;

	/**
	 * Remove a single attribute.
	 *
	 * Implementations MAY prevent removal of reserved attributes
	 * such as "name" or "value".
	 *
	 * @param string $key Attribute name
	 *
	 * @return void
	 */
	public function remove_attribute( string $key ): void;
}
