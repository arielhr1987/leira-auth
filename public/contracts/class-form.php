<?php

namespace Leira_Auth\Public\Contracts;

use Leira_Auth\Public\Messages\Message;

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
	public function get_name(): string;

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
	 * Render form HTML.
	 *
	 * @return string
	 */
	public function render(): string;

	/**
	 * Collected field values.
	 *
	 * @return array
	 */
	public function get_values(): array;

	/**
	 * Add a child field.
	 *
	 * @param  Field  $field
	 *
	 * @return self
	 */
	public function add_field( Field $field ): self;

	/**
	 * Set an option by key.
	 *
	 * @param  string  $key
	 * @param  mixed  $value
	 *
	 * @return self
	 */
	public function set( string $key, mixed $value ): self;

	/**
	 * Get an option by key.
	 *
	 * @param  string  $key
	 * @param  mixed  $default
	 *
	 * @return mixed
	 */
	public function get( string $key, mixed $default = null ): mixed;

	/**
	 * Get options.
	 *
	 * @return array<string, mixed>
	 */
	public function get_options(): array;

	/**
	 * Get child field by key.
	 *
	 * @param  string  $name
	 *
	 * @return Field|null
	 */
	public function get_field( string $name ): ?Field;

	/**
	 * Get child fields.
	 *
	 * @return array<string, Field>
	 */
	public function all_fields(): array;

	/**
	 * Remove one child field by key.
	 *
	 * @param  string  $name
	 *
	 * @return self
	 */
	public function remove_field( string $name ): self;

	/**
	 * Remove all child fields.
	 *
	 * @return self
	 */
	public function clear_fields(): self;

	/**
	 * Validate submitted payload.
	 *
	 * @param  mixed  $data
	 *
	 * @return bool
	 */
	public function validate( mixed $data ): bool;

	/**
	 * Get form-level messages.
	 *
	 * @return array<int, Message>
	 */
	public function messages(): array;

	/**
	 * Add a form-level message.
	 *
	 * @param  Message|string  $message
	 * @param  string  $type
	 *
	 * @return self
	 */
	public function add_message( Message|string $message, string $type = Message::ERROR ): self;

	/**
	 * Remove all messages.
	 *
	 * @return self
	 */
	public function clear_messages(): self;

	/**
	 * Determine if the form has messages.
	 *
	 * @return bool
	 */
	public function has_messages(): bool;
}
