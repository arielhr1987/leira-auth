<?php

namespace Leira_Auth\Public\Fields;

/**
 * A checkbox input element.
 *
 * @since 1.0.0
 */
class Checkbox extends Input{

	/**
	 * Default text input attributes
	 * @var array|string[]
	 */
	protected array $attributes = [
		'type' => 'checkbox'
	];

	/**
	 * Render the input group with its label and description.
	 *
	 * @return string
	 */
	public function render(): string {
		$html = [];

		//Group
		$html[] = '<div class="field-' . esc_attr( $this->get_name() ) . '">';
		$html[] = '<div class="form-check">';

		// Input
		$html[] = $this->render_input();

		// Label
		$html[] = $this->render_label();

		// Errors
		$html[] = $this->render_errors();

		// Description
		$html[] = $this->render_description();

		$html [] = '</div>';
		$html [] = '</div>';

		$html = array_filter( $html, 'trim' );
		$html = array_filter( $html );

		return implode( PHP_EOL, $html );
	}
}
