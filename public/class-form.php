<?php

namespace Leira_Auth\Public;

use Exception;
use Leira_Auth\Includes\Plugin;
use WP_Block_Type_Registry;
use WP_Error;

/**
 * Class Form
 * Represents a form to handle submissions
 *
 * @package Public
 * @since 1.0.0
 */
class Form{

	/**
	 * Form name
	 *
	 * @var string - A unique form name.
	 */
	protected $name = '';

	/**
	 * The fields in the form
	 *
	 * @var array - The list of field names and their values
	 */
	protected $fields = [];

	/**
	 * The array of errors detected in the form
	 * @var WP_Error - The errors
	 */
	protected $errors;

	/**
	 * Whether the form was submitted or not
	 * @var bool
	 */
	protected $submitted = false;

	/**
	 * An abstract class that represents form
	 * @throws Exception
	 */
	public function __construct( $name ) {
		$this->errors = new WP_Error();
		if ( empty( $name ) ) {
			throw new Exception( 'Name is required.' );
		}
	}

	/**
	 * Add a field to the form
	 *
	 * @param $field  - The field to add
	 *
	 * @return void
	 */
	public function addField( $field ) {}

	/**
	 * Remove a field from the form
	 *
	 * @param  string  $field  - The field to remove
	 *
	 * @return void
	 */
	public function removeField( $field ) {}


	/**
	 * Handle the form submission
	 * @return bool
	 */
	public function handleRequest(): bool {
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
			return false;
		}

		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], $this->name ) ) {
			$this->errors[] = 'Invalid form submission.';

			return false;
		}

		$this->submitted = true;
		$valid           = true;

		foreach ( $this->fields as $name => $field ) {
			$value = $_POST[ $name ] ?? null;

			if ( ! $field->validate( $value ) ) {
				$valid = false;
			}
		}

		if ( ! $valid ) {
			$this->errors[] = 'Please fix the errors below.';
		}

		return $valid;
	}

	/**
	 * The method to render the form
	 * @return string
	 * @throws Exception
	 * @since 1.0.0
	 */
	public function render(): string {
		$html = "<form method='post' class='needs-validation'>";
		$html .= wp_nonce_field( $this->name, '_wpnonce', true, false );

		if ( ! empty( $this->errors ) ) {
			$html .= '<div class="alert alert-danger">';
			foreach ( $this->errors as $error ) {
				$html .= "<div>{$error}</div>";
			}
			$html .= '</div>';
		}

		foreach ( $this->fields as $field ) {
			$html .= $field->render();
		}

		$html .= '<button class="btn btn-primary" type="submit">Submit</button>';
		$html .= '</form>';

		return $html;
	}

	/**
	 * Get the block name with namespace
	 * @return string
	 * @since 1.0.0
	 */
	protected function get_block_name() {
		$namespace = Plugin::instance()->get_plugin_name();

		return $namespace . '/' . $this->name;
	}

	/**
	 * Register the login block
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function init() {
		$block = plugin_dir_path( __DIR__ ) . 'blocks/' . $this->name . '/block.json';
		register_block_type( $block, [
			'render_callback' => array( $this, 'render' ),
		] );
	}

	/**
	 * Get default attributes for the block
	 *
	 * @return array - Default attributes for the block.
	 * @since 1.0.0
	 */
	protected function get_defaults_attributes() {
		$block_type = WP_Block_Type_Registry::get_instance()->get_registered( $this->name );

		if ( ! $block_type || empty( $block_type->attributes ) ) {
			return [];
		}

		$defaults = [];
		foreach ( $block_type->attributes as $key => $value ) {
			if ( array_key_exists( 'default', $value ) ) {
				$defaults[ $key ] = $value['default'];
			}
		}

		return $defaults;
	}

	/**
	 * A method to handle the form submission
	 * @return void
	 * @throws Exception
	 * @since 1.0.0
	 */
	public function handle() {
		throw new Exception( 'Method not implemented' );
	}

	/**
	 * Traverse the block DOM
	 *
	 * @param  string  $html  - The HTML content to traverse.
	 *
	 * @return string - The modified HTML.
	 * @since 1.0.0
	 * @deprecated
	 */
	protected function traverse_block_dom( $html ) {
		//TODO: remove this method
		if ( ! class_exists( 'DOMDocument' ) || ! function_exists( 'libxml_use_internal_errors' ) ) {
			return $html;
		}

		// Load HTML into DOMDocument
		$dom = new \DOMDocument();

		// Save current libxml error state
		$use_internal_errors = libxml_use_internal_errors( true );

		// Load HTML (force UTF-8)
		$dom->loadHTML( $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );

		libxml_clear_errors();

		// Restore previous libxml error handling
		libxml_use_internal_errors( $use_internal_errors );

		$dom->traverse();
		// Find the password input by ID

		$input = $dom->getElementById( 'user_pass' );
		if ( $input && ! empty( $custom_class ) ) {
			$existing_class = $input->getAttribute( 'class' );
			// Append the custom class without duplicating spaces
			$input->setAttribute( 'class', trim( $existing_class . ' ' . $custom_class ) );
		}

		// Return the modified HTML
		$html = $dom->saveHTML();

		return $html;
	}

	/**
	 * @param $node
	 *
	 * @return void
	 * @deprecated
	 */
	protected function traverse_element( $node ) {
		//TODO: remove this method
		if ( ! class_exists( 'DOMElement' ) ) {
			return;
		}

		if ( $node instanceof \DOMElement ) {
			// Remove all existing attributes
			$existing_attrs = [];
			foreach ( $node->attributes as $attr ) {
				$existing_attrs[] = $attr->name;
			}

			$filter_name    = 'leira_auth_block_attributes_' . str_replace( '/', '_', $this->name );
			$new_attributes = apply_filters( $filter_name, $existing_attrs );

			foreach ( $existing_attrs as $name ) {
				$node->removeAttribute( $name );
			}

			// Set new attributes (skip empty values)
			foreach ( $new_attributes as $name => $value ) {
				if ( $value !== '' && $value !== null ) {
					$node->setAttribute( $name, $value );
				}
			}
		}

		// Recurse into child nodes
		if ( $node->hasChildNodes() ) {
			foreach ( $node->childNodes as $child ) {
				$this->traverse_element( $child );
			}
		}
	}
}
