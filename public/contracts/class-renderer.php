<?php

namespace Leira_Auth\Public\Contracts;

use Leira_Auth\Public\Forms\Form;

/**
 * Generic renderer contract.
 *
 * @since 1.0.0
 */
interface Renderer{

	/**
	 * Render a target object into HTML.
	 *
	 * @param  Form  $form
	 *
	 * @return string
	 */
	public function render( Form $form ): string;
}

