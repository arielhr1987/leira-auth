<?php

namespace Leira_Auth\Public\Fields;

/**
 * Hidden input field.
 *
 * @since 1.0.0
 */
class Hidden extends Field{

	/**
	 * Hidden field constructor
	 *
	 * @param $name
	 */
	public function __construct( $name ) {
		parent::__construct( $name );
		$this->options->set( 'type', 'hidden' );
	}
}
