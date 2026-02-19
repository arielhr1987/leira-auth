<?php

namespace Leira_Auth\Public\Fields;

/**
 * Checkbox field.
 *
 * @since 1.0.0
 */
class Checkbox extends Field{

	/**
	 * Checkbox field constructor
	 *
	 * @param $name
	 */
	public function __construct( $name ) {
		parent::__construct( $name );
		$this->options->set( 'type', 'checkbox' );
	}
}
