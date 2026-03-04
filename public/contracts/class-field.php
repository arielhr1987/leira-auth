<?php

namespace Leira_Auth\Public\Contracts;

/**
 * Form Field contract.
 *
 * @since 1.0.0
 */
interface Field{

	/**
	 * Field identifier.
	 *
	 * @return string
	 */
	public function get_name(): string;

	/**
	 * Current field value.
	 *
	 * @return mixed
	 */
	public function get_value(): mixed;

	/**
	 * Parent form.
	 *
	 * @return Form|null
	 */
	public function get_form(): ?Form;

	/**
	 * Attach form.
	 *
	 * @param  Form  $form
	 *
	 * @return self
	 */
	public function set_form( Form $form ): self;

	/**
	 * Validate field value.
	 *
	 * @param  mixed  $value
	 *
	 * @return bool
	 */
	public function validate( mixed $value ): bool;

	/**
	 * Render field HTML.
	 *
	 * @return string
	 */
	public function render(): string;

}
