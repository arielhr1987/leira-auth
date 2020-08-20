<?php

require_once plugin_dir_path( __FILE__ ) . 'class-leira-auth-form-field.php';

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
class Leira_Auth_Form_Field_Text extends Leira_Auth_Form_Field{

	/**
	 * Leira_Auth_Form_Field_Text constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->set_attr( 'type', 'text' );
	}

}
