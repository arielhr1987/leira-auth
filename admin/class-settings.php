<?php

namespace Leira_Auth\Admin;

/**
 * Class Settings
 * Manages the settings for the Leira Auth plugin
 *
 * @package Admin
 * @since 1.0.0
 */
class Settings{

	/**
	 * Available tabs on the settings page
	 *
	 * @since 1.0.0
	 * @var string[]
	 */
	protected $tabs = array();

	/**
	 * Constructor to initialize the settings tabs
	 *
	 * @since 1.0.0
	 */
	public function __construct() {}

	/**
	 * Add the admin menu item for settings
	 *
	 * @return void
	 */
	public function admin_menu() {

		$this->tabs = array(
			'general'  => __( 'General', 'leira-auth' ),
			'advanced' => __( 'Advanced', 'leira-auth' ),
		);

		add_options_page(
			__( 'Authentication Settings', 'leira-auth' ),
			__( 'Authentication', 'leira-auth' ),
			'manage_options',
			'leira-auth-settings',
			[ $this, 'render' ]
		);
	}

	/**
	 * Render the settings page
	 *
	 * @return void
	 */
	public function render() {
		// Detect the current tab
		$current_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';

		if ( ! array_key_exists( $current_tab, $this->tabs ) ) {
			$current_tab = 'general';
		}

		//Render title
		echo '<div class="wrap">';
		echo '<h1>' . esc_html( get_admin_page_title() ) . '</h1>';

		//Render tabs
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->tabs as $tab => $label ) {
			$class = ( $tab === $current_tab ) ? ' nav-tab-active' : '';
			$url   = add_query_arg( [ 'tab' => $tab ] );
			printf(
				'<a href="%s" class="nav-tab%s">%s</a>',
				esc_url( $url ),
				esc_attr( $class ),
				esc_html( $label )
			);
		}
		echo '</h2>';

		//Render the form
		echo '<form method="post" action="options.php">';
		settings_fields( "leira_auth_settings_{$current_tab}" );
		do_settings_sections( "leira_auth_settings_{$current_tab}" );
		submit_button();
		echo '</form>';

		echo '</div>';
	}

	/**
	 * Register settings, sections, and fields
	 *
	 * @return void
	 */
	public function register_settings() {
		// General Settings
		register_setting( 'leira_auth_settings_general', 'leira_auth_enable_registration' );
		register_setting( 'leira_auth_settings_general', 'leira_auth_redirect_url' );

		$prefix  = 'leira_auth_settings';
		$page    = $prefix . '_general';
		$section = $page . '_section';
		add_settings_section(
			$section,
			__( 'General Settings', 'leira-auth' ),
			'__return_null',
			$page
		);

		add_settings_field(
			'membership',
			__( 'Membership', 'leira-auth' ),
			array( $this, 'render_field' ),
			$page,
			$section,
			array(
				'id'          => 'users_can_register',
				'label'       => __( 'Anyone can register', 'leira-auth' ),
				'type'        => 'checkbox',
				'value'       => get_option( 'users_can_register' ),
				'description' => __( 'Check this box to enable user registration.', 'leira-auth' ),
			)
		);

		add_settings_field(
			'verification',
			__( 'Verification', 'leira-auth' ),
			array( $this, 'render_field' ),
			$page,
			$section,
			array(
				array(
					'id'    => 'leira_auth_verify_email',
					'label' => __( 'User requires to verify their email', 'leira-auth' ),
					'type'  => 'checkbox',
					'value' => get_option( 'leira_auth_verify_email' ),
					//'description' => __( 'Check this box to enable user registration.', 'leira-auth' ),
				),
				array(
					'id'    => 'leira_auth_admin_approval',
					'label' => __( 'User are required to be approved by admin', 'leira-auth' ),
					'type'  => 'checkbox',
					'value' => get_option( 'leira_auth_admin_approval' ),
					//'description' => __( 'Check this box to enable user registration.', 'leira-auth' ),
				),
			)
		);

		add_settings_field(
			'Admin',
			__( 'Admin', 'leira-auth' ),
			array( $this, 'render_field' ),
			$page,
			$section,
			array(
				array(
					'id'    => 'leira_auth_admin_access',
					'label' => __( 'Restrict admin access to all user', 'leira-auth' ),
					'type'  => 'checkbox',
					'value' => get_option( 'leira_auth_admin_access' ),
					//'description' => __( 'Only Administrator will be able to access the site.', 'leira-auth' ),
				),
				array(
					'id'    => 'leira_auth_admin_bar',
					'label' => __( 'Hide bar for all user', 'leira-auth' ),
					'type'  => 'checkbox',
					'value' => get_option( 'leira_auth_admin_bar' ),
					//'description' => __( 'Check this box to enable user registration.', 'leira-auth' ),
				),
				array(
					'id'    => 'leira_auth_access',
					'label' => __( 'Only registered user can access the site', 'leira-auth' ),
					'type'  => 'checkbox',
					'value' => get_option( 'leira_auth_access' ),
					//'description' => __( 'Check this box to enable user registration.', 'leira-auth' ),
				),
			)
		);

		// Advanced Settings
		$page = $prefix . 'advanced';
		add_settings_section(
			'leira_auth_advanced_section',
			__( 'Advanced Settings', 'leira-auth' ),
			null,
			$page
		);

		add_settings_field(
			'leira_auth_custom_css',
			__( 'Custom CSS', 'leira-auth' ),
			array( $this, 'render_field' ),
			$page,
			'leira_auth_advanced_section',
			array(
				'label_for' => 'leira_auth_custom_css',
				'class'     => 'leira_auth_row',
			)
		);
	}

	/**
	 * Render a field
	 *
	 * @param  array  $args  List of arguments to render the field
	 *
	 * @return void
	 */
	public function render_field( $args ) {
		if ( is_array( $args ) && isset( $args[0] ) ) {
			//This is a multidimensional array
			echo '<fieldset>';
			array_map( array( $this, 'render_field' ), $args );
			echo '</fieldset>';

			return;
		}

		$defaults = [
			'id'          => '',
			'label'       => '',
			'type'        => 'text',
			'value'       => '',
			'options'     => [],
			'description' => '',
		];
		$args     = wp_parse_args( $args, $defaults );

		$id    = esc_attr( $args['id'] );
		$value = $args['value'];
		$desc  = $args['description'] ? '<p class="description">' . esc_html( $args['description'] ) . '</p>' : '';

		switch ( $args['type'] ) {
			case 'text':
			case 'password':
			case 'number':
				printf(
					'<input type="%1$s" id="%2$s" name="%2$s" value="%3$s" class="regular-text" />%4$s',
					esc_attr( $args['type'] ),
					$id,
					esc_attr( $value ),
					$desc
				);
				break;
			case 'textarea':
				printf(
					'<textarea id="%1$s" name="%1$s" rows="5" cols="50" class="large-text">%2$s</textarea>%3$s',
					$id,
					esc_textarea( $value ),
					$desc
				);
				break;
			case 'checkbox':
				printf(
					'<label for=""><input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s/> %3$s </label>%4$s',
					$id,
					checked( $value, 1, false ),
					esc_html( $args['label'] ),
					!empty($desc) ? $desc : '<br>'
				);
				break;
			case 'select':
				echo '<select id="' . $id . '" name="' . $id . '">';
				foreach ( $args['options'] as $opt_value => $label ) {
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						esc_attr( $opt_value ),
						selected( $value, $opt_value, false ),
						esc_html( $label )
					);
				}
				echo '</select>';
				echo $desc;
				break;
		}
	}

}
