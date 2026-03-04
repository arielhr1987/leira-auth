<?php

namespace Leira_Auth\Public\Collections;

use Leira_Auth\Public\Messages\Message;
use InvalidArgumentException;

/**
 * Typed message collection with helper methods for message levels.
 *
 * @since 1.0.0
 */
class Messages extends Collection{

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( Message::class );
	}

	/**
	 * Add a message item.
	 *
	 * Accepts an existing Message object or raw text plus a message type.
	 *
	 * @param  mixed  $message
	 * @param  string  $type
	 *
	 * @return self
	 */
	public function add( mixed $message, string $type = Message::ERROR ): self {
		if ( is_string( $message ) ) {
			$message = new Message( $message, $this->normalize_type( $type ) );
		}

		if ( ! $message instanceof Message ) {
			throw new InvalidArgumentException( 'Message must be a Message instance or string.' );
		}

		parent::add( $message );

		return $this;
	}

	/**
	 * Add a success message.
	 *
	 * @param  mixed  $message
	 *
	 * @return self
	 */
	public function success( mixed $message ): self {
		return $this->add( $message, Message::SUCCESS );
	}

	/**
	 * Add an error message.
	 *
	 * @param  mixed  $message
	 *
	 * @return self
	 */
	public function error( mixed $message ): self {
		return $this->add( $message, Message::ERROR );
	}

	/**
	 * Normalize the message type.
	 *
	 * @param  string  $type
	 *
	 * @return string
	 */
	protected function normalize_type( string $type ): string {
		$type = strtolower( trim( $type ) );

		return in_array( $type, [ Message::SUCCESS, Message::ERROR ], true ) ? $type : Message::ERROR;
	}
}

