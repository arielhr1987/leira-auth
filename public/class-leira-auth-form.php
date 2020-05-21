<?php

require plugin_dir_path( __FILE__ ) . 'class-leira-auth-form-field.php';
require plugin_dir_path( __FILE__ ) . 'class-leira-auth-element.php';

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Leira_Auth
 * @subpackage Leira_Auth/public
 * @author     Ariel <arielhr1987@gmail.com>
 * @since      1.0.0
 */
class Leira_Auth_Form extends Leira_Auth_Element{

	/**
	 * @var string
	 * @since 1.0.0
	 */
	protected $tag = 'form';

	/**
	 * The name of the form that identify it
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $name;

	/**
	 * Form element attributes
	 *
	 * @var string[]
	 * @since 1.0.0
	 */
	protected $attributes = array(
		'method' => 'post',
		'action' => '/'
	);

	/**
	 * Errors found while validating the form
	 *
	 * @var array
	 * @since 1.0.0
	 */
	protected $errors = array();

	/**
	 * Leira_Auth_Form constructor.
	 *
	 * @param string $tag
	 */
	public function __construct( $tag = 'form' ) {

	}

	/**
	 * Render the form
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function __toString() {

		do_action( 'leira_auth_before_render_form', $this );

		apply_filters( 'leira_auth_form_render', $this );

		do_action( 'leira_auth_after_render_form', $this );

		$output = apply_filters( 'leira_auth_form_output', parent::__toString() );

		return $output;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_name() {
		return $this->get_attribute( 'name' );
	}

	/**
	 * @param string $name
	 *
	 * @return Leira_Auth_Form
	 * @since 1.0.0
	 */
	public function set_name( $name ) {
		$this->set_attribute( 'name', $name );

		return $this;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_action() {
		return apply_filters( 'leira_auth_form_action', $this->get_attr( 'action' ), $this );
	}

	/**
	 * @param string $action
	 *
	 * @return Leira_Auth_Form
	 * @since 1.0.0
	 */
	public function set_action( $action ) {
		$this->set_attribute( 'action', $action );

		return $this;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_method() {
		return apply_filters( 'leira_auth_form_method', $this->get_attribute( 'method' ), $this );
	}

	/**
	 * @param string $method
	 *
	 * @return Leira_Auth_Form
	 * @since 1.0.0
	 */
	public function set_method( $method ) {
		$method = strtolower( $method );
		if ( ! in_array( $method, array( 'get', 'post' ) ) ) {
			$method = 'post';
		}
		$this->set_attribute( 'method', $method );

		return $this;
	}

}
