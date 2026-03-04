<?php

namespace Leira_Auth\Public\Fields;

use Leira_Auth\Public\Html;

/**
 * Checkbox field.
 *
 * @since 1.0.0
 */
class Checkbox extends Input{

	/**
	 * Checkbox field constructor
	 *
	 * @param  string  $name
	 * @param  array  $options
	 */
	public function __construct( $name, $options = [] ) {
		parent::__construct( $name, $options );
		$this->set( 'type', 'checkbox' );
	}

	/**
	 * Render checkbox field.
	 *
	 * @return string
	 */
	public function render(): string {
		// Render group
		$group_attr = array_filter( [
			'class' => Html::merge_classes( $this->get( 'group_class', '' ), 'leira-auth-field' )
		] );
		$html       = sprintf( '<div %s>', Html::render_attributes( $group_attr ) );
		// Render check container
		$html .= '<div class="leira-auth-check">';
		// Render input
		$html .= Field::render();
		// Render label
		$html .= $this->render_label();
		// Render errors
		$html .= $this->render_errors();
		// Render help
		$html .= $this->render_help();
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}
}
