<?php

namespace Leira_Auth\Public\Traits;

/**
 * Shared options accessors.
 *
 * @since 1.0.0
 */
trait Has_Options{

	/**
	 * Node options.
	 *
	 * @var array<string, mixed>
	 */
	protected array $options = [];

	/**
	 * Get options.
	 *
	 * @return array<string, mixed>
	 */
	public function get_options(): array {
		return $this->options;
	}

	/**
	 * Get an option by key.
	 *
	 * @param  string  $key
	 * @param  mixed  $default
	 *
	 * @return mixed
	 */
	public function get( string $key, mixed $default = null ): mixed {
		return $this->options[ $key ] ?? $default;
	}

	/**
	 * Set an option value.
	 *
	 * @param  string  $key
	 * @param  mixed  $value
	 *
	 * @return self
	 */
	public function set( string $key, mixed $value ): self {
		$this->options[ $key ] = $value;

		return $this;
	}
}
