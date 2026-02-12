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
	 * Initialize variables
	 *
	 * @return void
	 */
	public function plugin_loaded() {
		//Factory is initialized here so other plugins can access actions/filters
		Plugin::instance()->forms = new Factory();
		Plugin::instance()->flash = new Flash( Crypto\Factory::create() );
	}

	/**
	 * Register blocks
	 *
	 * @return void
	 */
	public function init() {
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
	public function shortcode( $attributes = [], $content = '', $block = null ) {
		$plugin = Plugin::instance();
		$forms  = $plugin->forms;
		$flash  = $plugin->flash;
		//Handle block logic
		if ( $block instanceof WP_Block ) {
			$name = $block->name;
			$name = strtolower( $name );
			$name = substr( $name, strrpos( $name, '/' ) + 1 );
			$form = $forms->create( $name );
			//Form not found
			if ( empty( $form ) ) {
				return __('Form not available.', 'leira-auth');
			}
		}
		//Handle shortcode logic
		if ( is_string( $block ) ) {
			$name = $attributes['action'] ?? null;
			//No action provided
			if ( empty( $name ) ) {
				return __('You most provide an action attribute in your "leira_auth" shortcode.', 'leira-auth');
			}
			$form = $forms->create( $name );
			//Form not found
			if ( empty( $form ) ) {
				return __('Invalid value for the "action" attribute in the "leira_auth" shortcode.', 'leira-auth');
			}
		}
		//We found a form, let's render it
		if ( isset( $form ) && $form ) {
			//Restore the previous form state (messages, errors and values)
			$forms->restore($form);
			//Render
			return $form->render();
		}

		return '';
	}

	/**
	 * Handle all form submissions
	 * This method checks the form submitted is part of leira_auth and handles it accordingly
	 *
	 * @return void
	 */
	public function handle(): void {
		//Bail if not a POST submission
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
			return;
		}
		//Bail if is not singular (post, page)
		if ( ! is_singular() ) {
			return;
		}
		$post = get_post();
		//Bails if the post does not contain any of our forms
		if ( ! $this->post_contains_auth_form( $post ) ) {
			return;
		}
		//Bails if not action submitted
		if ( ! isset( $_REQUEST['action'] ) ) {
			return;
		}
		$action  = sanitize_text_field( $_REQUEST['action'] );
		$factory = Plugin::instance()->forms;
		$form    = $factory->create( $action );
		//Bail if form unavailable
		if ( ! $form ) {
			//Nothing to handle
			return;
		}
		//validate the form
		if ( $form->validate() ) {
			//handle request
			$form->handle();
		} else {
			//flash the form state (errors and values)
			$factory->persist( $form );
		}
		//redirect back if the handle returns nothing
		$url = wp_get_referer();
		wp_safe_redirect( $url );
		die();//stop execution, wp_safe_redirect does not stop script execution
	}

	/**
	 * Determine if the post contains any of our forms
	 *
	 * @param  WP_Post  $post  The post to check
	 *
	 * @return bool
	 */
	protected function post_contains_auth_form( $post ): bool {
		//Check shortcodes
		if ( has_shortcode( $post->post_content, 'leira_auth' ) ) {
			return true;
		}
		//Check blocks
		$forms = Plugin::instance()->forms->registry();
		$forms = array_keys( $forms );
		foreach ( $forms as $name ) {
			$block = 'leira_auth/' . $name;
			if ( has_block( $block, $post ) ) {
				return true;
			}
		}

		return false;
	}
}
