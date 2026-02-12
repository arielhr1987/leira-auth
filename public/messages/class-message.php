<?php

namespace Leira_Auth\Public\Messages;

/**
 * A class that represents a message
 *
 * @since 1.0.0
 */
class Message{

	/**
	 * Constant to define the error message
	 */
	const ERROR = 'error';

	/**
	 * Constant to define the success message
	 */
	const SUCCESS = 'success';

	/**
	 * The message text
	 * @var string
	 */
	protected string $text = '';

	/**
	 * The message type
	 * @var string
	 */
	protected string $type = self::SUCCESS;

	/**
	 * Class constructor
	 *
	 * @param  string  $text
	 * @param  string  $type
	 */
	public function __construct( string $text, string $type = self::SUCCESS ) {
		$this->text = $text;
		$this->type = $type;
	}

	/**
	 * Get the message text
	 * @return string
	 */
	public function text(): string {
		return $this->text;
	}

	/**
	 * Get the message type
	 * @return string
	 */
	public function type(): string {
		return $this->type;
	}

	/**
	 * Determine if is a success message
	 * @return bool
	 */
	public function is_success(): bool {
		return $this->type === self::SUCCESS;
	}

	/**
	 * Determine if is an error message
	 * @return bool
	 */
	public function is_error(): bool {
		return $this->type === self::ERROR;
	}
}
