<?php

namespace Leira_Auth\Public\Renderers;

use Leira_Auth\Public\Contracts\Field as FieldContract;
use Leira_Auth\Public\Contracts\Renderer;
use Leira_Auth\Public\Fields\Button;
use Leira_Auth\Public\Fields\Checkbox;
use Leira_Auth\Public\Fields\Complex;
use Leira_Auth\Public\Fields\Hidden;
use Leira_Auth\Public\Fields\Input;
use Leira_Auth\Public\Fields\Nonce;
use Leira_Auth\Public\Fields\Referer;
use Leira_Auth\Public\Fields\Submit;
use Leira_Auth\Public\Fields\Textarea;
use Leira_Auth\Public\Tag;
use Leira_Auth\Public\Messages\Message;

/**
 * Field renderer layer.
 *
 * @since 1.0.0
 */
class Field_Renderer implements Renderer{

	/**
	 * Determine if field errors should be rendered inline.
	 *
	 * @param  FieldContract  $field
	 *
	 * @return bool
	 */
	public static function renders_errors_inline( FieldContract $field ): bool {
		$inline = ! ( $field instanceof Hidden || $field instanceof Submit || $field instanceof Button );

		return (bool) apply_filters( 'leira_auth_field_render_inline_errors', $inline, $field );
	}

	/**
	 * Render a field instance.
	 *
	 * @param  object  $renderable
	 *
	 * @return string
	 */
	public function render( object $renderable ): string {
		if ( ! $renderable instanceof FieldContract ) {
			return '';
		}

		$field = $renderable;

		if ( $field instanceof Referer ) {
			return (string) wp_referer_field( false );
		}

		if ( $field instanceof Nonce ) {
			return $this->render_nonce( $field );
		}

		if ( $field instanceof Complex ) {
			return $this->render_complex( $field );
		}

		if ( $field instanceof Checkbox ) {
			return $this->render_checkbox( $field );
		}

		if ( $field instanceof Textarea ) {
			return $this->render_textarea( $field );
		}

		if ( $field instanceof Hidden ) {
			return $this->render_hidden( $field );
		}

		if ( $field instanceof Input ) {
			return $this->render_input( $field );
		}

		$rendered = apply_filters( 'leira_auth_render_custom_field_html', null, $field, $this );

		return is_string( $rendered ) ? $rendered : '';
	}

	/**
	 * Render a standard input-based field.
	 *
	 * @param  Input  $field
	 *
	 * @return string
	 */
	protected function render_input( Input $field ): string {
		$tag = new Tag( 'input', $field->attributes()->attributes() );
		$this->apply_input_defaults( $tag, $field );

		$tag->add_class( 'form-control' );
		$tag->toggle_class( 'is-invalid', $this->has_inline_errors( $field ) );

		$input_html = $tag->render();

		if ( ! $this->supports_label_description( $field ) ) {
			return $input_html;
		}

		return $this->render_labeled_group( $field, $input_html );
	}

	/**
	 * Render a hidden field.
	 *
	 * @param  Hidden  $field
	 *
	 * @return string
	 */
	protected function render_hidden( Hidden $field ): string {
		$tag = new Tag(
			'input',
			array_merge(
				[
					'type'  => 'hidden',
					'id'    => (string) $field->attributes()->get_attribute( 'id', $field->get_name() ),
					'name'  => (string) $field->attributes()->get_attribute( 'name', $field->get_name() ),
					'value' => $field->get_value() ?? '',
				],
				$field->attributes()->attributes()
			)
		);

		return $tag->render();
	}

	/**
	 * Render nonce field.
	 *
	 * @param  Nonce  $field
	 *
	 * @return string
	 */
	protected function render_nonce( Nonce $field ): string {
		$action = $field->resolved_nonce_action();

		return (string) wp_nonce_field( $action, $field->get_name(), true, false );
	}

	/**
	 * Render checkbox field.
	 *
	 * @param  Checkbox  $field
	 *
	 * @return string
	 */
	protected function render_checkbox( Checkbox $field ): string {
		$input_tag = new Tag(
			'input',
			array_merge(
				[
					'type'  => 'checkbox',
					'id'    => (string) $field->attributes()->get_attribute( 'id', $field->get_name() ),
					'name'  => (string) $field->attributes()->get_attribute( 'name', $field->get_name() ),
					'class' => 'form-check-input',
					'value' => $field->attributes()->get_attribute( 'value', '1' ),
				],
				$field->attributes()->attributes()
			)
		);

		if ( ! empty( $field->get_value() ) ) {
			$input_tag->set_attribute( 'checked', true );
		}

		$input_html = $input_tag->render();

		$label_html = '';
		$label      = $this->field_label( $field );
		if ( '' !== $label ) {
			$label_tag = new Tag(
				'label',
				[
					'for'   => (string) $field->attributes()->get_attribute( 'id', $field->get_name() ),
					'class' => 'form-check-label',
				]
			);
			$label_html = $label_tag->render( esc_html( $label ) );
		}

		$html   = [];
		$html[] = '<div class="field-' . esc_attr( $field->get_name() ) . '">';
		$html[] = '<div class="form-check">';
		$html[] = $input_html;
		$html[] = $label_html;
		$html[] = '</div>';
		$html[] = $this->render_errors( $field );
		$html[] = $this->render_description( $field );
		$html[] = '</div>';

		return implode( PHP_EOL, array_filter( $html, static fn( string $line ): bool => '' !== trim( $line ) ) );
	}

	/**
	 * Render textarea field.
	 *
	 * @param  Textarea  $field
	 *
	 * @return string
	 */
	protected function render_textarea( Textarea $field ): string {
		$attributes = array_merge(
			[
				'id'   => (string) $field->attributes()->get_attribute( 'id', $field->get_name() ),
				'name' => (string) $field->attributes()->get_attribute( 'name', $field->get_name() ),
				'rows' => 5,
			],
			$field->attributes()->attributes()
		);
		unset( $attributes['type'], $attributes['value'] );

		$tag = new Tag( 'textarea', $attributes );
		$tag->add_class( 'form-control' );
		$tag->toggle_class( 'is-invalid', $this->has_inline_errors( $field ) );

		$input_html = $tag->render( esc_textarea( (string) ( $field->get_value() ?? '' ) ) );

		return $this->render_labeled_group( $field, $input_html );
	}

	/**
	 * Render complex field group.
	 *
	 * @param  Complex  $field
	 *
	 * @return string
	 */
	protected function render_complex( Complex $field ): string {
		$html   = [];
		$html[] = '<fieldset class="field-' . esc_attr( $field->get_name() ) . ' field-complex">';

		$label = $this->field_label( $field );
		if ( '' !== $label ) {
			$legend = new Tag( 'legend', [ 'class' => 'form-label' ] );
			$html[] = $legend->render( esc_html( $label ) );
		}

		$html[] = $this->render_errors( $field );
		foreach ( $field->fields() as $child ) {
			$html[] = $this->render( $child );
		}
		$html[] = $this->render_description( $field );
		$html[] = '</fieldset>';

		return implode( PHP_EOL, array_filter( $html, static fn( string $line ): bool => '' !== trim( $line ) ) );
	}

	/**
	 * Render labeled/description group.
	 *
	 * @param  Input  $field
	 * @param  string  $input_html
	 *
	 * @return string
	 */
	protected function render_labeled_group( Input $field, string $input_html ): string {
		$html   = [];
		$html[] = '<div class="field-' . esc_attr( $field->get_name() ) . '">';
		$html[] = $this->render_label( $field );
		$html[] = $input_html;
		$html[] = $this->render_errors( $field );
		$html[] = $this->render_description( $field );
		$html[] = '</div>';

		return implode( PHP_EOL, array_filter( $html, static fn( string $line ): bool => '' !== trim( $line ) ) );
	}

	/**
	 * Render field label.
	 *
	 * @param  Input  $field
	 *
	 * @return string
	 */
	protected function render_label( Input $field ): string {
		$label = $this->field_label( $field );
		if ( '' === $label ) {
			return '';
		}

		$tag = new Tag(
			'label',
			[
				'for'   => (string) $field->attributes()->get_attribute( 'id', $field->get_name() ),
				'class' => 'form-label',
			]
		);

		return $tag->render( esc_html( $label ) );
	}

	/**
	 * Render field description.
	 *
	 * @param  Input  $field
	 *
	 * @return string
	 */
	protected function render_description( Input $field ): string {
		$description = $this->field_description( $field );
		if ( '' === $description ) {
			return '';
		}

		$tag = new Tag( 'div', [ 'class' => 'form-text' ] );

		return $tag->render( esc_html( $description ) );
	}

	/**
	 * Render inline field errors.
	 *
	 * @param  Input  $field
	 *
	 * @return string
	 */
	protected function render_errors( Input $field ): string {
		if ( ! $this->has_inline_errors( $field ) ) {
			return '';
		}

		$messages = array_values(
			array_filter(
				$field->messages()->to_array(),
				static fn( Message $message ): bool => $message->is_error()
			)
		);

		if ( empty( $messages ) ) {
			return '';
		}

		$html   = [];
		$html[] = '<div class="invalid-feedback">';
		if ( 1 === count( $messages ) ) {
			$html[] = esc_html( $messages[0]->text() );
		} else {
			foreach ( $messages as $message ) {
				$html[] = '<div>' . esc_html( $message->text() ) . '</div>';
			}
		}
		$html[] = '</div>';

		return implode( PHP_EOL, $html );
	}

	/**
	 * Apply common defaults to an input tag.
	 *
	 * @param  Tag  $tag
	 * @param  Input  $field
	 *
	 * @return void
	 */
	protected function apply_input_defaults( Tag $tag, Input $field ): void {
		if ( null === $tag->get_attribute( 'type' ) ) {
			$tag->set_attribute( 'type', 'text' );
		}
		if ( null === $tag->get_attribute( 'id' ) ) {
			$tag->set_attribute( 'id', $field->get_name() );
		}
		if ( null === $tag->get_attribute( 'name' ) ) {
			$tag->set_attribute( 'name', $field->get_name() );
		}
		if ( null === $tag->get_attribute( 'value' ) ) {
			$tag->set_attribute( 'value', '' );
		}
	}

	/**
	 * Whether field supports label/description metadata.
	 *
	 * @param  Input  $field
	 *
	 * @return bool
	 */
	protected function supports_label_description( Input $field ): bool {
		return method_exists( $field, 'get_label' ) || method_exists( $field, 'get_description' );
	}

	/**
	 * Read label text from field.
	 *
	 * @param  Input  $field
	 *
	 * @return string
	 */
	protected function field_label( Input $field ): string {
		if ( ! method_exists( $field, 'get_label' ) ) {
			return '';
		}

		return (string) $field->get_label();
	}

	/**
	 * Read description text from field.
	 *
	 * @param  Input  $field
	 *
	 * @return string
	 */
	protected function field_description( Input $field ): string {
		if ( ! method_exists( $field, 'get_description' ) ) {
			return '';
		}

		return (string) $field->get_description();
	}

	/**
	 * Determine if inline errors should be displayed.
	 *
	 * @param  Input  $field
	 *
	 * @return bool
	 */
	protected function has_inline_errors( Input $field ): bool {
		if ( ! method_exists( $field, 'messages' ) || ! $field->messages()->has() ) {
			return false;
		}

		return self::renders_errors_inline( $field );
	}
}
