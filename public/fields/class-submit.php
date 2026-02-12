<?php

namespace Leira_Auth\Public\Fields;

/**
 * A submit input element.
 *
 * @since 1.0.0
 */
class Submit extends Text{
	/**
	 * Default text input attributes
	 * @var array|string[]
	 */
	protected array $attributes = [
		'type' => 'submit'
	];
}
