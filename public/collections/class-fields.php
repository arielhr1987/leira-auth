<?php

namespace Leira_Auth\Public\Collections;

use Leira_Auth\Public\Fields\Field as FieldContract;

/**
 * Typed collection of form fields.
 *
 * @since 1.0.0
 */
class Fields extends Collection{

	/**
	 * Constructor.
	 *
	 * @param  array<int|string, FieldContract>  $items
	 */
	public function __construct( array $items = [] ) {
		parent::__construct( FieldContract::class );

		foreach ( $items as $key => $field ) {
			if ( is_int( $key ) ) {
				$this->add( $field );
				continue;
			}

			$this->set( (string) $key, $field );
		}
	}

	/**
	 * Add/replace a field by its identifier.
	 *
	 * @param  FieldContract  $field
	 *
	 * @return self
	 */
	public function add( mixed $field ): self {
		$this->assert_type( $field );

		/** @var FieldContract $field */
		$this->set( $field->get_name(), $field );

		return $this;
	}

	/**
	 * Get a list of fields sorted by render priority.
	 *
	 * @return array<int, FieldContract>
	 */
	public function ordered(): array {
		$fields = array_values( $this->to_array() );

		usort(
			$fields,
			static function ( FieldContract $a, FieldContract $b ): int {
				$a_priority = method_exists( $a, 'get_priority' ) ? (int) $a->get_priority() : 10;
				$b_priority = method_exists( $b, 'get_priority' ) ? (int) $b->get_priority() : 10;

				return $a_priority <=> $b_priority;
			}
		);

		return $fields;
	}
}
