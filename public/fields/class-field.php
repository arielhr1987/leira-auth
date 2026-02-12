<?php

namespace Leira_Auth\Public\Fields;

use Leira_Auth\Public\Contracts\Field as Field_Interface;
use Leira_Auth\Public\Contracts\Constraint;
use Leira_Auth\Public\Contracts\Form;
use Leira_Auth\Public\Constraints\Required;
use Leira_Auth\Public\Contracts\Stateful;
use Leira_Auth\Public\Messages\Bag;

/**
 * The base implementation for the fields in a form
 *
 * @since 1.0.0
 */
abstract class Field implements Field_Interface, Stateful{

	/**
	 * The error detected when validating this field
	 *
	 * @var Bag
	 */
	protected Bag $messages;

	/**
	 * The validators used to validate this field
	 *
	 * @var Constraint[]
	 */
	protected array $constraints = [];

	/**
	 * The form this field belongs to
	 *
	 * @var Form|null
	 */
	protected ?Form $form = null;

	/**
	 * Class constructor
	 *
	 * @param  string  $name  The field name.
	 */
	public function __construct( string $name ) {
		$this->messages = new Bag();
	}

	/**
	 * Get the field name
	 * @return string
	 */
	abstract public function get_name(): string;

	/**
	 * Get the submitted value of the field
	 *
	 * @return mixed
	 */
	abstract public function get_value(): mixed;

	/**
	 * Set the field value
	 *
	 * @param  mixed  $value  The value to set
	 *
	 * @return self Returns the current instance for method chaining
	 */
	abstract public function set_value( mixed $value ): self;

	/**
	 * The default method to render the field
	 *
	 * @return string
	 */
	abstract public function render(): string;

	/**
	 * Add a validator to the list of the field validations.
	 *
	 * @param  Constraint  $constraint
	 *
	 * @return $this
	 */
	public function constraint( Constraint $constraint ): self {
		$this->constraints[] = $constraint;

		return $this;
	}

	/**
	 * Get the errors from the validation process
	 *
	 * @return Bag
	 */
	public function messages(): Bag {
		return $this->messages;
	}

	/**
	 * Get the form this field belongs to
	 *
	 * @return Form The parent form instance
	 */
	public function get_form(): Form {
		return $this->form;
	}

	/**
	 * Set the form this field belongs to
	 *
	 * @param  Form  $form  The parent form instance
	 *
	 * @return self Returns the current instance for method chaining
	 */
	public function set_form( Form $form ): self {
		$this->form = $form;

		return $this;
	}

	/**
	 * Validate the field
	 *
	 * @param $value
	 *
	 * @return bool
	 */
	public function validate( $value ): bool {
		$this->messages = new Bag();
		$this->set_value( $value );

		$isEmpty = $value === null || $value === '' || ( is_array( $value ) && empty( $value ) );

		// Optional field short-circuit
		if ( $isEmpty && ! $this->is_required() ) {
			return true;
		}

		foreach ( $this->constraints as $constraint ) {
			$error = $constraint->validate( $value, $this );

			if ( ! empty( $error ) ) {
				$this->messages()->add( $error );
			}
		}

		return ! $this->messages()->has();
	}

	/**
	 * Determine if the field is required.
	 * We check if a "Required" constraint exists for this field.
	 *
	 * @return bool
	 */
	protected function is_required(): bool {
		foreach ( $this->constraints as $validator ) {
			if ( $validator instanceof Required ) {
				return true;
			}
		}

		return false;
	}
}
