<?php

namespace Leira_Auth\Public\Renderers;

use Leira_Auth\Public\Contracts\Renderer as Renderer_Interface;
use Leira_Auth\Public\Forms\Form;
use Leira_Auth\Public\Fields\Field;

/**
 * Base class to render a form
 */
class Renderer implements Renderer_Interface{

	/**
	 * Render a form
	 *
	 * @param  Form  $form
	 *
	 * @return string
	 */
	public function render( Form $form ): string {
		$attr = array_filter( [
			'method' => 'post',
			'id'     => $form->options()->get( 'id' ),
			'class'  => $form->options()->get( 'class' ),
			'action' => $form->options()->get( 'action' )
		] );

		$html = '<form ' . $this->render_attr( $attr ) . '>';

		// WordPress nonce if your form supports it later
		//TODO: include automatically via fields
		$html .= wp_nonce_field( $form->name(), '_wpnonce', true, false );

		// Top-level messages (form errors)
		foreach ( $form->messages()->all() as $msg ) {
			$html .= '<div class="leira-form-error">' . esc_html( $msg ) . '</div>';
		}

		// Fields
		foreach ( $form->all() as $field ) {
			$html .= $this->render_field( $field );
		}

		$html .= '</form>';

		return apply_filters(
			'leira_auth_render_form',
			$html,
			$form,
			$this
		);
	}

	/**
	 * Render a single field
	 *
	 * @param  Field  $field
	 *
	 * @return string
	 */
	public function render_field( Field $field ): string {
		$type = $field->options()->get( 'type' );

		$html = match ( $type ) {
			'checkbox' => $this->renderCheckbox( $field ),
			'hidden' => $this->renderHidden( $field ),
			'submit' => $this->renderSubmit( $field ),
			default => $this->renderInput( $field ),
		};

		return apply_filters( 'leira_auth_render_field', $html, $field, $this );
	}

	/**
	 * Render standard input
	 *
	 * @param  Field  $field
	 *
	 * @return string
	 */
	protected function renderInput( Field $field ): string {
		$attr = $field->options()->get( 'input_attr', [] );

		$attr['id']   = $attr['id'] ?? $field->value();
		$attr['type'] = $field->options()->get( 'type' );
		$attr['name'] = $field->name();

		if ( ! isset( $attr['value'] ) && $field->value() !== null ) {
			$attr['value'] = $field->value();
		}

		$html = '<div class="leira-form-row">';

		if ( $label = $field->options()->get( 'label' ) ) {
			$label_attr        = $field->options()->get( 'label_attr', [] );
			$label_attr['for'] = $attr['id'];
			$html              .= '<label ' . $this->render_attr( $label_attr ) . '>' . esc_html( $label ) . '</label>';
		}

		$html .= '<input ' . $this->render_attr( $attr ) . '>';

		$html .= $this->render_errors( $field );

		$html .= '</div>';

		return $html;
	}

	/**
	 * Render checkbox
	 *
	 * @param  Field  $field
	 *
	 * @return string
	 */
	protected function renderCheckbox( Field $field ): string {
		$attr = $field->options()->get( 'input_attr', [] );

		$attr['type'] = 'checkbox';
		$attr['name'] = $field->name();
		$attr['id']   = $attr['id'] ?? $field->value();

		if ( $field->value() ) {
			$attr['checked'] = true;
		}

		$html = '<div class="leira-form-row leira-checkbox">';

		$label_attr        = $field->options()->get( 'label_attr', [] );
		$label_attr['for'] = $attr['id'];
		$html              .= '<label ' . $this->render_attr( $label_attr ) . '>';

		$html .= '<input ' . $this->render_attr( $attr ) . '>';

		if ( $label = $field->options()->get( 'label' ) ) {
			$html .= ' ' . esc_html( $label );
		}

		$html .= '</label>';

		$html .= $this->render_errors( $field );

		$html .= '</div>';

		return $html;
	}

	/**
	 * Render hidden
	 *
	 * @param  Field  $field
	 *
	 * @return string
	 */
	protected function renderHidden( Field $field ): string {
		$attr = $field->options()->get( 'input_attr', [] );

		$attr['type'] = 'hidden';
		$attr['name'] = $field->name();

		if ( ! isset( $attr['value'] ) && $field->value() !== null ) {
			$attr['value'] = $field->value();
		}

		return '<input ' . $this->render_attr( $attr ) . '>';
	}

	/**
	 * Render submit button
	 *
	 * @param  Field  $field
	 *
	 * @return string
	 */
	protected function renderSubmit( Field $field ): string {
		$attr = $field->options()->get( 'input_attr', [] );

		$attr['type'] = 'submit';
		$attr['name'] = $field->name();

		if ( ! isset( $attr['value'] ) ) {
			$attr['value'] = __( 'Submit', 'leira-auth' );
		}

		return '<div class="leira-form-row">' . '<input ' . $this->render_attr( $attr ) . '>' . '</div>';
	}

	/**
	 * Render field errors
	 *
	 * @param  Field  $field
	 *
	 * @return string
	 */
	protected function render_errors( Field $field ): string {
		$html = '';

		foreach ( $field->messages() as $err ) {
			$html .= '<div class="leira-field-error">' . esc_html( $err ) . '</div>';
		}

		return $html;
	}

	/**
	 * Convert attribute array to HTML string
	 */
	protected function render_attr( array $attr ): string {
		$html = [];
		foreach ( $attr as $key => $value ) {
			if ( $value === true ) {
				$html[] = esc_attr( $key );
				continue;
			}
			if ( $value === false || $value === null ) {
				continue;
			}
			$html[] = sprintf( '%s="%s"', esc_attr( $key ), esc_attr( (string) $value ) );
		}

		return implode( ' ', $html );
	}
}
