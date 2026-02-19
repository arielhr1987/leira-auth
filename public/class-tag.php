<?php

namespace Leira_Auth\Public;

/**
 * Helper class to manage an HTML tag and its attributes.
 *
 * @since 1.0.0
 */
class Tag{

	/**
	 * Tag name.
	 *
	 * @var string
	 */
	protected string $tag = 'div';

	/**
	 * Tag attributes.
	 *
	 * @var array<string, mixed>
	 */
	protected array $attributes = [];

	/**
	 * Default self-closing tags.
	 *
	 * @var array<int, string>
	 */
	protected array $self_closing_tags = [
		'area',
		'base',
		'br',
		'col',
		'embed',
		'hr',
		'img',
		'input',
		'link',
		'meta',
		'param',
		'source',
		'track',
		'wbr',
	];

	/**
	 * Constructor.
	 *
	 * @param  string  $tag
	 * @param  array<string, mixed>  $attributes
	 */
	public function __construct( string $tag = 'div', array $attributes = [] ) {
		$this->set_tag( $tag );
		$this->set_attributes( $attributes );
	}

	/**
	 * Set tag name.
	 *
	 * @param  string  $tag
	 *
	 * @return self
	 */
	public function set_tag( string $tag ): self {
		$tag = strtolower( trim( $tag ) );
		if ( '' !== $tag ) {
			$this->tag = $tag;
		}

		return $this;
	}

	/**
	 * Get tag name.
	 *
	 * @return string
	 */
	public function get_tag(): string {
		return $this->tag;
	}

	/**
	 * Set one attribute.
	 *
	 * @param  string  $name
	 * @param  mixed  $value
	 *
	 * @return self
	 */
	public function set_attribute( string $name, mixed $value ): self {
		$name = trim( $name );
		if ( '' === $name ) {
			return $this;
		}

		$this->attributes[ $name ] = $value;

		return $this;
	}

	/**
	 * Set many attributes at once.
	 *
	 * @param  array<string, mixed>  $attributes
	 *
	 * @return self
	 */
	public function set_attributes( array $attributes ): self {
		foreach ( $attributes as $name => $value ) {
			$this->set_attribute( (string) $name, $value );
		}

		return $this;
	}

	/**
	 * Get one attribute.
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
	 * Remove one attribute.
	 *
	 * @param  string  $name
	 *
	 * @return self
	 */
	public function remove_attribute( string $name ): self {
		unset( $this->attributes[ $name ] );

		return $this;
	}

	/**
	 * Return all attributes.
	 *
	 * @return array<string, mixed>
	 */
	public function attributes(): array {
		return $this->attributes;
	}

	/**
	 * Add one or many classes.
	 *
	 * @param  string  $classes
	 *
	 * @return self
	 */
	public function add_class( string $classes ): self {
		$incoming = $this->parse_classes( $classes );
		if ( empty( $incoming ) ) {
			return $this;
		}

		$current = $this->classes();
		$merged  = array_values( array_unique( array_merge( $current, $incoming ) ) );

		$this->attributes['class'] = implode( ' ', $merged );

		return $this;
	}

	/**
	 * Delete one or many classes.
	 *
	 * @param  string  $classes
	 *
	 * @return self
	 */
	public function delete_class( string $classes ): self {
		$remove = $this->parse_classes( $classes );
		if ( empty( $remove ) ) {
			return $this;
		}

		$current = $this->classes();
		$next    = array_values( array_diff( $current, $remove ) );

		if ( empty( $next ) ) {
			unset( $this->attributes['class'] );
		} else {
			$this->attributes['class'] = implode( ' ', $next );
		}

		return $this;
	}

	/**
	 * Alias for delete_class().
	 *
	 * @param  string  $classes
	 *
	 * @return self
	 */
	public function remove_class( string $classes ): self {
		return $this->delete_class( $classes );
	}

	/**
	 * Toggle class on/off.
	 *
	 * @param  string  $class
	 * @param  bool|null  $force
	 *
	 * @return self
	 */
	public function toggle_class( string $class, ?bool $force = null ): self {
		$parts = $this->parse_classes( $class );
		if ( empty( $parts ) ) {
			return $this;
		}

		$class = $parts[0];
		$has   = $this->has_class( $class );

		if ( true === $force || ( null === $force && ! $has ) ) {
			return $this->add_class( $class );
		}

		return $this->delete_class( $class );
	}

	/**
	 * Determine if a class exists.
	 *
	 * @param  string  $class
	 *
	 * @return bool
	 */
	public function has_class( string $class ): bool {
		$parts = $this->parse_classes( $class );
		if ( empty( $parts ) ) {
			return false;
		}

		return in_array( $parts[0], $this->classes(), true );
	}

	/**
	 * Get a normalized class list.
	 *
	 * @return array<int, string>
	 */
	public function classes(): array {
		$value = $this->attributes['class'] ?? '';
		if ( is_array( $value ) ) {
			$value = implode( ' ', $value );
		}

		return $this->parse_classes( (string) $value );
	}

	/**
	 * Render all attributes.
	 *
	 * @return string
	 */
	public function render_attributes(): string {
		$parts = [];

		foreach ( $this->attributes as $name => $value ) {
			$attribute = $this->render_attribute( (string) $name, $value );
			if ( '' !== $attribute ) {
				$parts[] = $attribute;
			}
		}

		return implode( ' ', $parts );
	}

	/**
	 * Render opening tag.
	 *
	 * @return string
	 */
	public function render_opening_tag(): string {
		$tag   = '<' . esc_attr( $this->tag );
		$attrs = $this->render_attributes();
		if ( '' !== $attrs ) {
			$tag .= ' ' . $attrs;
		}

		return $this->is_self_closing() ? $tag . '/>' : $tag . '>';
	}

	/**
	 * Render full tag with optional content.
	 *
	 * @param  string  $content
	 *
	 * @return string
	 */
	public function render( string $content = '' ): string {
		if ( $this->is_self_closing() ) {
			return $this->render_opening_tag();
		}

		return $this->render_opening_tag() . $content . $this->render_closing_tag();
	}

	/**
	 * Render closing tag.
	 *
	 * @return string
	 */
	public function render_closing_tag(): string {
		if ( $this->is_self_closing() ) {
			return '';
		}

		return '</' . esc_attr( $this->tag ) . '>';
	}

	/**
	 * Render one attribute.
	 *
	 * @param  string  $name
	 * @param  mixed  $value
	 *
	 * @return string
	 */
	protected function render_attribute( string $name, mixed $value ): string {
		$name = trim( $name );
		if ( '' === $name ) {
			return '';
		}

		if ( true === $value ) {
			return esc_attr( $name );
		}

		if ( false === $value || null === $value ) {
			return '';
		}

		if ( 'class' === $name ) {
			$value = implode( ' ', $this->classes() );
		}

		return sprintf( '%s="%s"', esc_attr( $name ), esc_attr( (string) $value ) );
	}

	/**
	 * Check if the tag is self-closing.
	 *
	 * @return bool
	 */
	public function is_self_closing(): bool {
		return in_array( $this->tag, $this->self_closing_tags, true );
	}

	/**
	 * Parse classes from string.
	 *
	 * @param  string  $classes
	 *
	 * @return array<int, string>
	 */
	protected function parse_classes( string $classes ): array {
		$list = preg_split( '/\s+/', trim( $classes ) ) ?: [];
		$list = array_map( 'trim', $list );
		$list = array_filter( $list );

		return array_values( array_unique( $list ) );
	}
}
