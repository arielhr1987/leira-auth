<?php

namespace Leira_Auth\Public;

use Leira_Auth\Includes\Plugin;
use Leira_Auth\Public\Contracts\Form as FormContract;
use Leira_Auth\Public\Forms\Factory;
use Leira_Auth\Public\Renderers\Field_Renderer;
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
		register_block_type(
			$block,
			[
				'render_callback' => [ $this, 'shortcode' ],
			]
		);
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
		$forms = $this->get_forms_factory();
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

		if ( '' === $form_type ) {
			return __( 'You must provide an action attribute in your "leira_auth" shortcode.', 'leira-auth' );
		}

		$form = $forms->create( $form_type, is_array( $attributes ) ? $attributes : [] );
		if ( ! $form ) {
			return __( 'Form not available.', 'leira-auth' );
		}

		$forms->restore( $form );

		if ( method_exists( $form, 'ajax_enabled' ) && $form->ajax_enabled() ) {
			wp_enqueue_script( 'leira-auth-frontend' );
		}

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
			$forms->persist( $form );
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
					'form'           => $form->name(),
					'messages'       => $form->messages()->to_array(),
					//'field_messages' => $this->field_messages( $form ),
					'html'           => $this->renderer()->render( $form ),
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
	 * Filter login URL to custom frontend page.
	 *
	 * @param  string  $login_url
	 * @param  string  $redirect
	 * @param  bool  $force_reauth
	 *
	 * @return string
	 */
	public function login_url( string $login_url, string $redirect, bool $force_reauth ): string {
		if ( is_admin() || isset( $_REQUEST['interim-login'] ) ) {
			return $login_url;
		}

		$custom_url = apply_filters( 'leira_auth_login_url', site_url( 'login' ), $login_url, $redirect,
			$force_reauth );

		return is_string( $custom_url ) && '' !== $custom_url ? $custom_url : $login_url;
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
	 * Build field messages payload for AJAX responses.
	 *
	 * @param  FormContract  $form
	 *
	 * @return array<string, array<int, array{text: string, type: string}>>
	 */
	protected function field_messages( FormContract $form ): array {
		$messages = [];
		foreach ( $form->all() as $field ) {
			if ( ! Field_Renderer::renders_errors_inline( $field ) ) {
				continue;
			}

			if ( ! method_exists( $field, 'messages' ) || ! $field->messages()->has() ) {
				continue;
			}

			$messages[ $field->name() ] = $field->messages()->to_array();
		}

		return $messages;
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
		wp_register_script(
			'leira-auth-frontend',
			plugin_dir_url( __FILE__ ) . 'js/leira-auth-frontend.js',
			[],
			defined( 'LEIRA_AUTH_VERSION' ) ? LEIRA_AUTH_VERSION : '1.0.0',
			true
		);

		wp_localize_script(
			'leira-auth-frontend',
			'leiraAuthFrontend',
			[
				'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
				'ajaxAction' => 'leira_auth_submit',
				'errorText'  => __( 'Unable to submit the form right now.', 'leira-auth' ),
			]
		);
	}
}
