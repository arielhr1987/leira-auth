<?php

namespace Leira_Auth\Public\Fields;

/**
 * Class that represents a text input
 * @since 1.0.0
 */
class Text extends Input{

	/**
	 * Default text input attributes
	 * @var array|string[]
	 */
	protected array $attributes = [
		'type' => 'text'
	];
}
