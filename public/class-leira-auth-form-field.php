<?php

require plugin_dir_path( __FILE__ ) . 'class-leira-auth-dom.php';

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
class Leira_Auth_Form_Field{

	/**
	 * The id of the input element
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $id = '';

	/**
	 * @var string
	 * @since 1.0.0
	 */
	protected $name = '';

	/**
	 * @var string
	 * @since 1.0.0
	 */
	protected $value = '';

	/**
	 * @var array
	 * @since 1.0.0
	 */
	protected $options = array();

	/**
	 * @var string
	 * @since 1.0.0
	 */
	protected $label = '';

	/**
	 * @var string
	 * @since 1.0.0
	 */
	protected $description = '';

	/**
	 * @var string
	 * @since 1.0.0
	 */
	protected $type = 'text';

	/**
	 * Leira_Auth_Form_Field constructor.
	 */
	public function __construct() {

	}

	/**
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @param string $id
	 *
	 * @return Leira_Auth_Form_Field
	 */
	public function set_id( $id ) {
		$this->id = $id;

		return $this;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @param string $name
	 *
	 * @return Leira_Auth_Form_Field
	 * @since 1.0.0
	 */
	public function set_name( $name ) {
		$this->name = $name;

		return $this;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * @param string $value
	 *
	 * @return Leira_Auth_Form_Field
	 * @since 1.0.0
	 */
	public function set_value( $value ) {
		$this->value = $value;

		return $this;
	}

	/**
	 * @return array
	 * @since 1.0.0
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * @param array $options
	 *
	 * @return Leira_Auth_Form_Field
	 * @since 1.0.0
	 */
	public function set_options( $options ) {
		$this->options = $options;

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
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * @param string $type
	 *
	 * @return Leira_Auth_Form_Field
	 */
	public function set_type( $type ) {
		$this->type = $type;

		return $this;
	}

	/**
	 * Determine if the current field is valid
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function valid() {
		return true;
	}

	/**
	 * Render the field
	 *
	 * @since 1.0.0
	 */
	public function __toString() {

		$group = new Leira_Auth_Dom();
		$group->add_class( 'form-group' );

		$label = new Leira_Auth_Dom( 'label' );
		$label->set_attribute( 'for', $this->get_id() )
		      ->append( $this->label );


		$input = new Leira_Auth_Dom( 'input' );
		$input->set_attribute( 'id', $this->get_id() )
		      ->set_attribute( 'name', $this->get_name() )
		      ->set_attribute( 'type', $this->get_type() )
		      ->add_class( 'form-control' );

		switch ( $this->get_type() ) {
			case 'checkbox':
				$input->set_attribute( 'value', $this->get_value() );
				$group->add_class( 'form-checkbox' );
				$group->append( $input );
				$group->append( $label );
				break;
			case 'textarea':
				$input->append( $this->get_value() )
				      ->set_tag( 'textarea' );
				$group->append( $label );
				$group->append( $input );
				break;
			case 'dropdown':
				$input->set_tag( 'select' );
				foreach ( $this->get_options() as $value => $option ) {
					$option_el = new Leira_Auth_Dom( 'option' );
					$option_el->set_attribute( 'value', $value )
					          ->append( $option );
					if ( $this->get_value() == $value ) {
						$option_el->set_attribute( 'selected', 'selected' );
					}
					$input->append( $option_el );
				}
				$group->append( $label );
				$group->append( $input );
				break;
			default;
				$input->set_attribute( 'value', $this->get_value() );
				$group->append( $label );
				$group->append( $input );
		}

		if ( ! empty( $this->get_description() ) ) {
			$description = new Leira_Auth_Dom( 'small' );
			$description->add_class( array(
				'form-text',
				'text-muted'
			) );
			$description->append( $this->get_description() );
			$group->append( $description );
		}

		return $group->__toString();
	}

	/**
	 * @return string
	 */
	public function render_input() {

		$group = new Leira_Auth_Html_Element();
		$group->add_class( 'form-group' );

		return '<input type="text" class="form-control" id="" aria-describedby="emailHelp">';
	}

}
