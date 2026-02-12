<?php

namespace Leira_Auth\Public\Crypto;

use Leira_Auth\Public\Contracts\Crypto;

/**
 * Base64 signed payload implementation.
 *
 * Signs data using HMAC-SHA256 and encodes the result as Base64.
 * The payload contains the original data and its MAC.
 *
 * @since 1.0.0
 */
class Base64 implements Crypto{

	/**
	 * Sign and encode data.
	 *
	 * @param  array  $data  Data to sign
	 *
	 * @return string Base64-encoded signed payload
	 */
	public function encrypt( array $data ): string {
		$json = wp_json_encode( $data );

		$payload = [
			'data' => $json,
			'mac'  => hash_hmac( 'sha256', $json, wp_salt( 'auth' ) ),
		];

		return base64_encode( wp_json_encode( $payload ) );
	}

	/**
	 * Verify and decode a signed payload.
	 *
	 * @param  string  $payload  Base64-encoded payload
	 *
	 * @return array Decoded data or empty array on failure
	 */
	public function decrypt( string $payload ): array {
		$decoded = base64_decode( $payload, true );
		if ( false === $decoded ) {
			return [];
		}

		$payload = json_decode( $decoded, true );
		if ( ! is_array( $payload ) || empty( $payload['data'] ) || empty( $payload['mac'] ) ) {
			return [];
		}

		$expected_mac = hash_hmac( 'sha256', $payload['data'], wp_salt( 'auth' ) );

		if ( ! hash_equals( $expected_mac, $payload['mac'] ) ) {
			return [];
		}

		return json_decode( $payload['data'], true ) ?? [];
	}
}
