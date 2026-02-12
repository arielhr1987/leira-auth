<?php

namespace Leira_Auth\Public;

use Exception;
use Leira_Auth\Includes\Plugin;

/**
 * Class Block
 * Represents a block in the system
 *
 * @package Public
 * @since 1.0.0
 */
abstract class Field{

	/**
	 * Block name
	 *
	 * @var string - The block name including namespace.
	 */
	protected $name = '';

	/**
	 * An abstract class that represents form
	 */
	public function __construct() {}

	/**
	 * The method to render the field
	 * @return string
	 */
	public function render() {
		$html = '<div class="field">';

		return $html;
	}
}
