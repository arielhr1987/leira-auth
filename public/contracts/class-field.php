<?php

namespace Leira_Auth\Public\Contracts;

use Leira_Auth\Public\Messages\Bag;

/**
 * Field interface
 *
 * Defines the contract for field objects in the authentication system.
 *
 * @package Leira_Auth\Public\Contracts
 * @since 1.0.0
 */
interface Field{

	/**
	 * Get the field name (HTML name / identifier)
	 *
	 * @return string The unique identifier for this field
	 */
	public function get_name(): string;

	/**
	 * Get the stored field value
	 *
	 * @return mixed The current value of the field
	 */
	public function get_value(): mixed;

	/**
	 * Set the field value
	 *
	 * @param  mixed  $value  The value to set
	 *
	 * @return self Returns the current instance for method chaining
	 */
	public function set_value( mixed $value ): self;

	/**
	 * Get the form this field belongs to
	 *
	 * @return Form The parent form instance
	 */
	public function get_form(): Form;

	/**
	 * Set the form this field belongs to
	 *
	 * @param  Form  $form  The parent form instance
	 *
	 * @return self Returns the current instance for method chaining
	 */
	public function set_form( Form $form ): self;

	/**
	 * Render the field HTML
	 *
	 * @return string The HTML representation of the field
	 */
	public function render(): string;
}
