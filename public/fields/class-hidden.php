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
	 * @param string $name
	 * @param  array  $options
	 */
	public function __construct( $name, $options = [] ) {
		parent::__construct( $name, $options );
		$this->set( 'type', 'hidden' );
	}
}
