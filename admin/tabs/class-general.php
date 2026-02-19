<?php

namespace Leira_Auth\Admin\Tabs;

/**
 * Registers the General settings tab for the Leira Auth plugin.
 */
class General extends Tab{

	/**
	 * Register the General settings section and fields.
	 *
	 * Registers WordPress options and attaches the corresponding
	 * settings fields to the General tab using the WordPress
	 * Settings API.
	 *
	 * @return void
	 */
	public function register(): void {
		$group   = $this->group();
		$page    = $this->page();
		$section = $this->section();

		register_setting( $group, 'users_can_register' );
		register_setting( $group, 'leira_auth_verify_email' );
		register_setting( $group, 'leira_auth_admin_approval' );
		register_setting( $group, 'leira_auth_admin_access' );
		register_setting( $group, 'leira_auth_admin_bar' );
		register_setting( $group, 'leira_auth_access' );
		register_setting( $group, 'leira_auth_redirect_url' );

		add_settings_section(
			$section,
			__( 'General Settings', 'leira-auth' ),
			'__return_null',
			$page
		);

		add_settings_field(
			'membership',
			__( 'Membership', 'leira-auth' ),
			array( $this->settings, 'render_field' ),
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
			array( $this->settings, 'render_field' ),
			$page,
			$section,
			array(
				array(
					'id'    => 'leira_auth_verify_email',
					'label' => __( 'Users must verify their email', 'leira-auth' ),
					'type'  => 'checkbox',
					'value' => get_option( 'leira_auth_verify_email' ),
				),
				array(
					'id'    => 'leira_auth_admin_approval',
					'label' => __( 'Users require admin approval', 'leira-auth' ),
					'type'  => 'checkbox',
					'value' => get_option( 'leira_auth_admin_approval' ),
				),
			)
		);

		add_settings_field(
			'admin',
			__( 'Admin', 'leira-auth' ),
			array( $this->settings, 'render_field' ),
			$page,
			$section,
			array(
				array(
					'id'    => 'leira_auth_admin_access',
					'label' => __( 'Restrict admin access for non-admin users', 'leira-auth' ),
					'type'  => 'checkbox',
					'value' => get_option( 'leira_auth_admin_access' ),
				),
				array(
					'id'    => 'leira_auth_admin_bar',
					'label' => __( 'Hide admin bar for non-admin users', 'leira-auth' ),
					'type'  => 'checkbox',
					'value' => get_option( 'leira_auth_admin_bar' ),
				),
				array(
					'id'    => 'leira_auth_access',
					'label' => __( 'Only registered users can access the site', 'leira-auth' ),
					'type'  => 'checkbox',
					'value' => get_option( 'leira_auth_access' ),
				),
			)
		);
	}
}
