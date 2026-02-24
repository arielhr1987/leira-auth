<?php

namespace Leira_Auth\Public\Fields;

use Leira_Auth\Public\Form_Node;
use Leira_Auth\Public\Constraints\Required;
use Leira_Auth\Public\Forms\Form;
use Leira_Auth\Public\Messages\Message;

/**
 * Base field implementation.
 *
 * @since 1.0.0
 */
abstract class Field extends Form_Node{

	/**
	 * Parent node.
	 *
	 * @var Form_Node|null
	 */
	protected ?Form_Node $parent = null;

	/**
	 * Render priority.
	 *
	 * @var int
	 */
	protected int $priority = 10;

	/**
	 * Determine if the field form was submitted.
	 *
	 * @var true
	 */
	protected bool $submitted = false;

	/**
	 * The raw data submitted
	 *
	 * @var mixed
	 */
	protected mixed $raw = null;

	/**
	 * The transformed data
	 *
	 * @var mixed
	 */
	protected mixed $value = null;

	/**
	 * Constructor.
	 *
	 * @param  string  $name
	 */
	public function __construct( string $name ) {
		parent::__construct( $name );
	}

	/**
	 * Get the parent of this field.
	 * It could be either a field, the form this field belongs to or null if not attached to any field form yet.
	 *
	 * @return Form_Node|null
	 */
	public function get_parent(): ?Form_Node {
		return $this->parent;
	}

	/**
	 * Get field value.
	 *
	 * @return mixed
	 */
	public function value(): mixed {
		return $this->value;
	}

	/**
	 * Return the raw value submitted
	 *
	 * @return mixed
	 */
	public function raw_value() {
		return $this->raw;
	}

	/**
	 * Get the form this field belongs to.
	 *
	 * @return Form|null
	 */
	public function get_form(): ?Form {
		if ( $this->parent === null ) {
			return null;
		}

		return $this->parent instanceof Form ? $this->parent : $this->get_parent()->get_form();
	}

	/**
	 * Get render priority.
	 *
	 * @return int
	 */
	public function priority(): int {
		return $this->priority;
	}

	/**
	 * Set render priority.
	 *
	 * @param  int  $priority
	 *
	 * @return self
	 */
	public function set_priority( int $priority ): self {
		$this->priority = $priority;

		return $this;
	}

	/**
	 * Submit the form
	 *
	 * @param  mixed  $value  The submitted value
	 *
	 * @return void
	 */
	public function submit( $value ): void {
		// set field as submitted
		$this->submitted = true;

		// store raw field value
		$this->raw = $value;

		// process the value
		foreach ( $this->sanitizers as $sanitizer ) {
			$value = $sanitizer->process( $value, $this );
		}

		// store processed value
		$this->value = $value;

		// validate the value
		$this->validate( $value );
	}

	/**
	 * Validate field value.
	 *
	 * @param  mixed  $value
	 *
	 * @return bool
	 */
	public function validate( mixed $value ): bool {
		$this->messages()->clear();

		$is_empty = null === $value || '' === $value || ( is_array( $value ) && empty( $value ) );
		if ( $is_empty && ! $this->is_required() ) {
			return true;
		}

		foreach ( $this->constraints() as $constraint ) {
			$error = $constraint->validate( $value, $this );
			if ( ! empty( $error ) ) {
				$this->messages()->add( new Message( $error ) );
			}
		}

		return ! $this->messages()->has();
	}

	/**
	 * Determine whether a field has a Required constraint.
	 *
	 * @return bool
	 */
	protected function is_required(): bool {
		//Todo: could be improved
		foreach ( $this->constraints() as $constraint ) {
			if ( $constraint instanceof Required ) {
				return true;
			}
		}

		return false;
	}

}
