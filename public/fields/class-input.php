<?php

namespace Leira_Auth\Public\Fields;

/**
 * The base field implementation
 *
 * @since 1.0.0
 */
class Input extends Field{

	/**
	 * @var string The label to display on the field
	 */
	protected string $label = '';

	/**
	 * @var string The field description
	 */
	protected string $description = '';

	/**
	 * @var array|string[] The input attributes
	 */
	protected array $attributes = [
		'type' => 'text'
	];

	/**
	 * Class constructor
	 *
	 * @param  string  $name  The field name.
	 */
	public function __construct( string $name ) {
		parent::__construct( $name );
		$this->set_attribute( 'name', $name );
	}

	/**
	 * Get the field name
	 *
	 * @return string The field name
	 */
	public function get_name(): string {
		return (string) $this->get_attribute( 'name', $this->name );
	}

	/**
	 * Get the field value
	 *
	 * @return mixed The field value
	 */
	public function get_value(): mixed {
		return $this->get_attribute( 'value' );
	}

	/**
	 * Set the field value
	 *
	 * @param  mixed  $value  The new value
	 *
	 * @return Field
	 */
	public function set_value( mixed $value ): self {
		$this->set_attribute( 'value', $value );

		return $this;
	}

	/**
	 * Get the field label
	 * @return string
	 */
	public function get_label(): string {
		return $this->label;
	}

	/**
	 * Set the field label
	 *
	 * @param  string  $label  The new label
	 *
	 * @return self
	 */
	public function set_label( string $label ): self {
		$this->label = $label;

		return $this;
	}

	/**
	 * Get the field description
	 *
	 * @return string The description
	 */
	public function get_description(): string {
		return $this->description;
	}

	/**
	 * Set the field description
	 *
	 * @param  string  $description  The new description
	 *
	 * @return self
	 */
	public function set_description( string $description ): self {
		$this->description = $description;

		return $this;
	}

	/**
	 * Get an attribute value for this field
	 *
	 * @param  string  $name  The attribute name
	 * @param  mixed  $default  The default value of the attribute if not set.
	 *
	 * @return mixed|string|null
	 */
	public function get_attribute( $name, $default = null ): mixed {
		return $this->attributes[ $name ] ?? $default;
	}

	/**
	 * Set a field attribute
	 *
	 * @param  string  $name  The attribute
	 * @param  mixed  $value  The value of the attribute
	 *
	 * @return $this
	 */
	public function set_attribute( string $name, $value ): self {
		$this->attributes[ $name ] = $value;

		return $this;
	}

	/**
	 * Get a list of input field attributes
	 *
	 * @return array|string[]
	 */
	public function attributes(): array {
		return $this->attributes;
	}

	/**
	 * Render the input group with its label and description.
	 *
	 * @return string
	 */
	public function render(): string {

		$html = [];

		//Group
		$html[] = '<div class="field-' . esc_attr( $this->get_name() ) . '">';

		// Label
		$html[] = $this->render_label();

		// Input
		$html[] = $this->render_input();

		// Errors
		$html[] = $this->render_errors();

		// Description
		$html[] = $this->render_description();

		$html [] = '</div>';

		$html = array_filter( $html, 'trim' );
		$html = array_filter( $html );

		return implode( PHP_EOL, $html );
	}

	/**
	 * Render the field label
	 *
	 * @return string
	 */
	protected function render_label(): string {
		if ( ! empty( $this->label ) ) {
			return $this->render_tag(
				'label',
				[
					'for'   => $this->get_attribute( 'id' ) ?? $this->get_name(),
					'class' => 'form-label',
				],
				esc_html( $this->label )
			);
		}

		return '';
	}

	/**
	 * Render the input part of the group
	 *
	 * @return string
	 */
	protected function render_input(): string {

		$classes = [ 'form-control' ];
		if ( $this->messages()->has() ) {
			$classes[] = 'is-invalid';
		}

		$defaults   = [
			'type'  => $this->get_attribute( 'type' ) ?? 'text',
			'id'    => $this->get_attribute( 'id' ) ?? $this->get_name(),
			'name'  => $this->get_name(),
			'class' => implode( ' ', $classes ),
			'value' => ""
		];
		$attributes = array_merge( $defaults, $this->attributes );

		return $this->render_tag( 'input', $attributes );
	}

	/**
	 * Render the field errors
	 * @return string
	 */
	protected function render_errors(): string {
		$html = '';
		if ( $this->messages()->has() ) {
			$messages = $this->messages()->all();
			$html     .= '<div class="invalid-feedback">';
			if ( 1 === count( $messages ) ) {
				$html .= esc_html( $messages[0]->text() );
			} else {
				foreach ( $messages as $message ) {
					$html .= sprintf( '<div>%s</div>', esc_html( $message->text() ) );
				}
			}
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * Render the field description
	 *
	 * @return string
	 */
	protected function render_description(): string {
		if ( empty( $this->description ) ) {
			return '';
		}

		return sprintf( '<div class="form-text">%s</div>', esc_html( $this->description ) );
	}

	/**
	 * Render an HTML tag
	 *
	 * @param  string  $name
	 * @param  array  $attributes
	 * @param  string  $content
	 *
	 * @return string
	 */
	protected function render_tag( string $name, array $attributes = [], string $content = '' ): string {

		$tag = '<' . esc_attr( $name );

		$attrs = [];
		foreach ( $attributes as $key => $value ) {
			$attrs[] = $this->render_attribute( $key, $value );
		}
		$attrs = array_filter( $attrs );

		if ( ! empty( $attrs ) ) {
			$tag .= ' ' . implode( ' ', $attrs );
		}

		// Self-closing if no content
		if ( $content === '' ) {
			return $tag . '/>';
		}

		return sprintf( '%s>%s</%s>', $tag, $content, esc_attr( $name ) );
	}

	/**
	 * Render a tag attribute
	 *
	 * @param  string  $name  The attribute name
	 * @param  mixed  $value  The attribute value
	 *
	 * @return string
	 */
	protected function render_attribute( string $name, mixed $value ): string {
		if ( empty( $name ) ) {
			return '';
		}

		// Boolean attribute
		if ( $value === true ) {
			return esc_attr( $name );
		}

		return sprintf(
			'%s="%s"',
			esc_attr( $name ),
			esc_attr( (string) $value )
		);
	}
}
