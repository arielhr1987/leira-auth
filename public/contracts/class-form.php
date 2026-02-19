<?php

namespace Leira_Auth\Public\Contracts;

use Leira_Auth\Public\Collections\Messages;
use Leira_Auth\Public\Fields\Field as Field_Node;

/**
 * Form contract.
 *
 * @since 1.0.0
 */
interface Form{

	/**
	 * Form identifier.
	 *
	 * @return string
	 */
	public function name(): string;

	/**
	 * Build form from options.
	 *
	 * @param  array  $options
	 *
	 * @return void
	 */
	public function build( array $options ): void;

	/**
	 * Handle current request.
	 *
	 * @return bool
	 */
	public function handle(): bool;

	/**
	 * Collected data.
	 *
	 * @return array
	 */
	public function data(): array;

	/**
	 * Add a child field.
	 *
	 * @param  Field_Node  $field
	 * @param  string|null  $key
	 *
	 * @return self
	 */
	public function add( Field_Node $field, ?string $key = null ): self;

	/**
	 * Set a child field by key.
	 *
	 * @param  string  $name
	 * @param  Field_Node  $field
	 *
	 * @return self
	 */
	public function set( string $name, Field_Node $field ): self;

	/**
	 * Get child field by key.
	 *
	 * @param  string  $name
	 *
	 * @return Field_Node|null
	 */
	public function get( string $name ): ?Field_Node;

	/**
	 * Get an ordered child field list.
	 *
	 * @return array<int, Field_Node>
	 */
	public function all(): array;

	/**
	 * Remove one child field by key.
	 *
	 * @param  string  $name
	 *
	 * @return self
	 */
	public function remove( string $name ): self;

	/**
	 * Remove all child fields.
	 *
	 * @return self
	 */
	public function clear(): self;

	/**
	 * Get form-level messages.
	 *
	 * @return Messages
	 */
	public function messages(): Messages;
}
