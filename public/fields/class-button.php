<?php

namespace Leira_Auth\Public\Fields;

/**
 * Generic button input field.
 *
 * @since 1.0.0
 */
class Button extends Input{

	/**
	 * Submit field constructor
	 *
	 * @param  string  $name
	 * @param  array  $options
	 */
	public function __construct( $name, $options = [] ) {
		parent::__construct( $name, $options );
		$this->set( 'type', 'button' );
	}
}
