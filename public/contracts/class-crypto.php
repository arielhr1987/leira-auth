<?php

namespace Leira_Auth\Public\Contracts;

/**
 * Crypto interface
 *
 * Defines the contract for encryption and decryption operations in the authentication system.
 *
 * @package Leira_Auth\Public\Contracts
 * @since 1.0.0
 */
interface Crypto {

	/**
	 * Encrypt and authenticate payload
	 *
	 * Securely encrypts the provided data array and adds authentication to ensure data integrity and authenticity.
	 *
	 * @param array $data The data to encrypt
	 *
	 * @return string The encrypted and authenticated data as a string
	 */
	public function encrypt( array $data ): string;

	/**
	 * Decrypt payload
	 *
	 * Decrypts and verifies the authenticity of the encrypted payload.
	 * If the payload is invalid or tampered with, implementations should handle this appropriately.
	 *
	 * @param string $payload The encrypted data to decrypt
	 *
	 * @return array The decrypted data as an array
	 */
	public function decrypt( string $payload ): array;
}
