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
class Leira_Auth_Form_Field_Checkbox extends Leira_Auth_Form_Field{

	/**
	 * Args to build the field
	 *
	 * @var string[]
	 * @since 1.0.0
	 */
	protected $args = array(
		'form_group_start' => '<div class="form-group"><div class="form-check">',
		'form_group_end'   => '</div></div>',
		'label'            => '<label for="%s" class="form-check-label">%s</label>',
		'description'      => '<small class="form-text text-muted">%s</small>',
		'error'            => '<div class="invalid-feedback">%s</div>',
	);

	/**
	 * Leira_Auth_Form_Field_Text constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->set_attr( 'type', 'checkbox' );
		$this->remove_class( 'form-control' );
		$this->add_class( 'form-check-input' );
		$this->set_attr( 'value', '1' );
	}

	/**
	 * Render checkbox field element
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function render() {

		$out = $this->args['form_group_start'] . PHP_EOL;

		if ( $this->error ) {
			$this->add_class( 'is-invalid' );
		}
		$out .= $this->input() . PHP_EOL;

		if ( $this->label ) {
			$out .= sprintf( $this->args['label'], $this->get_attr( 'id', '' ), sanitize_text_field( $this->label ) ) . PHP_EOL;
		}

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

}
