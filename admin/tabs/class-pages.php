<?php

namespace Leira_Auth\Admin\Tabs;

/**
 * Registers the Authentication Pages settings tab.
 *
 * This class integrates with the WordPress Settings API to
 * register individual page selectors for each authentication
 * route (login, register, lost password, reset password, logout).
 *
 * Each page is stored as its own option containing the selected
 * WordPress page ID.
 */
class Pages extends Tab{

	/**
	 * Register the settings section and fields for authentication pages.
	 *
	 * @return void
	 */
	public function register(): void {
		$group   = $this->group();
		$page    = $this->page();
		$section = $this->section();

		add_settings_section(
			$section,
			__( 'Authentication Pages', 'leira-auth' ),
			'__return_null',
			$page
		);

		$fields = array(
			'login'          => __( 'Login Page', 'leira-auth' ),
			'register'       => __( 'Registration Page', 'leira-auth' ),
			'lost_password'  => __( 'Lost Password Page', 'leira-auth' ),
			'reset_password' => __( 'Reset Password Page', 'leira-auth' ),
			'logout'         => __( 'Logout Page', 'leira-auth' ),
			'profile'         => __( 'Profile Page', 'leira-auth' ),
		);

		foreach ( $fields as $key => $label ) {
			// register each page as its own option storing a page ID
			$option_name = 'leira_auth_page_' . $key;

			//Register the setting
			register_setting(
				$group,
				$option_name,
				[
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
					'default'           => 0,
				]
			);

			$value = (string) get_option( $option_name, 0 );

			//Render the field
			add_settings_field(
				$option_name,
				$label,
				array( $this->settings, 'render_field' ),
				$page,
				$section,
				array(
					'id'            => $option_name,
					'name'          => $option_name,
					'type'          => 'page',
					'value'         => $value,
					'label_for'     => $option_name,
					'new_page_link' => true,
					'description'   => sprintf(
						__( 'Select the default page used for %s.', 'leira-auth' ),
						str_replace( '_', ' ', $key ) //TODO: Improve this translation
					),
				)
			);
		}
	}
}
