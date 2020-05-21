<?php

/**
 * Class Leira_Auth_Element
 *
 *
 */
class Leira_Auth_Element{

	/**
	 * The element tag
	 *
	 * @var string
	 */
	protected $tag = 'div';

	/**
	 * Element attributes
	 *
	 * @var string[]
	 * @since 1.0.0
	 */
	protected $attributes = array();

	/**
	 * Self closer elements
	 *
	 * @var string[]
	 * @since 1.0.0
	 */
	protected $self_closers = array( 'input', 'img', 'hr', 'br', 'meta', 'link' );

	/**
	 * Leira_Auth_Element constructor.
	 *
	 * @param string $tag
	 */
	public function __construct( $tag = 'div' ) {
		$this->set_tag( $tag );
	}

	/**
	 * Add class
	 *
	 * @param string $attr    The attribute to add
	 * @param null   $default Default value for the attribute
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_attr( $attr, $default = null ) {
		return isset( $this->attributes[ $attr ] ) ? $this->attributes[ $attr ] : $default;
	}

	/**
	 * @param string $attr  The attribute to set
	 * @param null   $value The value for the attribute, default null
	 *
	 * @return $this
	 * @since 1.0.0
	 */
	public function set_attr( $attr, $value = null ) {
		if ( ! in_array( $attr, array( 'class' ) ) && is_string( $attr ) ) {
			$this->attributes[ $attr ] = $value;
		}

		return $this;
	}

	/**
	 * Remove element attribute
	 *
	 * @param string $attr The attribute to return
	 *
	 * @return $this
	 * @since 1.0.0
	 */
	public function remove_attr( $attr ) {
		unset( $this->attributes[ $attr ] );

		return $this;
	}

	/**
	 * Add class
	 *
	 * @param string $class The class or classes to add to the element
	 *
	 * @return $this
	 * @since 1.0.0
	 */
	public function add_class( $class ) {
		if ( ! isset( $this->attributes['class'] ) || ! is_string( $this->attributes['class'] ) ) {
			$this->attributes['class'] = '';
		}

		if ( is_string( $class ) ) {
			$this->attributes['class'] = $this->str_unique_words( $this->attributes['class'] . ' ' . $class );
		}

		return $this;
	}

	/**
	 * Remove class
	 *
	 * @param string $class The class to remove from element
	 *
	 * @return $this
	 * @since 1.0.0
	 */
	public function remove_class( $class ) {
		if ( ! isset( $this->attributes['class'] ) || ! is_string( $this->attributes['class'] ) ) {
			$this->attributes['class'] = '';
		}

		if ( is_string( $class ) ) {
			$regex   = '\b' . trim( $class ) . '\b';
			$classes = preg_replace( $regex, '', $this->attributes['class'] );//remove the given class
			$classes = $this->str_unique_words( $classes );

			$this->attributes['class'] = $classes;
		}

		return $this;
	}

	/**
	 * Determine if element has a class
	 *
	 * @param string $class
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function has_class( $class ) {
		if ( ! isset( $this->attributes['class'] ) || ! is_string( $this->attributes['class'] ) ) {
			$this->attributes['class'] = '';
		}

		return is_string( $class ) && ( mb_strpos( $this->attributes['class'], trim( $class ) ) );
	}

	/**
	 * Delete all repeated words in a give string.
	 * This method also replace multiple spaces by a single space.
	 *
	 * @param string $str
	 *
	 * @return string
	 * @since 1.0.0
	 */
	protected function str_unique_words( $str ) {
		$regex = '(\b[a-zA-Z0-9_-]+\b)(?=.+\1)';
		$str   = preg_replace( $regex, '', $str ); //remove al. repeated words
		$str   = preg_replace( '\s+', ' ', trim( $str ) ); //remove extra spaces

		return $str;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_tag() {
		return $this->tag;
	}

	/**
	 * @param string $tag
	 *
	 * @since 1.0.0
	 */
	public function set_tag( $tag ) {
		$this->tag = $tag;
	}

	/**
	 * @return string[]
	 * @since 1.0.0
	 */
	public function get_self_closers() {
		return $this->self_closers;
	}

	/**
	 * @param string[] $self_closers
	 *
	 * @since 1.0.0
	 */
	public function set_self_closers( $self_closers ) {
		$this->self_closers = $self_closers;
	}

	/**
	 * Convert to string
	 *
	 * @return string
	 */
	public function __toString() {
		$html = '<' . $this->tag;

		//add attributes
		if ( count( $this->attributes ) ) {
			foreach ( $this->attributes as $key => $value ) {
				if ( $key != 'text' ) {
					$html .= ' ' . $key . '="' . $value . '"';
				}
			}
		}

		//closing
		if ( ! in_array( $this->tag, $this->self_closers ) ) {
			$html .= '>';
			if ( isset( $this->attributes['text'] ) ) {
				$html .= $this->attributes['text'];
			}
			$html .= '</' . $this->tag . '>';
		} else {
			$html .= ' />';
		}

		//return it
		return $html;
	}

}