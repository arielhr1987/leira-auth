<?php

namespace Leira_Auth\Public\Fields;

use Leira_Auth\Public\Html;

/**
 * Submit input field.
 *
 * @since 1.0.0
 */
class Submit extends Input{

	/**
	 * Submit field constructor
	 *
	 * @param  string  $name
	 * @param  array  $options
	 */
	public function __construct( $name, $options = [] ) {
		parent::__construct( $name, $options );
		$this->set( 'type', 'submit' );
	}

	/**
	 * Render submit button field.
	 *
	 * @return string
	 */
	public function render(): string {

		$attr          = $this->get( 'input_attr', [] );
		$attr          = is_array( $attr ) ? $attr : [];
		$attr['type']  = 'submit';
		$attr['name']  = $this->get_name();
		$attr['class'] = Html::merge_classes( '', 'wp-block-button__link wp-element-button' );

		$label = (string) $this->get( 'label', '' );
		if ( '' === $label ) {
			$label = isset( $attr['default'] ) ? (string) $attr['default'] : __( 'Submit', 'leira-auth' );
		}

		$group_class = trim( (string) $this->get( 'group_class', 'wp-block-buttons' ) );
		if ( '' === $group_class ) {
			$group_class = 'wp-block-buttons';
		}

		return implode(
			PHP_EOL,
			[
				'<div class="leira-auth-field">',
				'<div class="' . esc_attr( $group_class ) . '">',
				Html::render_element('button', $attr, esc_html( $label ) ),
				'</div>',
				'</div>',
			]
		);
	}
}
