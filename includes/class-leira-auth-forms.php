<?php

require_once plugin_dir_path( __FILE__ ) . 'class-leira-auth-element.php';

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Leira_Auth
 * @subpackage Leira_Auth/includes
 * @author     Ariel <arielhr1987@gmail.com>
 * @since      1.0.0
 */
class Leira_Auth_Forms{

	/**
	 * Form instances
	 *
	 * @var array
	 * @since 1.0.0
	 */
	protected $forms = array();

	/**
	 * Get form instance
	 *
	 * @param $name
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get( $name ) {
		if ( isset( $this->forms[ $name ] ) ) {
			return $this->forms[ $name ];
		}
		//create form instance
		$forms = array(
			'login'
		);
		if ( in_array( $name, $forms ) ) {
			$name = trim( strtolower( $name ) );
			require_once plugin_dir_path( __FILE__ ) . "forms/class-leira-auth-form-$name.php";
			$class    = "Leira_Auth_Form_" . ucfirst( $name );
			$instance = new $class();
			$this->set( $name, $instance );

			return $this->forms[ $name ];
		}

		return false;
	}

	/**
	 * Set form instance
	 *
	 * @param string          $name The form name
	 * @param Leira_Auth_Form $form The form instance
	 *
	 * @since 1.0.0
	 */
	public function set( $name, $form ) {
		if ( $form instanceof Leira_Auth_Form ) {
			$this->forms[ $name ] = $form;
		}
	}
}
