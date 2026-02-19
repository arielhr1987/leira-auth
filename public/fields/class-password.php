<?php

namespace Leira_Auth\Public\Fields;

/**
 * Password field.
 *
 * @since 1.0.0
 */
class Password extends Text{

	/**
	 * Text field constructor
	 *
	 * @param $name
	 */
	public function __construct( $name ) {
		parent::__construct( $name );
		$this->options->set( 'type', 'password' );
	}

	/**
	 * Whether the password field can be toggled.
	 *
	 * @var bool
	 */
	protected bool $toggleable = false;

	/**
	 * Enable/disable the visibility toggle.
	 *
	 * @param  bool  $value
	 *
	 * @return self
	 */
	public function toggleable( bool $value = true ): self {
		$this->toggleable = $value;

		return $this;
	}

	/**
	 * Determine if this password field is toggleable.
	 *
	 * @return bool
	 */
	public function is_toggleable(): bool {
		return $this->toggleable;
	}
}
