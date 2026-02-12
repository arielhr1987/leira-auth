<?php

namespace Leira_Auth\Includes;

use Exception;
use WP_Error;

/**
 * Class Flash
 * Handles flash messages for users
 *
 * @package Includes
 * @since 1.0.0
 */
class Flash{

	/**
	 * Cookie name to use
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $cookie = 'leira_auth';

	/**
	 * All types of notifications allowed
	 *
	 * @since 1.0.0
	 * @var string[]
	 */
	protected $types = array( 'error', 'success', 'warning', 'info' );

	/**
	 * The WP error object containing the messages
	 *
	 * @since 1.0.0
	 * @var WP_Error|null
	 */
	protected $messages = null;

	/**
	 * The cipher method to use when encrypting
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $cipher = 'AES-256-CBC';

	/**
	 * The key used in encryption and decryption
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $key;

	/**
	 * Leira_Roles_Notifications constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		//Initialize the error variable
		$this->messages = new WP_Error();
		//initialize the encryption key
		$this->key = hash( 'sha256', wp_salt( 'auth' ), true );
		//Load error messages from cookie
		$this->load();

//		/**
//		 * Read cookie if exist
//		 */
//		if ( isset( $_COOKIE[ $this->cookie ] ) ) {
//			$messages = $_COOKIE[ $this->cookie ];
//			$messages = @json_decode( $messages, true );
//			if ( is_array( $messages ) ) {
//				$this->messages = $messages;
//			}
//
//			/**
//			 * Delete the cookie by setting an expiration time before the current time
//			 */
//			if ( ! headers_sent() ) {
//				@setcookie( $this->cookie, '', strtotime( '-1 month' ) );
//			}
//		}
	}

	/**
	 * Load the messages from the cookie
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function load() {
		if ( ! isset( $_COOKIE[ $this->cookie ] ) ) {
			return null;
		}

		$encrypted = $_COOKIE[ $this->cookie ];
		$error     = $this->decrypt( $encrypted );

		// Delete the cookie after reading
		@setcookie( $this->cookie, '', strtotime( '-1 month' ), COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
		unset( $_COOKIE[ $this->cookie ] );

		if ( $error instanceof WP_Error ) {
			$this->messages = $error;
		}
	}

	/**
	 * Display the messages
	 *
	 * @return string - The HTML code for displaying the messages
	 * @since 1.0.0
	 */
	public function display() {
		$html = '';
		foreach ( $this->types as $type ) {
			$messages = $this->get( $type );
			foreach ( $messages as $message ) {
				if ( is_string( $message ) ) {
					$html .= wp_kses_post( sprintf(
						'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
						$type,
						urldecode( $message )
					) );
				}
			}
		}

		return $html;
	}

	/**
	 * Get all messages for a given type
	 *
	 * @param  string  $type
	 *
	 * @return array The messages
	 * @since 1.0.0
	 */
	protected function get( $type ) {
		$messages = array();
		if ( isset( $this->messages[ $type ] ) && is_array( $this->messages[ $type ] ) ) {
			$messages = $this->messages[ $type ];
		}

		return $messages;
	}

	/**
	 * @param  string  $type  The type of notification to show to the user [error|success|warning|info]
	 * @param  string  $msg  The message to show to the user
	 *
	 * @return bool If notification was added successfully
	 * @since 1.0.0
	 */
	public function add( $type, $msg ) {
		if ( ! in_array( $type, $this->types ) || ! is_string( $msg ) ) {
			return false;
		}

		$messages   = $this->get( $type );
		$messages[] = $msg;

		// Update the messages
		$this->messages[ $type ] = $messages;

		if ( ! headers_sent() ) {
			/**
			 * Set the cookie to read in the next call
			 * Expiration time is set to a long number to avoid timezone differences
			 */
			@setcookie( $this->cookie, wp_json_encode( $this->messages ), strtotime( '+1 month' ) );
		}

		return true;
	}

	/**
	 * Show an error message
	 *
	 * @param  string  $msg
	 *
	 * @since 1.0.0
	 */
	public function error( $msg ) {
		$this->add( 'error', $msg );
	}

	/**
	 * Show a success message
	 *
	 * @param  string  $msg
	 *
	 * @since 1.0.0
	 */
	public function success( $msg ) {
		$this->add( 'success', $msg );
	}

	/**
	 * Show a warning message
	 *
	 * @param  string  $msg
	 *
	 * @since 1.0.0
	 */
	public function warning( $msg ) {
		$this->add( 'warning', $msg );
	}

	/**
	 * Show an info message
	 *
	 * @param  string  $msg
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function info( $msg ) {
		$this->add( 'info', $msg );
	}

	/**
	 * Encrypt a string.
	 *
	 * @param  WP_Error  $data  - The WP_Error to encrypt
	 *
	 * @return string
	 * @since 1.0.0
	 */
	protected function encrypt( $data ) {

		$serialized = maybe_serialize( $data );

		if ( function_exists( 'openssl_encrypt' ) ) {
			try{
				$iv         = random_bytes( openssl_cipher_iv_length( $this->cipher ) );
				$ciphertext = openssl_encrypt( $serialized, $this->cipher, $this->key, 0, $iv );
				$serialized = $iv . $ciphertext;
			}catch ( Exception $e ){
				//Do nothing, just continue...
			}
		}

		return base64_encode( $serialized );
	}

	/**
	 * Decrypt data.
	 *
	 * @param  string  $encoded  - The encrypted data
	 *
	 * @return WP_Error|null - The decrypted object
	 * @since 1.0.0
	 */
	protected function decrypt( $encoded ) {

		if ( ! function_exists( 'openssl_decrypt' ) ) {
			$decoded = base64_decode( $encoded, true );

			return $decoded ? maybe_unserialize( $decoded ) : null;
		}

		$data = base64_decode( $encoded, true );
		if ( $data === false ) {
			return null;
		}

		try{
			$iv_length = openssl_cipher_iv_length( $this->cipher );
			$iv        = substr( $data, 0, $iv_length );
			$cipher    = substr( $data, $iv_length );
			$decrypted = openssl_decrypt( $cipher, $this->cipher, $this->key, 0, $iv );
		}catch ( Exception $e ){
			return null;
		}

		if ( ! $decrypted ) {
			return null;
		}

		$unserialized = maybe_unserialize( $decrypted );

		return $unserialized instanceof WP_Error ? $unserialized : null;
	}
}
