<?php

namespace Leira_Auth\Public\Traits;

trait Attributable_Field {

	/**
	 * Internal attributes storage.
	 *
	 * @var array<string, mixed>
	 */
	protected array $attributes = [];

	/**
	 * Retrieve all attributes.
	 *
	 * @return array<string, mixed>
	 */
	public function attributes(): array {
		return $this->attributes;
	}

	/**
	 * Set or update a single attribute.
	 *
	 * Reserved attributes such as "name" and "value" are ignored.
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return void
	 */
	public function set_attribute( string $key, mixed $value ): void {
		if ( in_array( $key, [ 'name', 'value' ], true ) ) {
			return;
		}

		$this->attributes[ $key ] = $value;
	}

	/**
	 * Retrieve a single attribute value.
	 *
	 * @param string $key
	 *
	 * @return mixed|null
	 */
	public function get_attribute( string $key ): mixed {
		return $this->attributes[ $key ] ?? null;
	}

	/**
	 * Remove a single attribute.
	 *
	 * Reserved attributes such as "name" and "value" are ignored.
	 *
	 * @param string $key
	 *
	 * @return void
	 */
	public function remove_attribute( string $key ): void {
		if ( in_array( $key, [ 'name', 'value' ], true ) ) {
			return;
		}

		unset( $this->attributes[ $key ] );
	}
}
