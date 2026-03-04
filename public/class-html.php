<?php

namespace Leira_Auth\Public;

/**
 * HTML helpers for classes, attributes and element rendering.
 *
 * @since 1.0.0
 */
class Html{

	/**
	 * Merge class names into one normalized class string.
	 *
	 * @param  string|array  ...$classes
	 *
	 * @return string
	 */
	public static function merge_classes( string|array ...$classes ): string {
		$items = [];
		foreach ( $classes as $class_group ) {
			$class_group = is_array( $class_group ) ? $class_group : preg_split( '/\s+/', trim( $class_group ) );
			if ( ! is_array( $class_group ) ) {
				continue;
			}
			foreach ( $class_group as $class_name ) {
				$class_name = trim( (string) $class_name );
				if ( '' === $class_name ) {
					continue;
				}
				$items[] = $class_name;
			}
		}

		return implode( ' ', array_values( array_unique( $items ) ) );
	}

	/**
	 * Determine if a class exists in a class list.
	 *
	 * @param  string|array<string>  $classes
	 * @param  string  $class_name
	 *
	 * @return bool
	 */
	public static function has_class( string|array $classes, string $class_name ): bool {
		$class_name = trim( $class_name );
		if ( '' === $class_name ) {
			return false;
		}

		$normalized = self::merge_classes( $classes );
		if ( '' === $normalized ) {
			return false;
		}

		return in_array( $class_name, explode( ' ', $normalized ), true );
	}

	/**
	 * Remove one class from a class list.
	 *
	 * @param  string|array<string>  $classes
	 * @param  string  $class_name
	 *
	 * @return string
	 */
	public static function remove_class( string|array $classes, string $class_name ): string {
		$class_name = trim( $class_name );
		if ( '' === $class_name ) {
			return self::merge_classes( $classes );
		}

		$normalized = self::merge_classes( $classes );
		if ( '' === $normalized ) {
			return '';
		}

		$items = array_filter(
			explode( ' ', $normalized ),
			static fn( string $item ): bool => $item !== $class_name
		);

		return implode( ' ', $items );
	}

	/**
	 * Convert attribute array to HTML attributes string.
	 *
	 * @param  array<string, mixed>  $attributes
	 *
	 * @return string
	 */
	public static function render_attributes( array $attributes ): string {
		$html = [];
		foreach ( $attributes as $key => $value ) {
			if ( true === $value ) {
				$html[] = esc_attr( $key );
				continue;
			}

			if ( false === $value || null === $value ) {
				continue;
			}

			$html[] = sprintf( '%s="%s"', esc_attr( $key ), esc_attr( (string) $value ) );
		}

		return implode( ' ', $html );
	}

	/**
	 * Render an HTML element.
	 *
	 * @param  string  $tag
	 * @param  array<string, mixed>  $attributes
	 * @param  string  $content
	 *
	 * @return string
	 */
	public static function render_element( string $tag, array $attributes = [], string $content = '' ): string {
		$tag = trim( strtolower( $tag ) );
		if ( empty( $tag ) ) {
			return '';
		}

		$attr = self::render_attributes( $attributes );
		$attr = ! empty( $attr ) ? ' ' . $attr : '';

		$void_tags = [
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

		if ( in_array( $tag, $void_tags, true ) ) {
			return sprintf( '<%1$s%2$s/>', $tag, $attr );
		}

		return sprintf( '<%1$s%2$s>%3$s</%1$s>', $tag, $attr, $content );
	}

}
