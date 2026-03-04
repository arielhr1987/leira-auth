<?php

namespace Leira_Auth\Public\Fields;

/**
 * Textarea input field.
 *
 * @since 1.0.0
 */
class Textarea extends Input{

	/**
	 * Textarea field constructor
	 *
	 * @param  string  $action
	 * @param  array  $options
	 */
	public function __construct( $action, $options = [] ) {
		parent::__construct( $action, $options );
		$this->set( 'type', 'textarea' );
	}
}
