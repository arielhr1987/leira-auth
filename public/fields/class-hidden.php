<?php

namespace Leira_Auth\Public\Fields;

/**
 * Hidden input field.
 *
 * Hidden inputs do not render labels, descriptions, or inline error output.
 *
 * @since 1.0.0
 */
class Hidden extends Field{

	/**
	 * Field attributes.
	 *
	 * @var array<string, mixed>
	 */
	protected array $attributes = [
		'type' => 'hidden',
	];

	/**
	 * Class constructor.
	 *
	 * @param  string  $name
	 */
	public function __construct( string $name ) {
		parent::__construct( $name );
		$this->attributes['name'] = $name;
	}

	/**
	 * Get field name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return (string) ( $this->attributes['name'] ?? $this->name );
	}

	/**
	 * Get field value.
	 *
	 * @return mixed
	 */
	public function get_value(): mixed {
		return $this->attributes['value'] ?? null;
	}

	/**
	 * Set field value.
	 *
	 * @param  mixed  $value
	 *
	 * @return self
	 */
	public function set_value( mixed $value ): self {
		$this->attributes['value'] = $value;

		return $this;
	}

	/**
	 * Set custom attribute.
	 *
	 * @param  string  $name
	 * @param  mixed  $value
	 *
	 * @return self
	 */
	public function set_attribute( string $name, mixed $value ): self {
		$this->attributes[ $name ] = $value;

		return $this;
	}

	/**
	 * Get custom attribute.
	 *
	 * @param  string  $name
	 * @param  mixed|null  $default
	 *
	 * @return mixed
	 */
	public function get_attribute( string $name, mixed $default = null ): mixed {
		return $this->attributes[ $name ] ?? $default;
	}

	/**
	 * Hidden fields should not render inline errors.
	 *
	 * @return bool
	 */
	public function should_render_errors_inline(): bool {
		return false;
	}

	/**
	 * Render hidden field markup.
	 *
	 * @return string
	 */
	public function render(): string {
		$attributes = array_merge(
			[
				'type'  => 'hidden',
				'id'    => $this->get_attribute( 'id', $this->get_name() ),
				'name'  => $this->get_name(),
				'value' => $this->get_value() ?? '',
			],
			$this->attributes
		);

		return $this->render_tag( 'input', $attributes );
	}

	/**
	 * Render an HTML tag.
	 *
	 * @param  string  $name
	 * @param  array<string, mixed>  $attributes
	 *
	 * @return string
	 */
	protected function render_tag( string $name, array $attributes ): string {
		$parts = [];
		foreach ( $attributes as $key => $value ) {
			if ( '' === (string) $key ) {
				continue;
			}

			if ( true === $value ) {
				$parts[] = esc_attr( $key );
				continue;
			}

			$parts[] = sprintf( '%s="%s"', esc_attr( $key ), esc_attr( (string) $value ) );
		}

		$attrs = implode( ' ', array_filter( $parts ) );

		return sprintf( '<%s %s/>', esc_attr( $name ), $attrs );
	}
}
