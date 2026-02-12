<?php

namespace Leira_Auth\Public\Fields;

/**
 * A class that represents a password field
 *
 * @since 1.0.0
 */
class Password extends Input{

	/**
	 * Default text input attributes
	 * @var array|string[]
	 */
	protected array $attributes = [
		'type' => 'password'
	];

	/**
	 * @var bool The password field is toggleable
	 */
	protected bool $toggleable = false;

	/**
	 * @param  bool  $value  Show the eye toggle button
	 *
	 * @return $this
	 */
	public function toggleable( bool $value = true ): self {
		$this->toggleable = $value;

		return $this;
	}

	/**
	 * Determine if the password field is toggleable
	 * @return bool
	 */
	public function is_toggleable() {
		return $this->toggleable;
	}
}
