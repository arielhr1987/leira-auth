<?php

namespace Leira_Auth\Public\Fields;

/**
 * Text field.
 *
 * @since 1.0.0
 */
class Text extends Field{

	/**
	 * Text field constructor
	 *
	 * @param $name
	 */
	public function __construct( $name ) {
		parent::__construct( $name );
		$this->options->set( 'type', 'text' );
	}

}
