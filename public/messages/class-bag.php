<?php

namespace Leira_Auth\Public\Messages;

/**
 * Messages Collection Manager
 *
 * A class for managing a collection of messages.
 * Provides functionality for adding, retrieving, clearing, and converting messages
 * to/from array format. Supports both single messages and batches of messages.
 *
 * @package Leira_Auth\Public\Messages
 * @since 1.0.0
 */
class Bag{

	/**
	 * Stored messages
	 *
	 * @var list<string>
	 */
	protected array $messages = [];

	/**
	 * Add a message or multiple messages to the collection
	 *
	 * @param  string|string[]|Message  $message  A single message or an array of messages
	 *
	 * @return self Returns the current instance for method chaining
	 */
	public function add( $message, $type = Message::ERROR ): self {
		if ( is_array( $message ) ) {
			foreach ( $message as $msg ) {
				$this->add( $msg, $type );
			}

			return $this;
		}

		// Handle a single message
		if ( is_string( $message ) ) {
			$this->add( new Message( (string) $message, $type ) );
		}

		// Add the message
		if ( $message instanceof Message ) {
			$this->messages[] = $message;
		}

		return $this;
	}

	/**
	 * Check if there are any messages in the collection
	 *
	 * @return bool Returns true if there are messages, false otherwise
	 */
	public function has(): bool {
		return ! empty( $this->messages );
	}

	/**
	 * Get all messages from the collection
	 *
	 * Returns the complete list of messages
	 *
	 * @return list<Message> Array of messages
	 */
	public function all(): array {
		return $this->messages;
	}

	/**
	 * Clear all messages from the collection
	 *
	 * Removes all messages, resetting the collection to an empty state
	 *
	 * @return self Returns the current instance for method chaining
	 */
	public function clear(): self {
		$this->messages = [];

		return $this;
	}

	/**
	 * Restore messages from an array format
	 *
	 * Populates the instance with the provided message data
	 *
	 * @param  array<int, string|array{message?: string}|Message>  $data  Array of message data to restore
	 *
	 * @return self New Messages instance containing the restored messages
	 */
	public function from_array( array $data ): self {
		$this->add( $data );

		return $this;
	}

	/**
	 * Convert the bag to an array representation
	 *
	 * @return array
	 */
	public function to_array(): array {
		return array_map( function ( Message $message ) {
			return [
				'text' => $message->text(),
				'type' => $message->type(),
			];
		}, $this->all() );
	}
}
