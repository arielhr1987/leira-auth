<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Leira_Auth
 * @subpackage Leira_Auth/public
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Auth_Form{

	/**
	 * @var
	 */
	public $name;

	/**
	 * @var string
	 */
	public $action = '/';

	/**
	 * Fields in the form
	 *
	 * @var array
	 */
	public $fields = array();

	/**
	 *
	 */
	public function render() {

		$output = '';

		do_action( 'leira_auth_before_render_form', $this );


		do_action( 'leira_auth_after_render_form', $this );

		$output = apply_filters( 'leira_auth_form_output', $output, $this );

	}

	/**
	 * @param $name
	 * @param $field
	 * @param $priority
	 *
	 * @return bool
	 */
	public function add_field( $name, $field, $priority ) {
		if ( $field instanceof Leira_Auth_Form_Field ) {
			//throw exception
			return false;
		}

		$this->fields[ $name ] = array(
			'field'    => $field,
			'priority' => $priority
		);

		return true;
	}

	/**
	 * @param $name
	 */
	public function remove_field( $name ) {
		if ( isset( $this->fields[ $name ] ) ) {
			unset( $this->fields[ $name ] );
		}
	}

}
