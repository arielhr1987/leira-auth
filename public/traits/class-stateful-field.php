<?php

namespace Leira_Auth\Public\Traits;

/**
 * Provides state persistence for form fields.
 *
 * This trait allows a field to export its current state (value and messages)
 * and restore a previous state, typically after a POST-redirect-GET cycle.
 *
 * The consuming field is expected to implement compatible methods such as
 * `value()`, `fill()`, and `messages()` when applicable.
 *
 * @since 1.0.0
 */
trait Stateful_Field{

	/**
	 * Export the current field state.
	 *
	 * Returns an array containing the field value and validation messages, if supported by the consuming field.
	 *
	 * @return array<string, mixed> Field state data
	 */
	public function state(): array {
		return [
			'value'    => method_exists( $this, 'value' ) ? $this->value() : null,
			'messages' => method_exists( $this, 'messages' ) ? $this->messages() : [],
		];
	}

	/**
	 * Restore a previously exported field state.
	 *
	 * Restores the field value and validation messages when supported.
	 *
	 * @param  array<string, mixed>  $state  Previously stored field state
	 *
	 * @return void
	 */
	public function restore( array $state ): void {
		if ( isset( $state['value'] ) && method_exists( $this, 'fill' ) ) {
			$this->fill( $state['value'] );
		}

		if ( isset( $state['messages'] ) && property_exists( $this, 'messages' ) ) {
			$this->messages = $state['messages'];
		}
	}
}
