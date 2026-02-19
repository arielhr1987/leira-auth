<?php

namespace Leira_Auth\Public\Renderers;

use Leira_Auth\Public\Contracts\Form as FormContract;
use Leira_Auth\Public\Contracts\Renderer;
use Leira_Auth\Public\Tag;
use Leira_Auth\Public\Messages\Message;

/**
 * Form renderer layer.
 *
 * @since 1.0.0
 */
class Form_Renderer implements Renderer{

	/**
	 * Field renderer dependency.
	 *
	 * @var Field_Renderer
	 */
	protected Field_Renderer $field_renderer;

	/**
	 * Constructor.
	 *
	 * @param  Field_Renderer|null  $field_renderer
	 */
	public function __construct( ?Field_Renderer $field_renderer = null ) {
		$this->field_renderer = $field_renderer ?? new Field_Renderer();
	}

	/**
	 * Render a full form.
	 *
	 * @param  object  $renderable
	 *
	 * @return string
	 */
	public function render( object $renderable ): string {
		if ( ! $renderable instanceof FormContract ) {
			return '';
		}

		$form = $renderable;
		if ( is_callable( [ $form, 'ensure_render_fields' ] ) ) {
			call_user_func( [ $form, 'ensure_render_fields' ] );
		}

		$attributes = $this->resolve_attributes( $form );
		$attributes['action'] = (string) apply_filters(
			'leira_auth_form_action_url',
			(string) ( $attributes['action'] ?? '' ),
			$form
		);

		$tag  = new Tag( 'form', $attributes );
		$html = [];
		$html[] = $tag->render_opening_tag();
		$html[] = $this->render_messages( $form );

		foreach ( $form->all() as $field ) {
			$html[] = $this->field_renderer->render( $field );
		}

		$html[] = $tag->render_closing_tag();
		$output = implode( PHP_EOL, array_filter( $html, static fn( string $line ): bool => '' !== trim( $line ) ) );

		return (string) apply_filters( 'leira_auth_form_rendered_html', $output, $form );
	}

	/**
	 * Resolve form HTML attributes without requiring them in the form contract.
	 *
	 * @param  FormContract  $form
	 *
	 * @return array<string, mixed>
	 */
	protected function resolve_attributes( FormContract $form ): array {
		if ( is_callable( [ $form, 'attributes' ] ) ) {
			$attributes = call_user_func( [ $form, 'attributes' ] );
			if ( $attributes instanceof Tag ) {
				return $attributes->attributes();
			}
			if ( is_array( $attributes ) ) {
				return $attributes;
			}
		}

		return [
			'method' => 'post',
			'class'  => 'leira-auth-form',
			'action' => '',
		];
	}

	/**
	 * Render form-level error messages.
	 *
	 * @param  FormContract  $form
	 *
	 * @return string
	 */
	protected function render_messages( FormContract $form ): string {
		$messages = array_values(
			array_filter(
				$form->messages()->to_array(),
				static fn( Message $message ): bool => $message->is_error()
			)
		);

		if ( empty( $messages ) ) {
			return '';
		}

		$html   = [];
		$html[] = '<div class="alert alert-danger">';
		foreach ( $messages as $message ) {
			$html[] = '<div>' . esc_html( $message->text() ) . '</div>';
		}
		$html[] = '</div>';

		return implode( PHP_EOL, $html );
	}
}
