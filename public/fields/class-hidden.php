<?php

namespace Leira_Auth\Public\Fields;

use Leira_Auth\Public\Contracts\Attributable;
use Leira_Auth\Public\Contracts\Stateful;
use Leira_Auth\Public\Contracts\Validatable;
use Leira_Auth\Public\Traits\Attributable_Field;
use Leira_Auth\Public\Traits\Validatable_Field;
use Leira_Auth\Public\Traits\Stateful_Field;

/**
 * A checkbox input element.
 *
 * @since 1.0.0
 */
class Hidden implements Attributable, Validatable, Stateful{

	use Attributable_Field;
	use Validatable_Field;
	use Stateful_Field;

	/**
	 * Default input attributes
	 * @var array|string[]
	 */
	protected array $attributes = [
		'type' => 'hidden',
	];

	/**
	 * Render the input hidden field
	 *
	 * @return string The filed HTML
	 */
	public function render(): string {
		$defaults   = [
			'id'    => $this->get_attribute( 'id' ) ?? $this->get_name(),
			'name'  => $this->get_name(),
			'value' => $this->get_attribute( 'value' ) ?? ""
		];
		$attributes = array_merge( $defaults, $this->attributes );

		return $this->render_tag( 'input', $attributes );
	}
}
