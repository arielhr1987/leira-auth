<?php

require_once plugin_dir_path( __FILE__ ) . 'class-leira-auth-element.php';

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
	 * Errors found while validating the form
	 *
	 * @var array
	 * @since 1.0.0
	 */
	protected $errors = array();

	/**
	 * Form fields
	 *
	 * @var array
	 * @since 1.0.0
	 */
	protected $fields = array();

	protected $options = array();

	protected $default_options = array();

	/**
	 * Render the form
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function render() {

		do_action( 'leira_auth_before_render_form', $this );

		$output = '';

		/**
		 * Render errors
		 */
		if ( $this->errors ) {

		}

		/**
		 * Render fields
		 */
		$fields = $this->get_fields();
		foreach ( $fields as $field ) {
			$output .= $field->render();
		}
		$this->set_attr( 'text', $output );

		/**
		 * Filter output
		 */
		$output = apply_filters( 'leira_auth_form_output', parent::render() );

		return $output;
	}

	/**
	 * Validate current form request
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function validate() {
		return false;
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
//	public function set_action( $action ) {
//		$this->set_attr( 'action', $action );
//
//		return $this;
//	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_method() {
		return apply_filters( 'leira_auth_form_method', $this->get_attr( 'method' ), $this );
	}

	/**
	 * @param string $method
	 *
	 * @return Leira_Auth_Form
	 * @since 1.0.0
	 */
//	public function set_method( $method ) {
//		$method = strtolower( $method );
//		if ( ! in_array( $method, array( 'get', 'post' ) ) ) {
//			$method = 'post';
//		}
//		$this->set_attr( 'method', $method );
//
//		return $this;
//	}

	/**
	 * Add field to form
	 *
	 * @param $field
	 *
	 * @return Leira_Auth_Form
	 * @since 1.0.0
	 */
	public function add_field( $field ) {
		if ( $field instanceof Leira_Auth_Form_Field ) {
			$this->fields[ $field->get_name() ] = $field;
			$field->set_form( $this );
		}

		return $this;
	}

	/**
	 * Remove field from form
	 *
	 * @param $field
	 *
	 * @return Leira_Auth_Form
	 * @since 1.0.0
	 */
	public function remove_field( $field ) {
		if ( $field instanceof Leira_Auth_Form_Field ) {
			unset( $this->fields[ $field->get_name() ] );
		} else {
			unset( $this->fields[ $field ] );
		}

		return $this;
	}

	/**
	 * Get form field by name
	 *
	 * @param $field
	 *
	 * @return bool|mixed
	 * @since 1.0.0
	 */
	public function get_field( $field ) {
		if ( isset( $this->fields[ $field ] ) ) {
			return $this->fields[ $field ];
		}

		return false;
	}

	/**
	 * Get all field order by priority
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_fields() {
		$priorities    = array();
		$sorted_fields = array();

		// Prioritize the fields
		foreach ( $this->fields as $field ) {
			$priority = $field->get_priority();
			if ( ! isset( $priorities[ $priority ] ) ) {
				$priorities[ $priority ] = array();
			}
			$priorities[ $priority ][] = $field;
		}

		ksort( $priorities );

		// Sort the fields
		foreach ( $priorities as $priority => $fields ) {
			foreach ( $fields as $field ) {
				$sorted_fields[] = $field;
			}
		}
		unset( $priorities );

		return $sorted_fields;
	}

}
