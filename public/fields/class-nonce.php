<?php

namespace Leira_Auth\Public\Fields;

use Leira_Auth\Public\Messages\Message;

/**
 * Nonce field backed by wp_nonce_field().
 *
 * @since 1.0.0
 */
class Nonce extends Hidden{

	/**
	 * Constructor.
	 *
	 * @param  string  $action
	 * @param  string  $name
	 */
	public function __construct( string $action = '_leira_auth_nonce', string $name = '_wpnonce' ) {
		parent::__construct( $name, [
			'action' => $action
		] );
	}

	/**
	 * Validate nonce token.
	 *
	 * @param  mixed  $value
	 *
	 * @return bool
	 */
	public function validate( mixed $value ): bool {
		$this->sanitize( $value );
		$this->clear_messages();
		$nonce = is_scalar( $value ) ? (string) $value : '';
		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, $this->get( 'action', '-1' ) ) ) {
			$form = $this->get_form();
			if ( $form ) {
				//If the field belongs to a form, add the error to the form
				$form->add_message( __( 'Invalid form submission.', 'leira-auth' ), Message::ERROR );
			} else {
				// If no form set, add the error to the field errors
				$this->add_message( __( 'Invalid form submission.', 'leira-auth' ), Message::ERROR );
			}

			return false;
		}

		$this->value = $value;

		return true;
	}

	/**
	 * Render nonce field using WordPress helper.
	 *
	 * @return string
	 */
	public function render(): string {
		return wp_nonce_field( $this->get( 'action', '-1' ), $this->get_name(), true, false );
	}
}
