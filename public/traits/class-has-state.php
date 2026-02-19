<?php

namespace Leira_Auth\Public\Traits;

use Leira_Auth\Public\Messages\Message;

/**
 * Shared state export/restore logic for stateful fields.
 *
 * @since 1.0.0
 */
trait Has_State{

	/**
	 * Export current field state.
	 *
	 * @return array
	 */
	public function state(): array {
		return [
			'value'    => $this->get_value(),
			'messages' => $this->messages()->to_array(),
		];
	}

	/**
	 * Restore field state.
	 *
	 * @param  array  $state
	 *
	 * @return void
	 */
	public function restore( array $state ): void {
		if ( array_key_exists( 'value', $state ) ) {
			$this->set_value( $state['value'] );
		}

		$this->messages()->clear();

		$messages = $state['messages'] ?? [];
		if ( ! is_array( $messages ) ) {
			return;
		}

		foreach ( $messages as $message ) {
			if ( ! is_array( $message ) ) {
				continue;
			}

			$text = isset( $message['text'] ) ? (string) $message['text'] : '';
			if ( '' === $text ) {
				continue;
			}

			$type = isset( $message['type'] ) ? (string) $message['type'] : Message::ERROR;
			$this->messages()->add( $text, $type );
		}
	}
}
