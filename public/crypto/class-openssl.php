<?php

namespace Leira_Auth\Public\Crypto;

use Leira_Auth\Public\Contracts\Crypto;

/**
 * Openssl crypto implementation
 *
 * @since 1.0.0
 */
class Openssl implements Crypto{

	/**
	 * @var string The encryption key
	 */
	protected string $key;

	/**
	 * @var string The cipher method
	 */
	protected string $cipher = 'aes-256-gcm';

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->key = hash( 'sha256', AUTH_KEY . SECURE_AUTH_KEY . LOGGED_IN_KEY, true );
	}

	/**
	 * Encrypt method
	 *
	 * @param  array  $data  The data to encrypt
	 *
	 * @return string
	 */
	public function encrypt( array $data ): string {

		try{
			$iv  = random_bytes( 12 );
			$tag = '';
		}catch ( \Exception $e ){
			return '';
		}

		$ciphertext = openssl_encrypt(
			wp_json_encode( $data ),
			$this->cipher,
			$this->key,
			OPENSSL_RAW_DATA,
			$iv,
			$tag
		);

		if ( false === $ciphertext ) {
			return '';
		}

		return base64_encode( $iv . $tag . $ciphertext );
	}

	/**
	 * Decrypt the message
	 *
	 * @param  string  $payload  The payload to decrypt
	 *
	 * @return array The decrypted data
	 */
	public function decrypt( string $payload ): array {

		$raw = base64_decode( $payload, true );

		if ( false === $raw || strlen( $raw ) < 28 ) {
			return [];
		}

		$iv   = substr( $raw, 0, 12 );
		$tag  = substr( $raw, 12, 16 );
		$data = substr( $raw, 28 );

		$json = openssl_decrypt(
			$data,
			$this->cipher,
			$this->key,
			OPENSSL_RAW_DATA,
			$iv,
			$tag
		);

		if ( false === $json ) {
			return [];
		}

		return json_decode( $json, true );
	}
}
