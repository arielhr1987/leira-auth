<?php

/**
 * Class to for html element manipulation.
 *
 * @package    Leira_Auth
 * @subpackage Leira_Auth/public
 * @author     Ariel <arielhr1987@gmail.com>
 * @since      1.0.0
 */
class Leira_Auth_Dom{

	/**
	 * Element tag
	 *
	 * @var string
	 */
	protected $tag = 'div';

	/**
	 * Array of element attributes
	 *
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * Self closer tags
	 *
	 * @var array
	 */
	protected $self_closers = array( 'input', 'img', 'hr', 'br', 'meta', 'link' );

	/**
	 * Child elements
	 *
	 * @var array
	 */
	protected $nodes = array();

	/**
	 * @var int
	 * @since 1.0.0
	 */
	protected $priority = 10;

	/**
	 * Leira_Auth_Dom constructor.
	 *
	 * @param string $tag
	 */
	public function __construct( $tag = 'div' ) {
		$this->tag = $tag;
	}

	/**
	 * @return string
	 */
	public function get_tag() {
		return $this->tag;
	}

	/**
	 * @param string $tag
	 *
	 * @return Leira_Auth_Dom
	 */
	public function set_tag( $tag ) {
		$this->tag = $tag;

		return $this;
	}

	/**
	 * Get child nodes
	 */
	public function &get_nodes() {

		return $this->nodes;

		$priorities   = array();
		$sorted_nodes = array();

		// Prioritize the fields
		foreach ( $this->nodes as $node ) {
			$priority = $node->get_priority();
			if ( ! isset( $priorities[ $priority ] ) ) {
				$priorities[ $priority ] = array();
			}
			$priorities[ $priority ][] = $node;
		}

		ksort( $priorities );

		// Sort the fields
		foreach ( $priorities as $priority => $fields ) {
			foreach ( $fields as $node ) {
				$sorted_nodes[] = $node;
			}
		}
		unset( $priorities );

		return $sorted_nodes;
	}

	/**
	 * @param $node
	 *
	 * @return Leira_Auth_Dom
	 */
	public function append( $node ) {
		$this->nodes[] = $node;

		return $this;
	}

	/**
	 * @param $node
	 *
	 * @return Leira_Auth_Dom
	 */
	public function prepend( $node ) {
		array_unshift( $this->nodes );

		return $this;
	}

	/**
	 * @param $node
	 * @param $index
	 *
	 * @return Leira_Auth_Dom
	 */
	public function insert( $node, $index ) {
		array_splice( $this->nodes, $index, 0, $node );

		return $this;
	}

	/**
	 * Replace a node
	 *
	 * @param $newNode
	 * @param $oldNode
	 *
	 * @return Leira_Auth_Dom
	 */
	public function replace( $newNode, $oldNode ) {
		$index = array_search( $oldNode, $this->nodes );
		if ( $index !== false ) {
			$this->nodes[ $index ] = $newNode;
		}

		return $this;
	}

	/**
	 * @param $node
	 *
	 * @return $this
	 */
	public function remove( $node ) {
		if ( is_integer( $node ) ) {
			$index = $node;
		} else {
			$index = array_search( $node, $this->nodes );
		}
		array_splice( $this->nodes, $index, 1 );

		return $this;
	}

	/**
	 * @return int
	 * @since 1.0.0
	 */
	public function get_priority() {
		return $this->priority;
	}

	/**
	 * @param int $priority
	 *
	 * @return Leira_Auth_Dom
	 * @since 1.0.0
	 */
	public function set_priority( $priority ) {
		$this->priority = $priority;

		return $this;
	}

	/**
	 * Get the array of all attributes
	 *
	 * @return array
	 */
	public function get_attributes() {
		return $this->attributes;
	}

	/**
	 * Check if element has an attribute set
	 *
	 * @param $attr
	 *
	 * @return bool
	 */
	public function has_attribute( $attr ) {
		return isset( $this->attributes[ $attr ] );
	}

	/**
	 * @param string $attr The attribute name
	 *
	 * @return string|array The value of the attribute
	 */
	public function get_attribute( $attr ) {
		return isset( $this->attributes[ $attr ] ) ? $this->attributes[ $attr ] : false;
	}

	/**
	 * Set an attribute for the element
	 *
	 * @param string      $attr  The attribute
	 * @param string|bool $value The value for the attribute
	 *
	 * @return Leira_Auth_Dom
	 */
	public function set_attribute( $attr, $value = false ) {
		if ( in_array( $attr, array( 'class', 'style' ) ) ) {
			//use specific methods for these attributes
		} else {
			$this->attributes[ $attr ] = trim( $value );
		}

		return $this;
	}

	/**
	 * Delete an attribute
	 *
	 * @param string $attr
	 *
	 * @return Leira_Auth_Dom
	 */
	public function delete_attribute( $attr ) {
		if ( in_array( $attr, array( 'class', 'style' ) ) ) {
			$this->attributes[ $attr ] = array();
		} else {
			unset( $this->attributes[ $attr ] );
		}

		return $this;

	}

	/**
	 * Determine if element has class
	 *
	 * @param string $class
	 *
	 * @return bool
	 */
	public function has_class( $class ) {
		$has_class = array_search( $class, $this->attributes['class'] );

		return $has_class !== false;
	}

	/**
	 * Add class to element
	 *
	 * @param $class
	 *
	 * @return Leira_Auth_Dom
	 */
	public function add_class( $class ) {
		if ( is_string( $class ) ) {
			$class = explode( ' ', $class );
		}
		$class = (array) $class;
		$class = array_filter( $class );

		if ( ! isset( $this->attributes['class'] ) || ! is_array( $this->attributes['class'] ) ) {
			$this->attributes['class'] = array();
		}
		$class = array_merge( $this->attributes['class'], $class );
		$class = array_unique( $class );

		$this->attributes['class'] = $class;

		return $this;
	}

	/**
	 * Remove a class from the array of classes
	 *
	 * @param string|array $class
	 *
	 * @return Leira_Auth_Dom
	 */
	public function delete_class( $class ) {
		if ( is_string( $class ) ) {
			$class = explode( ' ', $class );
		}
		$class = (array) $class;
		$class = array_filter( $class );

		foreach ( $class as $c ) {
			$index = array_search( $c, $this->attributes['class'] );
			if ( $index !== false ) {
				unset( $this->attributes['class'][ $index ] );
			}
		}

		return $this;
	}

	/**
	 * Determine if an style is set
	 *
	 * @param $name
	 *
	 * @return bool
	 */
	public function has_style( $name ) {
		return isset( $this->attributes['style'][ $name ] );
	}

	/**
	 * Get style value
	 *
	 * @param $name
	 *
	 * @return bool
	 */
	public function get_style( $name ) {
		return isset( $this->attributes['style'][ $name ] ) ? $this->attributes['style'][ $name ] : false;
	}

	/**
	 * @param string|array $name
	 * @param string       $value
	 *
	 * @return Leira_Auth_Dom
	 */
	public function set_style( $name, $value ) {
		if ( ! isset( $this->attributes['style'] ) || ! is_array( $this->attributes['style'] ) ) {
			$this->attributes['style'] = array();
		}

		if ( is_array( $name ) ) {
			$this->attributes['style'] = array_merge( $this->attributes['style'], $name );
		} else {
			$this->attributes['style'][ $name ] = $value;
		}

		return $this;
	}

	/**
	 * Remove a class from the array of classes
	 *
	 * @param string|array $name
	 *
	 * @return Leira_Auth_Dom
	 */
	public function remove_style( $name ) {
		if ( is_string( $name ) ) {
			$name = explode( ' ', $name );
		}
		$name = (array) $name;
		$name = array_filter( $name );

		foreach ( $name as $n ) {
			$index = array_search( $n, $this->attributes['style'] );
			if ( $index !== false ) {
				unset( $this->attributes['style'][ $index ] );
			}
		}

		return $this;
	}

	/**
	 * Convert element to string
	 */
	public function __toString() {

		$output = '<' . $this->tag;

		//add attributes
		foreach ( $this->attributes as $key => $value ) {

			if ( $key == 'class' ) {
				$value = implode( ' ', $value );
			}

			if ( $key == 'style' ) {
				foreach ( $this->attributes['style'] as $style => $style_value ) {
					$value = ' ' . esc_attr( $style ) . '="' . esc_attr( $style_value ) . '"';
				}
			}

			$output .= ' ' . esc_attr( $key );
			if ( $value !== false ) {
				$output .= '="' . esc_attr( $value ) . '"';
			}
		}

		//closing
		if ( ! in_array( $this->tag, $this->self_closers ) ) {
			$output .= '>' . PHP_EOL;
			foreach ( $this->get_nodes() as $node ) {
				$output .= $node . PHP_EOL;
			}
			$output .= '</' . $this->tag . '>' . PHP_EOL;
		} else {
			$output .= '/>' . PHP_EOL;
		}

		//return it
		return $output;
	}
}
