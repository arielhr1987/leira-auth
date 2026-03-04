<?php

namespace Leira_Auth\Public\Fields;

/**
 * Text field.
 *
 * @since 1.0.0
 */
class Text extends Input{

	/**
	 * Text field constructor
	 *
	 * @param  string  $name
	 * @param  array  $options
	 */
	public function __construct( $name, $options = [] ) {
		parent::__construct( $name, $options );
		$this->set( 'type', 'text' );
	}
}
