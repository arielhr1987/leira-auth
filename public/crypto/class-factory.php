<?php

namespace Leira_Auth\Public\Crypto;

use Leira_Auth\Public\Contracts\Crypto;

/**
 * Factory class to create a new crypto instance
 *
 * @since 1.0.0
 */
class Factory{

	/**
	 * Create the crypto instance required
	 * @return Crypto
	 */
	static public function create(): Crypto {
		// Optional: OpenSSL
		if ( extension_loaded( 'openssl' ) ) {
			return new Openssl();
		}

		// Final fallback: sign-only
		return new Base64();
	}
}
