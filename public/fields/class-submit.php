<?php

namespace Leira_Auth\Public\Fields;

/**
 * Submit input field.
 *
 * @since 1.0.0
 */
class Submit extends Field{

	/**
	 * Submit field constructor
	 *
	 * @param $name
	 */
	public function __construct( $name ) {
		parent::__construct( $name );
		$this->options->set( 'type', 'submit' );
	}
}
