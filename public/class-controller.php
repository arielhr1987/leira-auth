<?php

namespace Leira_Auth\Public;

use Leira_Auth\Includes\Plugin;
use Leira_Auth\Public\Forms\Factory;
use WP_Block;
use WP_Post;

/**
 * A class to handle user-facing logic
 *
 * @since 1.0.0
 */
class Controller{

	/**
	 * Initialize shared plugin instances.
	 *
	 * @return void
	 */
	public function plugins_loaded(): void {
		$plugin = Plugin::instance();

		if ( ! ( $plugin->forms instanceof Factory ) ) {
			$plugin->forms = new Factory();
		}

		if ( ! ( $plugin->flash instanceof Flash ) ) {
			$plugin->flash = new Flash( Crypto\Factory::create() );
		}
	}

	/**
	 * Register blocks
	 *
	 * @return void
	 */
	public function init(): void {
		//TODO: Register all other blocks
		$block = plugin_dir_path( __DIR__ ) . 'blocks/login/block.json';
		register_block_type( $block, [
			'render_callback' => array( $this, 'shortcode' ),
		] );
	}

	/**
	 * Shortcode handler for [leira_auth] and callback for blocks
	 *
	 * @param  array  $attributes  The attributes
	 * @param  string  $content  The content of block/shortcode
	 * @param  mixed  $block  The WP_Block instance if block, string otherwise
	 *
	 * @return string HTML output of the login form or message.
	 * @since 1.0.0
	 */
	public function shortcode( $attributes = [], $content = '', $block = null ): string {
		$forms = $this->get_forms_factory();
		if ( ! $forms ) {
			return __( 'Authentication forms are not available.', 'leira-auth' );
		}

		$form_type = '';

		// Handle block logic.
		if ( $block instanceof WP_Block ) {
			$form_type = strtolower( (string) $block->name );
			$form_type = substr( $form_type, strrpos( $form_type, '/' ) + 1 );
		}

		// Handle shortcode logic.
		if ( empty( $form_type ) ) {
			$form_type = $attributes['action'] ?? $attributes['form'] ?? '';
			$form_type = sanitize_key( (string) $form_type );
		}

		// No action provided.
		if ( empty( $form_type ) ) {
			return __( 'You must provide an action attribute in your "leira_auth" shortcode.', 'leira-auth' );
		}

		$form = $forms->create( $form_type, is_array( $attributes ) ? $attributes : [] );
		if ( ! $form ) {
			return __( 'Form not available.', 'leira-auth' );
		}

		// Restore previous form state (messages, errors and values).
		$forms->restore( $form );

		return $form->render();
	}

	/**
	 * Handle all form submissions
	 * This method checks the form submitted is part of leira_auth and handles it accordingly
	 *
	 * @return void
	 */
	public function handle(): void {
		$method = strtoupper( $_SERVER['REQUEST_METHOD'] ?? '' );
		// Bail if not a POST submission.
		if ( 'POST' !== $method ) {
			return;
		}
		// Bail if is not singular (post, page).
		if ( ! is_singular() ) {
			return;
		}
		$post = get_post();
		if ( ! ( $post instanceof WP_Post ) ) {
			return;
		}
		// Bail if the post does not contain any of our forms.
		if ( ! $this->post_contains_auth_form( $post ) ) {
			return;
		}

		$forms = $this->get_forms_factory();
		if ( ! $forms ) {
			return;
		}

		$action = sanitize_key( (string) wp_unslash( $_POST['action'] ?? '' ) );
		if ( empty( $action ) ) {
			return;
		}

		$form = $forms->create( $action, is_array( $_POST ) ? wp_unslash( $_POST ) : [] );

		// Bail if form unavailable.
		if ( ! $form ) {
			return;
		}

		if ( ! $form->handle() ) {
			// Persist the invalid form state (errors and values).
			$forms->persist( $form );
		}

		wp_safe_redirect( $this->get_return_url( $post ) );
		exit;
	}

	/**
	 * Determine if the post contains any of our forms
	 *
	 * @param  WP_Post  $post  The post to check
	 *
	 * @return bool
	 */
	protected function post_contains_auth_form( WP_Post $post ): bool {
		//Check shortcodes
		if ( has_shortcode( $post->post_content, 'leira_auth' ) ) {
			return true;
		}

		$forms = $this->get_forms_factory();
		if ( ! $forms ) {
			return false;
		}

		// Check blocks.
		foreach ( array_keys( $forms->registry() ) as $name ) {
			$block = 'leira-auth/' . $name;
			if ( has_block( $block, $post ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Filter the login URL to point to a custom login page.
	 *
	 * @param  string  $login_url  The original login URL.
	 * @param  string  $redirect  The redirect URL after login.
	 * @param  bool  $force_reauth  Whether to force reauthentication.
	 *
	 * @return string
	 */
	public function login_url( string $login_url, string $redirect, bool $force_reauth ): string {
		if ( is_admin() || isset( $_REQUEST['interim-login'] ) ) {
			return $login_url;
		}

		$custom_url = apply_filters( 'leira_auth_login_url', site_url( 'login' ), $login_url, $redirect, $force_reauth );

		return is_string( $custom_url ) && ! empty( $custom_url ) ? $custom_url : $login_url;
	}

	/**
	 * Get forms factory instance.
	 *
	 * @return Factory|null
	 */
	protected function get_forms_factory(): ?Factory {
		$forms = Plugin::instance()->forms;

		if ( $forms instanceof Factory ) {
			return $forms;
		}

		$this->plugins_loaded();
		$forms = Plugin::instance()->forms;

		return $forms instanceof Factory ? $forms : null;
	}

	/**
	 * Resolve the URL where we should return after submission.
	 *
	 * @param  WP_Post  $post  Current singular post.
	 *
	 * @return string
	 */
	protected function get_return_url( WP_Post $post ): string {
		$url = wp_get_referer();
		if ( ! empty( $url ) ) {
			return $url;
		}

		$permalink = get_permalink( $post );

		return is_string( $permalink ) && ! empty( $permalink ) ? $permalink : home_url( '/' );
	}
}
