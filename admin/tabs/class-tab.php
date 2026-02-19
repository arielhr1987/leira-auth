<?php

namespace Leira_Auth\Admin\Tabs;

use Leira_Auth\Admin\Settings;

abstract class Tab{

	/**
	 * Settings service used to resolve tab groups, slugs and render fields.
	 *
	 * @var Settings
	 */
	protected Settings $settings;

	/**
	 * Pages constructor.
	 *
	 * @param  Settings  $settings  Settings service instance.
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Get the tab slug automatically from the class short name.
	 *
	 * @return string
	 */
	protected function get_name(): string {
		// Get the full class name (with namespace)
		$class = static::class;

		// Extract the short class name (after last namespace separator)
		$pos = strrpos($class, '\\');
		$short = ($pos === false) ? $class : substr($class, $pos + 1);

		// Convert to lowercase slug
		return strtolower($short);
	}

	public function group(  ) {
		return 'leira_auth_settings_' . $this->get_name();
	}

	public function page(  ) {
		return 'leira_auth_settings_' . $this->get_name();
	}


	public function section(  ) {
		return $this->page() . '_section';
	}
}
