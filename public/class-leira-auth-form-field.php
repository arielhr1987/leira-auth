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
class Leira_Auth_Form_Field extends Leira_Auth_Element{

	/**
	 * Field tag
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $tag = 'input';

	/**
	 * Field name
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $name = '';

	/**
	 * Field value
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $value = '';

	/**
	 * Field label text
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $label = '';

	/**
	 * Field description text
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $description = '';

	/**
	 * Field error message
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $error = '';

	/**
	 * The form this field belongs to
	 *
	 * @var null|Leira_Auth_Form
	 * @since 1.0.0
	 */
	protected $form = null;

	/**
	 * Field priority in the form
	 *
	 * @var int
	 * @since 1.0.0
	 */
	protected $priority = 10;

	/**
	 * Args to build the field
	 *
	 * @var string[]
	 * @since 1.0.0
	 */
	protected $args = array(
		'form_group_start' => '<div class="form-group">',
		'form_group_end'   => '</div>',
		'label'            => '<label for="%s">%s</label>',
		'input'            => '%',
		'description'      => '<small class="form-text text-muted">%s</small>',
		'error'            => '<div class="invalid-feedback">%s</div>',
	);

	/**
	 * Leira_Auth_Form_Field constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->add_class( 'form-control' );
	}

	/**
	 * Render field element
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function render() {

		$out = $this->args['form_group_start'] . PHP_EOL;

		if ( $this->label ) {
			$out .= sprintf( $this->args['label'], $this->get_attr( 'id', '' ), sanitize_text_field( $this->label ) ) . PHP_EOL;
		}

		if ( $this->error ) {
			$this->add_class( 'is-invalid' );
		}
		$out .= $this->input() . PHP_EOL;

		if ( $this->description ) {
			$out .= sprintf( $this->args['description'], sanitize_text_field( $this->description ) ) . PHP_EOL;
		}

		if ( $this->error ) {
			$out .= sprintf( $this->args['error'], sanitize_text_field( $this->error ) ) . PHP_EOL;
		}

		$out .= $this->args['form_group_end'] . PHP_EOL;

		$out = apply_filters( 'leira_auth_form_field_output', $out, $this );

		return $out;
	}

	/**
	 * Convert to string the field
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function __toString() {
		return $this->render();
	}

	/**
	 * Render the input element of the field
	 *
	 * @return string
	 * @since 1.0.0
	 */
	protected function input() {
		return Leira_Auth_Element::render();
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_name() {
		return $this->get_attr( 'name', '' );
	}

	/**
	 * @param string $name
	 *
	 * @return Leira_Auth_Form_Field
	 * @since 1.0.0
	 */
	public function set_name( $name ) {
		$this->set_attr( 'name', $name );

		return $this;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_value() {
		return $this->get_attr( 'value' );
	}

	/**
	 * @param string $value
	 *
	 * @return Leira_Auth_Form_Field
	 * @since 1.0.0
	 */
	public function set_value( $value ) {
		$this->set_attr( 'value', $value );

		return $this;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * @param string $label
	 *
	 * @return Leira_Auth_Form_Field
	 * @since 1.0.0
	 */
	public function set_label( $label ) {
		$this->label = $label;

		return $this;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * @param string $description
	 *
	 * @return Leira_Auth_Form_Field
	 * @since 1.0.0
	 */
	public function set_description( $description ) {
		$this->description = $description;

		return $this;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_error() {
		return $this->error;
	}

	/**
	 * @param string $error
	 *
	 * @return Leira_Auth_Form_Field
	 * @since 1.0.0
	 */
	public function set_error( $error ) {
		$this->error = $error;

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
	 * @return Leira_Auth_Form_Field
	 * @since 1.0.0
	 */
	public function set_priority( $priority ) {
		$this->priority = $priority;

		return $this;
	}

	/**
	 * @return string[]
	 * @since 1.0.0
	 */
	public function get_args() {
		return $this->args;
	}

	/**
	 * @param string[] $args
	 *
	 * @return Leira_Auth_Form_Field
	 * @since 1.0.0
	 */
	public function set_args( $args ) {
		$this->args = $args;

		return $this;
	}

	/**
	 * Get the form this field belongs to
	 *
	 * @return Leira_Auth_Form|null
	 * @since 1.0.0
	 */
	public function get_form() {
		return $this->form;
	}

	/**
	 * Set this field form
	 *
	 * @param Leira_Auth_Form|null $form
	 *
	 * @since 1.0.0
	 */
	public function set_form( $form ) {
		if ( $form instanceof Leira_Auth_Form ) {
			$this->form = $form;
		}
	}

}
