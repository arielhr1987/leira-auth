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
		$attr          = [];
		$attr['type']  = 'submit';
		$attr['name']  = $this->get_name();
		$attr['class'] = Html::merge_classes( 'wp-block-button__link', wp_theme_get_element_class_name( 'button' ) );

		$label = (string) $this->get( 'label', '' );
		if ( empty( $label ) ) {
			$label = $attr['default'] ?? __( 'Submit', 'leira-auth' );
		}

		$group_class = Html::merge_classes( $this->get( 'group_class', '' ), 'wp-block-buttons' );

		return Html::div( [ 'class' => 'leira-auth-field' ],
			Html::div( [ 'class' => $group_class ],
				Html::div( [ 'class' => 'wp-block-button' ],
					Html::el( 'button', $attr, esc_html( $label ) )
				)
			)
		);
	}
}
