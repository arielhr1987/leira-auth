<?php

namespace Leira_Auth\Public;

use WP_User;

/**
 * Controller to override WordPress authentication URLs.
 *
 * This controller hooks into core WordPress URL filters and replaces
 * login, registration, password reset, lost password, and logout URLs
 * with custom frontend pages defined in plugin settings.
 *
 * Features:
 * - Does not override wp-admin or wp-login.php internally
 * - Preserves redirect_to parameters
 * - Keeps logout nonce security
 * - Falls back to default WordPress URLs if pages are missing
 */
class Url_Controller{
	/**
	 * Mapping of auth action => page ID loaded from options.
	 *
	 * @var array<string,int>
	 */
	protected array $pages = [];

	/**
	 * Registers all WordPress filters required to override auth URLs.
	 *
	 * Should be called once during plugin bootstrap.
	 *
	 * @return void
	 */
	public function init(): void {}

	/**
	 * Determines whether the current request is a frontend context.
	 *
	 * Prevents URL overrides inside wp-admin or wp-login.php
	 * to avoid breaking native authentication flows.
	 *
	 * @return bool True when running on frontend.
	 */
	protected function is_frontend(): bool {
		// Never override inside wp-login.php or admin
		if ( is_admin() ) {
			return false;
		}

		global $pagenow;

		if ( $pagenow === 'wp-login.php' ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns the permalink for a configured auth page.
	 *
	 * @param  string  $key  Auth action key (login, register, lost_password, etc.)
	 *
	 * @return string|null Page URL or null if not configured.
	 */
	private function get_page_url( string $key ): ?string {

		if ( empty( $this->pages ) ) {
			$this->pages = (array) get_option( 'leira_auth_pages', [] );
		}

		if ( empty( $this->pages[ $key ] ) ) {
			return null;
		}

		$url = get_permalink( (int) $this->pages[ $key ] );

		return $url ?: null;
	}

	/**
	 * Adds a redirect_to query parameter to a URL when provided.
	 *
	 * @param  string|null  $url  Base URL.
	 * @param  string|null  $redirect  Redirect destination.
	 *
	 * @return string Modified URL (or empty string if base missing).
	 */
	private function add_redirect( ?string $url, $redirect ): string {
		if ( ! $url ) {
			return '';
		}

		if ( ! empty( $redirect ) ) {
			$url = add_query_arg( 'redirect_to', urlencode( $redirect ), $url );
		}

		return $url;
	}

	/**
	 * Filters the WordPress login URL.
	 *
	 * @param  string  $login_url  Default login URL.
	 * @param  string  $redirect  Optional redirect target.
	 * @param  bool  $force_reauth  Whether re-authentication is required.
	 *
	 * @return string Modified login URL.
	 */
	public function login_url( $login_url, $redirect, $force_reauth ) {
		if ( ! $this->is_frontend() ) {
			return $login_url;
		}

		$url = $this->get_page_url( 'login' );
		if ( ! $url ) {
			return $login_url;
		}

		$url = $this->add_redirect( $url, $redirect );

		if ( $force_reauth ) {
			$url = add_query_arg( 'reauth', '1', $url );
		}

		return $url;
	}

	/**
	 * Filters the WordPress registration URL.
	 *
	 * @param  string  $register_url  Default registration URL.
	 *
	 * @return string Modified registration URL.
	 */
	public function register_url( $register_url ) {
		if ( ! $this->is_frontend() ) {
			return $register_url;
		}

		return $this->get_page_url( 'register' ) ?: $register_url;
	}

	/**
	 * Filters the WordPress lost password URL.
	 *
	 * @param  string  $lost_url  Default lost password URL.
	 * @param  string  $redirect  Optional redirect target.
	 *
	 * @return string Modified lost password URL.
	 */
	public function lost_password_url( $lost_url, $redirect ) {
		if ( ! $this->is_frontend() ) {
			return $lost_url;
		}

		$url = $this->get_page_url( 'lost_password' );
		if ( ! $url ) {
			return $lost_url;
		}

		return $this->add_redirect( $url, $redirect );
	}

	/**
	 * Filters the WordPress logout URL.
	 *
	 * Ensures nonce protection is preserved when using a custom page.
	 *
	 * @param  string  $logout_url  Default logout URL.
	 * @param  string  $redirect  Optional redirect target.
	 *
	 * @return string Modified logout URL.
	 */
	public function logout_url( $logout_url, $redirect ) {
		if ( ! $this->is_frontend() ) {
			return $logout_url;
		}

		$url = $this->get_page_url( 'logout' );

		if ( ! $url ) {
			// fallback: still use default logout with nonce
			return $logout_url;
		}

		// preserve logout nonce action
		$nonce = wp_create_nonce( 'log-out' );

		$url = add_query_arg( '_wpnonce', $nonce, $url );

		if ( ! empty( $redirect ) ) {
			$url = add_query_arg( 'redirect_to', urlencode( $redirect ), $url );
		}

		return $url;
	}

	/**
	 * Rewrites the password reset link contained in the recovery email.
	 *
	 * Replaces the default wp-login.php?action=rp link with the configured
	 * frontend reset password page while preserving the reset key and login.
	 *
	 * @param  string  $message  Email message body.
	 * @param  string  $key  Password reset key.
	 * @param  string  $user_login  User login name.
	 * @param  WP_User  $user_data  User object.
	 *
	 * @return string Modified email message.
	 */
	public function reset_email( $message, $key, $user_login, $user_data ) {
		if ( ! $this->is_frontend() ) {
			return $message;
		}

		$reset = $this->get_page_url( 'reset_password' );
		if ( ! $reset ) {
			return $message;
		}

		$reset_link = add_query_arg( [
			'key'   => $key,
			'login' => rawurlencode( $user_login ),
		], $reset );

		// Replace the wp-login reset URL inside email
		$message = preg_replace( '#https?:\/\/[^\s]*wp-login\.php\?action=rp[^\s]*#', $reset_link, $message );

		return $message;
	}
}
