<?php

namespace Leira_Auth\Public;

use Leira_Auth\Includes\Plugin;
use Leira_Auth\Public\Forms\Factory;
use Leira_Auth\Public\Renderers\Renderer;
use WP_Block;
use WP_Post;

/**
 * Public-facing controller.
 *
 * @since 1.0.0
 */
class Controller{

	/**
	 * Form renderer.
	 *
	 * @var Renderer|null
	 */
	protected ?Renderer $form_renderer = null;

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
	 * Register public blocks and scripts.
	 *
	 * @return void
	 */
	public function init(): void {
		$this->register_frontend_script();

		$block = plugin_dir_path( __DIR__ ) . 'blocks/login/block.json';
		register_block_type( $block, [
			'render_callback' => [ $this, 'shortcode' ],
		] );

//		register_block_style( 'leira-auth/login', [
//			'name'         => 'neon-glow',
//			'label'        => __( 'Neon Glow', 'my-plugin' ),
//			'inline_style' => '.is-style-neon-glow { box-shadow: 0 0 10px cyan; }',
//		] );
	}

	/**
	 * Shortcode and block render callback.
	 *
	 * @param  array<string, mixed>|mixed  $attributes
	 * @param  string  $content
	 * @param  mixed  $block
	 *
	 * @return string
	 */
	public function shortcode( $attributes = [], $content = '', $block = null ): string {
		// Get the form factory
		$forms = $this->get_forms_factory();
		// Form factory not found
		if ( ! $forms ) {
			return __( 'Authentication forms are not available.', 'leira-auth' );
		}

		$form_type = '';
		if ( $block instanceof WP_Block ) {
			$form_type = strtolower( (string) $block->name );
			$form_type = substr( $form_type, strrpos( $form_type, '/' ) + 1 );
		}

		if ( '' === $form_type ) {
			$attributes = is_array( $attributes ) ? $attributes : [];
			$form_type  = $attributes['action'] ?? $attributes['form'] ?? '';
			$form_type  = sanitize_key( (string) $form_type );
		}

		// No form type was specified
		if ( '' === $form_type ) {
			return __( 'You must provide an action attribute in your "leira_auth" shortcode.', 'leira-auth' );
		}

		// Create the form
		$form = $forms->create( $form_type, is_array( $attributes ) ? $attributes : [] );
		if ( ! $form ) {
			return __( 'Form not available.', 'leira-auth' );
		}

		// Restore the plugin to previous state
		$form->restore( Plugin::instance()->flash->all() );

		// Enqueue form styles
//		wp_enqueue_style( 'leira-auth-forms-css' );
		if ( method_exists( $form, 'is_ajax' ) && $form->is_ajax() ) {
			// Enqueue form js
			wp_enqueue_script( 'leira-auth-forms-js' );
		}

		// Render the form
		return $this->renderer()->render( $form );
	}

	/**
	 * Handle regular page form submissions.
	 *
	 * @return void
	 */
	public function handle(): void {
		$method = strtoupper( $_SERVER['REQUEST_METHOD'] ?? '' );
		if ( 'POST' !== $method ) {
			return;
		}

		if ( ! is_singular() ) {
			return;
		}

		$post = get_post();
		if ( ! ( $post instanceof WP_Post ) ) {
			return;
		}

		if ( ! $this->post_contains_auth_form( $post ) ) {
			return;
		}

		$forms = $this->get_forms_factory();
		if ( ! $forms ) {
			return;
		}

		$data      = is_array( $_POST ) ? wp_unslash( $_POST ) : [];
		$form_type = $this->resolve_form_type( $data );
		if ( '' === $form_type ) {
			return;
		}

		$form = $forms->create( $form_type, $data );
		if ( ! $form ) {
			return;
		}

		if ( ! $form->handle() ) {
			$snapshot = $form->snapshot();
			Plugin::instance()->flash->add( $snapshot );
		}

		wp_safe_redirect( $this->get_return_url( $post ) );
		exit;
	}

	/**
	 * Handle AJAX form submissions.
	 *
	 * @return void
	 */
	public function ajax_handle(): void {
		$method = strtoupper( $_SERVER['REQUEST_METHOD'] ?? '' );
		if ( 'POST' !== $method ) {
			wp_send_json_error( [ 'message' => __( 'Method not allowed.', 'leira-auth' ) ], 405 );
		}

		$forms = $this->get_forms_factory();
		if ( ! $forms ) {
			wp_send_json_error( [ 'message' => __( 'Authentication forms are not available.', 'leira-auth' ) ], 500 );
		}

		$data      = is_array( $_POST ) ? wp_unslash( $_POST ) : [];
		$form_type = $this->resolve_form_type( $data );
		if ( '' === $form_type ) {
			wp_send_json_error( [ 'message' => __( 'Missing form type.', 'leira-auth' ) ], 400 );
		}

		$form = $forms->create( $form_type, $data );
		if ( ! $form ) {
			wp_send_json_error( [ 'message' => __( 'Form not available.', 'leira-auth' ) ], 404 );
		}

		if ( ! $form->handle() ) {
			wp_send_json_error(
				[
					'form'     => $form->name(),
					'messages' => $form->messages()->to_array(),
					//'field_messages' => $this->field_messages( $form ),
					'html'     => $this->renderer()->render( $form ),
				],
				422
			);
		}

		$response = [
			'form'     => $form->name(),
			'messages' => $form->messages()->to_array(),
		];

		if ( method_exists( $form, 'redirect_url' ) ) {
			$redirect = (string) $form->redirect_url();
			if ( '' !== $redirect ) {
				$response['redirect'] = $redirect;
			}
		}

		$response = (array) apply_filters( 'leira_auth_ajax_success_response', $response, $form, $data );

		wp_send_json_success( $response );
	}

	/**
	 * Determine if the current post contains auth forms.
	 *
	 * @param  WP_Post  $post
	 *
	 * @return bool
	 */
	protected function post_contains_auth_form( WP_Post $post ): bool {
		if ( has_shortcode( $post->post_content, 'leira_auth' ) ) {
			return true;
		}

		$forms = $this->get_forms_factory();
		if ( ! $forms ) {
			return false;
		}

		foreach ( array_keys( $forms->registry() ) as $name ) {
			$block = 'leira-auth/' . $name;
			if ( has_block( $block, $post ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Resolve the forms factory.
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
	 * Resolve return URL after the regular POST submission.
	 *
	 * @param  WP_Post  $post
	 *
	 * @return string
	 */
	protected function get_return_url( WP_Post $post ): string {
		$url = wp_get_referer();
		if ( ! empty( $url ) ) {
			return $url;
		}

		$permalink = get_permalink( $post );

		return is_string( $permalink ) && '' !== $permalink ? $permalink : home_url( '/' );
	}

	/**
	 * Resolve form type from the submitted payload.
	 *
	 * @param  array<string, mixed>  $data
	 *
	 * @return string
	 */
	protected function resolve_form_type( array $data ): string {
		$form_type = sanitize_key( (string) ( $data['_leira_auth_form'] ?? '' ) );
		if ( '' !== $form_type ) {
			return $form_type;
		}

		$action = sanitize_key( (string) ( $data['action'] ?? '' ) );
		if ( 'leira_auth_submit' === $action ) {
			return '';
		}

		return $action;
	}

	/**
	 * Resolve form renderer.
	 *
	 * @return Renderer
	 */
	protected function renderer(): Renderer {
		if ( null === $this->form_renderer ) {
			$this->form_renderer = new Renderer();
		}

		return $this->form_renderer;
	}

	/**
	 * Register frontend AJAX helper script.
	 *
	 * @return void
	 */
	protected function register_frontend_script(): void {
		$asset_file = LEIRA_AUTH_PATH . '/build/forms.asset.php';
		$asset      = file_exists( $asset_file ) ? include $asset_file : [
			'dependencies' => [],
			'version'      => LEIRA_AUTH_VERSION ?? '1.0.0'
		];

		wp_register_script(
			'leira-auth-forms-js',
			LEIRA_AUTH_URL . 'build/forms.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_register_style(
			'leira-auth-forms-css',
			LEIRA_AUTH_URL . 'build/forms.css',
			[ 'wp-block-library' ],
			$asset['version'],
		);

		// Ensure WordPress global + block button styles are available on the frontend
		if ( ! wp_style_is( 'wp-block-library', 'registered' ) ) {
			wp_register_style( 'wp-block-library', includes_url( 'css/dist/block-library/style.min.css' ), [], null );
		}

		wp_localize_script(
			'leira-auth-forms-js',
			'leiraAuthFrontend',
			[
				'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
				'ajaxAction' => 'leira_auth_submit',
				'errorText'  => __( 'Unable to submit the form right now.', 'leira-auth' ),
			]
		);
	}
}
