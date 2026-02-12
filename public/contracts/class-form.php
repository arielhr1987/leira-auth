<?php

namespace Leira_Auth\Public\Contracts;

/**
 * Form interface
 *
 * Defines the contract for form objects in the authentication system.
 * Handles the form rendering, validation, field management, and error handling.
 *
 * @package Leira_Auth\Public\Contracts
 * @since 1.0.0
 */
interface Form extends Validatable, Stateful{

	/**
	 * Get the form name/identifier.
	 *
	 * @return string
	 */
	public function name(): string;

	/**
	 * Build form fields and defaults from options.
	 *
	 * @param  array  $options  Form options
	 *
	 * @return void
	 */
	public function build( array $options ): void;

	/**
	 * Handle the HTTP request
	 *
	 * Processes the incoming HTTP request data, populates form fields, and validates the form.
	 *
	 * @return bool True if the form was submitted and valid, false otherwise
	 */
	public function handle(): bool;

	/**
	 * Render the full form HTML
	 *
	 * Generates the complete HTML representation of the form including all fields, error messages, and form elements.
	 *
	 * @return string The HTML representation of the form
	 */
	public function render(): string;

	/**
	 * Get all submitted form data
	 *
	 * Retrieves all data submitted with the form as an associative array.
	 *
	 * @return array The submitted form data as field_name => value pairs
	 */
	public function data(): array;

	/**
	 * Add a field to the form
	 *
	 * Registers a field with the form and establishes the parent-child relationship between the form and the field.
	 *
	 * @param  Field  $field  The field to add to the form
	 *
	 * @return self Returns the current instance for method chaining
	 */
	public function add_field( Field $field ): self;

	/**
	 * Get a field by name
	 *
	 * Retrieves a specific field from the form by its unique name.
	 *
	 * @param  string  $name  The name of the field to get from the form
	 *
	 * @return ?Field The field object if found, null otherwise
	 */
	public function get_field( string $name ): ?Field;

	/**
	 * Get all the form fields
	 *
	 * Retrieves all fields registered with this form.
	 *
	 * @return array The array of form fields
	 */
	public function fields(): array;
}
