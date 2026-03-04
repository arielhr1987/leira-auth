<?php

namespace Leira_Auth\Public\Fields;

/**
 * Password field.
 *
 * @since 1.0.0
 */
class Password extends Input{

	/**
	 * Text field constructor
	 *
	 * @param  string  $name
	 * @param  array  $options
	 */
	public function __construct( $name, $options = [] ) {
		parent::__construct( $name, $options );
		$this->set( 'type', 'password' );
	}

	/**
	 * Enable/disable the visibility toggle.
	 *
	 * @param  bool  $value
	 *
	 * @return self
	 */
	public function toggleable( bool $value = true ): self {
		$this->set( 'toggleable', $value );

		return $this;
	}

	/**
	 * Determine if this password field is toggleable.
	 *
	 * @return bool
	 */
	public function is_toggleable(): bool {
		return $this->get( 'toggleable' );
	}
}
