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
class Leira_Auth_Form_Field{

	/**
	 * @var null
	 */
	protected $name = null;

	/**
	 * @var int
	 */
	protected $priority = 10;

	/**
	 * Leira_Auth_Form_Field constructor.
	 *
	 * @param $name string The name of the field
	 */
	public function __construct( $name ) {

	}

	/**
	 * @return null
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param null $name
	 */
	public function setName( $name ) {
		$this->name = $name;
	}

	/**
	 * @return int
	 */
	public function getPriority() {
		return $this->priority;
	}

	/**
	 * @param int $priority
	 */
	public function setPriority( $priority ) {
		$this->priority = $priority;
	}

	/**
	 * Determine if the current field is valid
	 *
	 * @return bool
	 */
	public function valid() {
		return true;
	}

	/**
	 *
	 */
	public function render() {

	}

}
