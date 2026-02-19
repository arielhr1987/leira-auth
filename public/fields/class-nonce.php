<?php

namespace Leira_Auth\Public\Fields;

use Leira_Auth\Public\Contracts\Form as FormContract;
use Leira_Auth\Public\Messages\Message;

/**
 * Nonce field backed by wp_nonce_field().
 *
 * @since 1.0.0
 */
class Nonce extends Hidden{

	/**
	 * Nonce action.
	 *
	 * @var string
	 */
	protected string $nonce_action = '';

	/**
	 * Constructor.
	 *
	 * @param  string  $name
	 * @param  string  $nonce_action
	 */
	public function __construct( string $name = '_leira_auth_nonce', string $nonce_action = '' ) {
		parent::__construct( $name );
		$this->nonce_action = $nonce_action;
	}

	/**
	 * Set nonce action.
	 *
	 * @param  string  $nonce_action
	 *
	 * @return self
	 */
	public function set_nonce_action( string $nonce_action ): self {
		$this->nonce_action = $nonce_action;

		return $this;
	}

	/**
	 * Get nonce action.
	 *
	 * @return string
	 */
	public function get_nonce_action(): string {
		return $this->nonce_action;
	}

	/**
	 * Resolve nonce action with sane fallbacks.
	 *
	 * @return string
	 */
	public function resolved_nonce_action(): string {
		$action = trim( $this->nonce_action );
		if ( '' !== $action ) {
			return $action;
		}

		$form = $this->resolve_form();
		if ( $form instanceof FormContract ) {
			return $form->name();
		}

		return $this->get_name();
	}

	/**
	 * Validate nonce token.
	 *
	 * @param  mixed  $value
	 *
	 * @return bool
	 */
	public function validate( mixed $value ): bool {
		$this->messages()->clear();
		$this->set_value( $value );

		$nonce = is_scalar( $value ) ? (string) $value : '';
		if ( '' === $nonce || ! wp_verify_nonce( $nonce, $this->resolved_nonce_action() ) ) {
			$this->messages()->add( __( 'Invalid form submission.', 'leira-auth' ), Message::ERROR );

			return false;
		}

		return true;
	}
}
