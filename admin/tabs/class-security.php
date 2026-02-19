<?php

namespace Leira_Auth\Admin\Tabs;

/**
 * Registers the Security settings tab for the Leira Auth plugin.
 */
class Security extends Tab{

	/**
	 * Register the Security settings section and fields.
	 *
	 * @return void
	 */
	public function register(): void {

		$group   = $this->group();
		$page    = $this->page();
		$section = $this->section();

		register_setting( $group, 'leira_auth_max_attempts', [
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 5,
		] );

		register_setting( $group, 'leira_auth_lockout_minutes', [
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 15,
		] );

		register_setting( $group, 'leira_auth_whitelist_ips', [
			'type'              => 'string',
			'sanitize_callback' => [ $this, 'sanitize_ip_list' ],
			'default'           => '',
		] );

		register_setting( $group, 'leira_auth_blacklist_ips', [
			'type'              => 'string',
			'sanitize_callback' => [ $this, 'sanitize_ip_list' ],
			'default'           => '',
		] );

		add_settings_section(
			$section,
			__( 'Security Settings', 'leira-auth' ),
			'__return_null',
			$page
		);

		$fields = [
			'leira_auth_max_attempts'    => [
				'label' => __( 'Max Login Attempts', 'leira-auth' ),
				'type'  => 'number',
			],
			'leira_auth_lockout_minutes' => [
				'label' => __( 'Lockout Duration (minutes)', 'leira-auth' ),
				'type'  => 'number',
			],
			'leira_auth_whitelist_ips'   => [
				'label'       => __( 'Whitelist IPs', 'leira-auth' ),
				'type'        => 'textarea',
				'description' => __( 'Supports single IP, CIDR (192.168.1.0/24) and wildcard (192.168.*.*). One per line.',
					'leira-auth' ),
			],
			'leira_auth_blacklist_ips'   => [
				'label'       => __( 'Blacklist IPs', 'leira-auth' ),
				'type'        => 'textarea',
				'description' => __( 'Supports single IP, CIDR (192.168.1.0/24) and wildcard (192.168.*.*). One per line.',
					'leira-auth' ),
			],
		];

		foreach ( $fields as $id => $config ) {
			add_settings_field(
				$id,
				$config['label'],
				[ $this->settings, 'render_field' ],
				$page,
				$section,
				[
					'id'          => $id,
					'name'        => $id,
					'type'        => $config['type'],
					'value'       => (string) get_option( $id, '' ),
					'description' => $config['description'] ?? '',
					'label_for'   => $id,
				]
			);
		}
	}

	/**
	 * Sanitize textarea containing IPs, CIDR ranges, or wildcard ranges.
	 *
	 * @param  string  $value  Raw textarea value
	 *
	 * @return string Clean newline-separated list
	 */
	public function sanitize_ip_list( string $value ): string {

		$lines = preg_split( '/\r?\n/', $value );
		$valid = [];

		foreach ( $lines as $line ) {

			$line = trim( $line );
			if ( $line === '' ) {
				continue;
			}

			// VALID SINGLE IP
			if ( filter_var( $line, FILTER_VALIDATE_IP ) ) {
				$valid[] = $line;
				continue;
			}

			// VALID CIDR (example: 192.168.1.0/24)
			if ( preg_match( '/^([0-9]{1,3}(?:\.[0-9]{1,3}){3})\/(\d{1,2})$/', $line, $m ) ) {
				$mask = (int) $m[2];
				if ( $mask >= 0 && $mask <= 32 ) {
					$valid[] = $line;
					continue;
				}
			}

			// VALID WILDCARD (example: 192.168.*.*)
			if ( preg_match( '/^([0-9]{1,3}|\*)(\.([0-9]{1,3}|\*)){3}$/', $line ) ) {
				$valid[] = $line;
				continue;
			}
		}

		return implode( "\n", $valid );
	}
}
