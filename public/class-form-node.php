<?php

namespace Leira_Auth\Public;

use Leira_Auth\Public\Collections\Collection;
use Leira_Auth\Public\Collections\Fields;
use Leira_Auth\Public\Contracts\Constraint;
use Leira_Auth\Public\Contracts\Sanitizer;
use Leira_Auth\Public\Fields\Field;
use Leira_Auth\Public\Messages\Message;

/**
 * Shared node state for forms and fields.
 *
 * @since 1.0.0
 */
abstract class Form_Node{

	/**
	 * Node identifier.
	 *
	 * @var string
	 */
	protected string $name;

	/**
	 * Node messages.
	 *
	 * @var Collection
	 */
	protected Collection $messages;

	/**
	 * Node constraints.
	 *
	 * @var Collection
	 */
	protected Collection $constraints;

	/**
	 * Node normalizers.
	 *
	 * @var Collection
	 */
	protected Collection $sanitizers;

	/**
	 * Node options.
	 *
	 * @var Collection
	 */
	protected Collection $options;

	/**
	 * Child nodes.
	 *
	 * @var Fields
	 */
	protected Fields $fields;

	/**
	 * Constructor.
	 *
	 * @param  string  $name
	 */
	public function __construct( string $name ) {
		$this->name        = $name;
		$this->fields      = new Fields();
		$this->sanitizers  = new Collection( Sanitizer::class );
		$this->constraints = new Collection( Constraint::class );
		$this->messages    = new Collection( Message::class );
		$this->options     = new Collection();
	}

	/**
	 * Get node name.
	 *
	 * @return string
	 */
	public function name(): string {
		return $this->name;
	}

	/**
	 * Get node messages.
	 *
	 * @return Collection
	 */
	public function messages(): Collection {
		return $this->messages;
	}

	/**
	 * Get node constraints.
	 *
	 * @return Collection
	 */
	public function constraints(): Collection {
		return $this->constraints;
	}

	/**
	 * Get the list of processors.
	 *
	 * @return Collection
	 */
	public function sanitizers(): Collection {
		return $this->sanitizers;
	}

	/**
	 * Get node options collection.
	 *
	 * @return Collection
	 */
	public function options(): Collection {
		return $this->options;
	}

	/**
	 * Apply node normalizers to a value.
	 *
	 * @param  mixed  $value
	 *
	 * @return mixed
	 */
	protected function sanitize( mixed $value ): mixed {
		foreach ( $this->sanitizers as $sanitizer ) {
			$value = $sanitizer( $value, $this );
		}

		return $value;
	}

	/**
	 * Validate a field against a value
	 *
	 * @param  mixed  $value  The value to validate
	 *
	 * @return bool
	 */
	protected function validate( mixed $value ): bool {
		$valid = true;
		foreach ( $this->constraints() as $constraint ) {
			$error = $constraint->validate( $value, $this );
			if ( $error ) {
				$valid = false;
				$this->messages->add( $error );
			}
		}

		return $valid;
	}

	/**
	 * Get one child by key.
	 *
	 * @param  string  $key
	 *
	 * @return Field|null
	 */
	public function get( string $key ): ?Field {
		return $this->fields->get( $key );
	}

	/**
	 * Add a field.
	 *
	 * @param  Field  $child
	 *
	 * @return self
	 */
	public function add( Field $child ): self {
		if ( $child === $this ) {
			return $this;
		}

		$key = $child->name();

		$this->fields->add( $child );

		//$child->set_parent( $this );

		return $this;
	}

	/**
	 * Set child by key.
	 *
	 * @param  Field  $child
	 *
	 * @return self
	 */
	public function set( Field $child ): self {
		$this->fields->set( $child->name(), $child );

		return $this;
	}

	/**
	 * Check whether a child key exists.
	 *
	 * @param  string|null  $key
	 *
	 * @return bool
	 */
	public function has( ?string $key = null ): bool {
		return $this->fields->has( $key );
	}

	/**
	 * Remove one child by key or instance.
	 *
	 * @param  string|Field  $child
	 *
	 * @return self
	 */
	public function remove( string|Field $child ): self {
		$key = is_string( $child ) ? $child : $child->name();
		$this->fields->remove( $key );

		return $this;
	}

	/**
	 * Remove all children.
	 *
	 * @return self
	 */
	public function clear(): self {
		$this->fields->clear();

		return $this;
	}

	/**
	 * Get all children.
	 *
	 * @return array<string, Field>
	 */
	public function all(): array {
		return $this->fields->all();
	}
}
