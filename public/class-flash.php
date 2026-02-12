<?php

namespace Leira_Auth\Public;

use Leira_Auth\Public\Contracts\Crypto;

/**
 * A class to handle flash messages.
 * It relays on cookies to store and read the messages
 *
 * @since 1.0.0
 */
class Flash{

	/**
	 * @var string the cookie name to use
	 */
	protected const COOKIE_NAME = 'leira_auth_flash';

	/**
	 * @var array The data stored
	 */
	protected array $data = [];

	/**
	 * @var Crypto The crypto instance to use
	 */
	protected $crypto;

	/**
	 * @var int Cookie TTL in seconds
	 */
	protected $ttl = 300;

	/**
	 * @var bool Is loaded
	 */
	protected $loaded = false;

	/**
	 * Class constructor
	 *
	 * @param  Crypto  $crypto
	 */
	public function __construct( $crypto ) {
		$this->crypto = $crypto;
		$this->load();
	}

	/**
	 * Load flash data from the cookie and delete it immediately
	 */
	private function load(): void {

		if ( $this->loaded ) {
			return;
		}

		$this->loaded = true;

		if ( empty( $_COOKIE[ self::COOKIE_NAME ] ) ) {
			return;
		}

		$payload = $this->crypto->decrypt( $_COOKIE[ self::COOKIE_NAME ] );

		if ( is_array( $payload ) ) {
			$this->data = $payload;
		}

		// Delete cookie immediately (one-request lifetime)
		setcookie(
			self::COOKIE_NAME,
			'',
			time() - 3600,
			COOKIEPATH,
			COOKIE_DOMAIN,
			is_ssl(),
			true
		);
	}

	/**
	 * Add flash data (written for NEXT request)
	 *
	 * @param  string  $key  The key
	 * @param  mixed  $value  The value to store
	 *
	 * @return void
	 */
	public function add( string $key, mixed $value ): void {
		$this->data[ $key ] = $value;
		$this->persist();
	}

	/**
	 * Get an element from the flash data by key
	 *
	 * @param  string  $key  The Key to get
	 * @param  mixed|null  $default  The default value if doesnt exists
	 *
	 * @return mixed The data
	 */
	public function get( string $key, mixed $default = null ): mixed {
		return $this->data[ $key ] ?? $default;
	}

	/**
	 * Determine if a key exists in the flash
	 *
	 * @param  string  $key  The key to check
	 *
	 * @return bool
	 */
	public function has( string $key ): bool {
		return isset( $this->data[ $key ] );
	}

	/**
	 * Get all flash key stored
	 *
	 * @return array The flash data
	 */
	public function all(): array {
		return $this->data;
	}

	/**
	 * Persist flash data to cookie (only when adding).
	 * We should call this function before any content has been sent to the browser.
	 *
	 * @return void
	 */
	protected function persist(): void {
		$encrypted = $this->crypto->encrypt( $this->data );
		setcookie(
			self::COOKIE_NAME,
			$encrypted,
			time() + $this->ttl, // short-lived safety window
			COOKIEPATH,
			COOKIE_DOMAIN,
			is_ssl(),
			true
		);
	}
}
