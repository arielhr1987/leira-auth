<?php

namespace Leira_Auth\Public\Fields;

use Leira_Auth\Public\Html;

/**
 * Base input implementation.
 *
 * @since 1.0.0
 */
class Input extends Field{

	/**
	 * Get render priority.
	 *
	 * @return int
	 */
	public function get_priority(): int {
		return $this->get( 'priority' );
	}

	/**
	 * Set render priority.
	 *
	 * @param  int  $priority
	 *
	 * @return self
	 */
	public function set_priority( int $priority = 10 ): self {
		$this->set( 'priority', $priority );

		return $this;
	}

	/**
	 * Get label text.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return (string) $this->get( 'label', '' );
	}

	/**
	 * Set label text.
	 *
	 * @param  string  $label
	 *
	 * @return self
	 */
	public function set_label( string $label ): self {
		$this->set( 'label', $label );

		return $this;
	}

	/**
	 * Get help text.
	 *
	 * @return string
	 */
	public function get_help(): string {
		return (string) $this->get( 'help', '' );
	}

	/**
	 * Set help text.
	 *
	 * @param  string  $help
	 *
	 * @return self
	 */
	public function set_help( string $help ): self {
		$this->set( 'help', $help );

		return $this;
	}

	/**
	 * Render the input
	 *
	 * @return string
	 */
	public function render(): string {
		//Render group
		$group_attr = array_filter( [
			'class' => Html::merge_classes( $this->get( 'group_class', '' ), 'leira-auth-field' )
		] );

		return Html::render_element(
			'div',
			$group_attr,
			$this->render_label() . Field::render() . $this->render_errors() . $this->render_help()
		);
	}

	/**
	 * Render the label for this field
	 *
	 * @return string
	 */
	protected function render_label(): string {
		$label = (string) $this->get( 'label', '' );
		if ( empty( $label ) ) {
			return '';
		}
		$attr = [];
		foreach ( $this->get_options() as $key => $value ) {
			if ( str_starts_with( $key, 'label_' ) ) {
				$new_key          = substr( $key, 6 ); // remove "label_" prefix
				$attr[ $new_key ] = $value;
			}
		}
		$attr['for']   = $this->get( 'id', $this->get_name() );
		$attr['class'] = Html::merge_classes( $attr['class'] ?? '', 'leira-auth-form-label' );

		return Html::render_element( 'label', $attr, esc_html( $label ) );
	}

	/**
	 * Render validation errors for this field.
	 *
	 * @return string
	 */
	protected function render_errors(): string {
		$html = '';
		foreach ( $this->messages() as $error ) {
			$attr = [
				'role'  => $error->is_success() ? 'status' : 'alert',
				'class' => $error->is_success() ? '' : 'leira-auth-invalid-feedback',
			];
			$html .= Html::render_element( 'div', $attr, esc_html( $error->text() ) );
		}

		return $html;
	}

	/**
	 * Render help text for this field
	 *
	 * @return string
	 */
	protected function render_help() {
		$help = $this->get_help();
		if ( empty( $help ) ) {
			return '';
		}
		$attr = array_filter( [
			'id'    => $this->get_name() . '-help',
			'class' => Html::merge_classes( $this->get( 'help_class', '' ), 'leira-auth-help' )
		] );

		return Html::render_element( 'div', $attr, esc_html( $help ) );
	}

}
