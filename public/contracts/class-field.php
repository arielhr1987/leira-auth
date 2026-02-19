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
	 * Update field value.
	 *
	 * @param  mixed  $value
	 *
	 * @return self
	 */
	public function set_value( mixed $value ): self;

	/**
	 * Parent form.
	 *
	 * @return Form
	 */
	public function get_form(): Form;

	/**
	 * Attach form.
	 *
	 * @param  Form  $form
	 *
	 * @return self
	 */
	public function set_form( Form $form ): self;

}
