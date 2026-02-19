<?php

namespace Leira_Auth\Admin;

/**
 * Class Settings.
 *
 * Manages plugin settings page and tab registration.
 *
 * @package Admin
 * @since 1.0.0
 */
class Settings{

	/**
	 * Available tabs on the settings page.
	 *
	 * @var array<string, string>
	 */
	protected array $tabs = array();

	/**
	 * Constructor.
	 */
	public function __construct() {}

	/**
	 * Add the admin menu item for settings.
	 *
	 * @return void
	 */
	public function admin_menu() {
		add_options_page(
			__( 'Authentication Settings', 'leira-auth' ),
			__( 'Authentication', 'leira-auth' ),
			'manage_options',
			'leira-auth-settings',
			[ $this, 'render' ]
		);
	}

	/**
	 * Register settings, sections, and fields for each tab.
	 *
	 * @return void
	 */
	public function admin_init() {
		$this->tabs = array(
			'general'  => __( 'General', 'leira-auth' ),
			'pages'    => __( 'Pages', 'leira-auth' ),
			'security' => __( 'Security', 'leira-auth' ),
			'advanced' => __( 'Advanced', 'leira-auth' ),
		);
		foreach ( array_keys( $this->tabs ) as $tab ) {
			$this->register_tab( $tab );
		}
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public function render() {
		$current_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general';
		if ( ! array_key_exists( $current_tab, $this->tabs ) ) {
			$current_tab = 'general';
		}

		echo '<div class="wrap">';
		echo '<h1>' . esc_html( get_admin_page_title() ) . '</h1>';

		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->tabs as $tab => $label ) {
			$class = ( $tab === $current_tab ) ? ' nav-tab-active' : '';
			$url   = add_query_arg( array( 'tab' => $tab ) );
			printf(
				'<a href="%s" class="nav-tab%s">%s</a>',
				esc_url( $url ),
				esc_attr( $class ),
				esc_html( $label )
			);
		}
		echo '</h2>';

		echo '<form method="post" action="options.php">';
		settings_fields( 'leira_auth_settings_' . $current_tab );
		do_settings_sections( 'leira_auth_settings_' . $current_tab );
		submit_button();
		echo '</form>';
		echo '</div>';
	}

	/**
	 * Register one tab from a dedicated file.
	 *
	 * @param  string  $tab
	 *
	 * @return void
	 */
	protected function register_tab( string $tab ): void {
		// Ensure the tab exists
		if ( ! isset( $this->tabs[ $tab ] ) ) {
			return;
		}
		// Build the class name from the tab slug (e.g. "general" → Leira_Auth\Admin\Tabs\General)
		$class_name = 'Leira_Auth\\Admin\\Tabs\\' . ucfirst( $tab );

		if ( ! class_exists( $class_name ) ) {
			return;
		}

		$instance = new $class_name( $this );
		if ( method_exists( $instance, 'register' ) ) {
			$instance->register();
		}
	}

	/**
	 * Render one settings field.
	 *
	 * @param  array<int|string, mixed>  $args
	 *
	 * @return void
	 */
	public function render_field( $args ) {
		if ( is_array( $args ) && isset( $args[0] ) ) {
			echo '<fieldset>';
			array_map( array( $this, 'render_field' ), $args );
			echo '</fieldset>';

			return;
		}

		$defaults = array(
			'id'          => '',
			'name'        => '',
			'label'       => '',
			'type'        => 'text',
			'value'       => '',
			'options'     => array(),
			'description' => '',
		);
		$args     = wp_parse_args( $args, $defaults );

		$id    = esc_attr( (string) $args['id'] );
		$name  = '' !== (string) $args['name'] ? (string) $args['name'] : (string) $args['id'];
		$value = $args['value'];
		$desc  = ! empty( $args['description'] )
			? '<p class="description">' . esc_html( (string) $args['description'] ) . '</p>'
			: '';

		switch ( $args['type'] ) {
			case 'text':
			case 'password':
			case 'email':
			case 'number':
				printf(
					'<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" class="regular-text" />%5$s',
					esc_attr( (string) $args['type'] ),
					$id,
					esc_attr( $name ),
					esc_attr( (string) $value ),
					$desc
				);
				break;
			case 'textarea':
				printf(
					'<textarea id="%1$s" name="%2$s" rows="5" cols="50" class="large-text">%3$s</textarea>%4$s',
					$id,
					esc_attr( $name ),
					esc_textarea( (string) $value ),
					$desc
				);
				break;
			case 'checkbox':
				printf(
					'<label><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s /> %4$s</label>%5$s',
					$id,
					esc_attr( $name ),
					checked( $value, 1, false ),
					esc_html( (string) $args['label'] ),
					'' !== $desc ? $desc : '<br>'
				);
				break;
			case 'select':
				echo '<select id="' . $id . '" name="' . esc_attr( $name ) . '">';
				foreach ( (array) $args['options'] as $opt_value => $label ) {
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						esc_attr( (string) $opt_value ),
						selected( (string) $value, (string) $opt_value, false ),
						esc_html( (string) $label )
					);
				}
				echo '</select>';
				echo $desc;
				break;
			case 'page':
				$value = (int) $value;
				wp_dropdown_pages( [
					'name'              => $name,
					'id'                => $id,
					'selected'          => $value,
					'show_option_none'  => __( '— Select a page —' ),
					'option_none_value' => '',
				] );

				// Optional "create new page" link shown inline to the right
				if ( ! empty( $args['new_page_link'] ) ) {
					$create_url = admin_url( 'post-new.php?post_type=page' );
					$icon       = '<span class="dashicons dashicons-external" style="font-size:14px;vertical-align:middle;"></span>';
					echo sprintf(
						' <a href="%s" target="_blank">%s%s</a>',
						esc_url( $create_url ),
						esc_html__( 'Create new', 'leira-auth' ),
						$icon
					);
				}

				echo $desc;
				break;
		}
	}
}
