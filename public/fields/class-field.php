<?php

namespace Leira_Auth\Public\Fields;

use Leira_Auth\Public\Constraints\Required;
use Leira_Auth\Public\Contracts\Field as Field_Contract;
use Leira_Auth\Public\Contracts\Form as Form_Contract;
use Leira_Auth\Public\Html;
use Leira_Auth\Public\Traits\Has_Constraints;
use Leira_Auth\Public\Traits\Has_Messages;
use Leira_Auth\Public\Traits\Has_Options;

/**
 * Base field implementation.
 *
 * @since 1.0.0
 */
abstract class Field implements Field_Contract{

	use Has_Messages;
	use Has_Options;
	use Has_Constraints;

	/**
	 * Field identifier.
	 *
	 * @var string
	 */
	protected string $name;

	/**
	 * Parent form.
	 *
	 * @var Form_Contract|null
	 */
	protected ?Form_Contract $form = null;

	/**
	 * The transformed data.
	 *
	 * @var mixed
	 */
	protected mixed $value = null;

	/**
	 * Constructor.
	 *
	 * @param  string  $name  The field name
	 * @param  array  $options  The field options
	 */
	public function __construct( string $name, array $options = [] ) {
		$this->name    = $name;
		$this->options = $options;
	}

	/**
	 * Field name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Get field value.
	 *
	 * @return mixed
	 */
	public function get_value(): mixed {
		return $this->value;
	}

	/**
	 * Set the form this field belongs to.
	 *
	 * @param  Form_Contract  $form  The form instance
	 *
	 * @return self
	 */
	public function set_form( Form_Contract $form ): self {
		$this->form = $form;

		return $this;
	}

	/**
	 * Get the form this field belongs to.
	 *
	 * @return Form_Contract|null
	 */
	public function get_form(): ?Form_Contract {
		return $this->form;
	}

	/**
	 * Return the raw value submitted before sanitization.
	 *
	 * @return mixed
	 */
	public function get_raw_value(): mixed {
		return $this->get( 'raw_value' );
	}

	/**
	 * Get render priority.
	 *
	 * @return int
	 */
	public function get_priority(): int {
		return $this->get( 'priority' );
	}

	/**
	 * Set render priority.
	 *
	 * @param  int  $priority
	 *
	 * @return self
	 */
	public function set_priority( int $priority = 10 ): self {
		$this->set( 'priority', $priority );

		return $this;
	}

	/**
	 * Sanitize incoming field value using the configured callback.
	 *
	 * @param  mixed  $value
	 *
	 * @return mixed
	 */
	protected function sanitize( mixed $value ): mixed {
		$callback = $this->get( 'sanitizer_callback', 'sanitize_text_field' );

		if ( is_callable( $callback ) ) {
			try{
				return call_user_func( $callback, $value, $this );
			}catch ( \ArgumentCountError ){
				return call_user_func( $callback, $value );
			}
		}

		if ( is_scalar( $value ) && function_exists( 'sanitize_text_field' ) ) {
			return sanitize_text_field( (string) $value );
		}

		return $value;
	}

	/**
	 * Validate field value.
	 *
	 * @param  mixed  $value
	 *
	 * @return bool
	 */
	public function validate( mixed $value ): bool {
		$this->clear_messages();
		$this->sanitize( $value );
		$is_empty = null === $value || '' === $value || ( is_array( $value ) && empty( $value ) );
		$is_valid = true;
		foreach ( $this->get_constraints() as $constraint ) {
			// Run Required only for empty values; skip other constraints when empty.
			if ( $is_empty && ! $constraint instanceof Required ) {
				continue;
			}
			//Validate against constraint
			if ( ! $constraint->validate( $value, $this ) ) {
				//Add error
				$is_valid = false;
				$this->add_error_message( $constraint->get_message() );
			}
		}

		if ( $is_valid ) {
			$this->value = $value;
		}

		return $is_valid;
	}

	/**
	 * Render this field.
	 *
	 * @return string
	 */
	public function render(): string {
		$help  = trim( $this->get( 'help', '' ) );
		$class = Html::merge_classes( $this->get( 'class', '' ), $this->has_messages() ? 'leira-auth-is-invalid' : '' );

		$attributes = [
			'type'             => $this->get( 'type', 'text' ),
			'name'             => $this->get_name(),
			'id'               => $this->get( 'id', $this->get_name() ),
			'class'            => $class,
			'placeholder'      => $this->get( 'placeholder', '' ),
			'autocomplete'     => $this->get( 'autocomplete', '' ),
			'required'         => $this->get( 'required', '' ),
			'value'            => $this->get( 'default' ),
			'aria-describedby' => ! empty( $help ) ? $this->get_name() . '-help' : null,
		];

		$attributes = apply_filters( 'leira_auth_input_attributes', array_filter( $attributes ), $this );

		$html = Html::el( 'input', $attributes );

		return (string) apply_filters( 'leira_auth_render_input', $html, $this );
	}
}
