<?php

namespace Leira_Auth\Public\Traits;

/**
 * Optional label and help behavior for fields.
 *
 * @since 1.0.0
 */
trait Has_Label_And_Help{

	/**
	 * Get label text.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return $this->options()->get( 'label' );
	}

	/**
	 * Set label text.
	 *
	 * @param  string  $label
	 *
	 * @return self
	 */
	public function set_label( string $label ): self {
		$this->options()->set( 'label', $label );

		return $this;
	}

	/**
	 * Get help text.
	 *
	 * @return string
	 */
	public function get_help(): string {
		return $this->options()->get( 'help' );
	}

	/**
	 * Set help text.
	 *
	 * @param  string  $help
	 *
	 * @return self
	 */
	public function set_help( string $help ): self {
		$this->options()->set( 'help', $help );

		return $this;
	}
}
