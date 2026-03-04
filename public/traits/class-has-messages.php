<?php

namespace Leira_Auth\Public\Traits;

use InvalidArgumentException;
use Leira_Auth\Public\Messages\Message;

/**
 * Shared messages state/access.
 *
 * @since 1.0.0
 */
trait Has_Messages{

	/**
	 * Node messages.
	 *
	 * @var array<int, Message>
	 */
	protected array $messages = [];

	/**
	 * Get node messages.
	 *
	 * @return array<int, Message>
	 */
	public function messages(): array {
		return $this->messages;
	}

	/**
	 * Get node messages.
	 *
	 * @return array<int, Message>
	 */
	public function get_messages(): array {
		return $this->messages();
	}

	/**
	 * Set node messages.
	 *
	 * @param  array<int, Message|string>  $messages
	 *
	 * @return self
	 */
	public function set_messages( array $messages ): self {
		$this->messages = [];
		foreach ( $messages as $message ) {
			$this->add_message( $message );
		}

		return $this;
	}

	/**
	 * Add a message.
	 *
	 * @param  Message|string  $message
	 * @param  string  $type
	 *
	 * @return self
	 */
	public function add_message( Message|string $message, string $type = Message::ERROR ): self {
		if ( is_string( $message ) ) {
			$message = new Message( $message, $this->normalize_message_type( $type ) );
		}

		if ( ! $message instanceof Message ) {
			throw new InvalidArgumentException( 'Message must be a Message instance or string.' );
		}

		$this->messages[] = $message;

		return $this;
	}

	/**
	 * Add error message.
	 *
	 * @param  Message|string  $message
	 *
	 * @return self
	 */
	public function add_error_message( Message|string $message ): self {
		return $this->add_message( $message, Message::ERROR );
	}

	/**
	 * Add success message.
	 *
	 * @param  Message|string  $message
	 *
	 * @return self
	 */
	public function add_success_message( Message|string $message ): self {
		return $this->add_message( $message, Message::SUCCESS );
	}

	/**
	 * Clear node messages.
	 *
	 * @return self
	 */
	public function clear_messages(): self {
		$this->messages = [];

		return $this;
	}

	/**
	 * Determine whether node has messages.
	 *
	 * @return bool
	 */
	public function has_messages(): bool {
		return ! empty( $this->messages );
	}

	/**
	 * Export messages as serializable array.
	 *
	 * @return array<int, array{text: string, type: string}>
	 */
	public function messages_to_array(): array {
		$items = [];
		foreach ( $this->messages as $message ) {
			$items[] = [
				'text' => $message->text(),
				'type' => $message->type(),
			];
		}

		return $items;
	}

	/**
	 * Normalize a message type.
	 *
	 * @param  string  $type
	 *
	 * @return string
	 */
	protected function normalize_message_type( string $type ): string {
		$type = strtolower( trim( $type ) );

		return in_array( $type, [ Message::ERROR, Message::SUCCESS ], true ) ? $type : Message::ERROR;
	}
}
