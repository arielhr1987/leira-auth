<?php

namespace Leira_Auth\Admin\Tabs;

/**
 * Registers the Advanced settings tab for the Leira Auth plugin.
 */
class Advanced extends Tab{

	/**
	 * Register the Advanced settings section and fields.
	 *
	 * Creates the settings section and registers the custom CSS
	 * option used to inject additional styles into authentication
	 * templates.
	 *
	 * @return void
	 */
	public function register(): void {
		$group   = $this->group();
		$page    = $this->page();
		$section = $this->section();

		register_setting( $group, 'leira_auth_custom_css' );

		add_settings_section(
			$section,
			__( 'Advanced Settings', 'leira-auth' ),
			'__return_null',
			$page
		);

		add_settings_field(
			'leira_auth_custom_css',
			__( 'Custom CSS', 'leira-auth' ),
			array( $this->settings, 'render_field' ),
			$page,
			$section,
			array(
				'id'          => 'leira_auth_custom_css',
				'type'        => 'textarea',
				'value'       => (string) get_option( 'leira_auth_custom_css', '' ),
				'description' => __( 'Add CSS applied to authentication templates.', 'leira-auth' ),
			)
		);
	}
}
