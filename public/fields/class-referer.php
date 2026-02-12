<?php

namespace Leira_Auth\Public\Fields;

/**
 * A class to
 */
class Referer extends Field{
	/**
	 * The name of the field
	 * @return string
	 */
	public function get_name(): string {
		return '_wp_original_http_referer';
	}

	/**
	 * Get the wp url referer
	 * @return mixed
	 */
	public function get_value(): mixed {
		return null;
	}

	/**
	 * Do nothing
	 *
	 * @param  mixed  $value
	 *
	 * @return Field
	 */
	public function set_value( mixed $value ): Field {
		//Value is calculated dynamically
		return $this;
	}

	/**
	 * Render the form
	 * @return string
	 */
	public function render(): string {
		return wp_referer_field( false );
	}
}
