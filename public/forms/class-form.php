<?php

namespace Leira_Auth\Public\Forms;

use Leira_Auth\Public\Contracts\Field as Field_Contract;
use Leira_Auth\Public\Contracts\Form as Form_Contract;
use Leira_Auth\Public\Fields\Hidden;
use Leira_Auth\Public\Fields\Nonce;
use Leira_Auth\Public\Html;
use Leira_Auth\Public\Messages\Message;
use Leira_Auth\Public\Traits\Has_Messages;
use Leira_Auth\Public\Traits\Has_Options;

/**
 * Base form implementation.
 *
 * @since 1.0.0
 */
class Form implements Form_Contract{

	use Has_Options;
	use Has_Messages;

	/**
	 * Form identifier.
	 *
	 * @var string
	 */
	protected string $name;

	/**
	 * Child fields.
	 *
	 * @var array<string, Field_Contract>
	 */
	protected array $fields = [];

	/**
	 * Constructor.
	 *
	 * @param  string  $name
	 */
	public function __construct( string $name ) {
		//Set the form name
		$this->name = $name;
		//Clear any fields
		$this->clear_fields();
		//Add default form fields
		$this->add_field( new Hidden( 'action', [ 'default' => $name ] ) );
		$this->add_field( new Nonce( $name ) );
	}

	/**
	 * Form name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Return field values.
	 *
	 * @return array
	 */
	public function get_values(): array {
		$data = [];
		foreach ( $this->all_fields() as $field ) {
			$data[ $field->get_name() ] = $field->get_value();
		}

		return $data;
	}

	/**
	 * Determine if the current form is ajax.
	 *
	 * @return bool
	 */
	public function is_ajax(): bool {
		return (bool) $this->get( 'ajax', false );
	}

	/**
	 * Set the form ajax option.
	 *
	 * @param  bool  $value
	 *
	 * @return void
	 */
	public function set_ajax( bool $value = true ): void {
		$this->set( 'ajax', $value );
	}

	/**
	 * Get child field by key.
	 *
	 * @param  string  $name
	 *
	 * @return Field_Contract|null
	 */
	public function get_field( string $name ): ?Field_Contract {
		$field = $this->fields[ $name ] ?? null;

		return $field instanceof Field_Contract ? $field : null;
	}

	/**
	 * Add a child field.
	 *
	 * @param  Field_Contract  $field
	 *
	 * @return self
	 */
	public function add_field( Field_Contract $field ): self {
		$field->set_form( $this );
		$this->fields[ $field->get_name() ] = $field;

		return $this;
	}

	/**
	 * Get all child fields.
	 *
	 * @return array
	 */
	public function all_fields(): array {
		return $this->fields;
	}

	/**
	 * Remove one child field by key.
	 *
	 * @param  string  $name
	 *
	 * @return self
	 */
	public function remove_field( string $name ): self {
		unset( $this->fields[ $name ] );

		return $this;
	}

	/**
	 * Remove all child fields.
	 *
	 * @return self
	 */
	public function clear_fields(): self {
		$this->fields = [];

		return $this;
	}

	/**
	 * Build form fields and defaults.
	 *
	 * @param  array  $options
	 *
	 * @return void
	 */
	public function build( array $options ): void {}

	/**
	 * Handle current request.
	 *
	 * @return bool
	 */
	public function handle(): bool {
		//Implement in child class
		return true;
	}

	/**
	 * Export a current form snapshot.
	 *
	 * @return array
	 */
	public function snapshot(): array {
		$messages = [];
		foreach ( $this->messages() as $message ) {
			$messages[] = [
				'text' => $message->text(),
				'type' => $message->type(),
			];
		}

		$fields = [];
		foreach ( $this->all_fields() as $field ) {
			foreach ( $field->messages() as $message ) {
				$is_restorable = (bool) $field->get( 'restorable', false );
				if ( $is_restorable ) {
					$fields[ $field->get_name() ]['value'] = $field->get_value();
				}
				$fields[ $field->get_name() ]['messages'][] = [
					'text' => $message->text(),
					'type' => $message->type(),
				];
			}
		}

		return array_filter( compact( 'messages', 'fields' ) );
	}

	/**
	 * Restore a previous version of the form.
	 *
	 * @param  array  $data
	 *
	 * @return void
	 */
	public function restore( $data ): void {
		// Form error messages
		$messages = wp_unslash( $data['messages'] ?? [] );
		foreach ( $messages as $message ) {
			$text = sanitize_text_field( $message['text'] ?? '' );
			$type = sanitize_text_field( $message['type'] ?? Message::ERROR );
			$this->add_message( $text, $type );
		}
		// Restore field values and messages
		$fields = wp_unslash( $data['fields'] ?? [] );
		foreach ( $fields as $name => $field_data ) {
			// Get the field
			$name       = sanitize_text_field( $name );
			$field_data = wp_unslash( $field_data );
			$field      = $this->get_field( $name );
			if ( ! $field ) {
				continue;
			}
			// Restore field value
			$value = sanitize_text_field( $field_data['value'] ?? null );
			if ( $field->get( 'restorable', false ) && ! is_null( $value ) ) {
				$field->set( 'default', $value );
			}
			// Restore field messages
			$messages = wp_unslash( $field_data['messages'] ?? [] );
			foreach ( $messages as $message ) {
				$text = sanitize_text_field( $message['text'] ?? '' );
				$type = sanitize_text_field( $message['type'] ?? Message::ERROR );
				if ( ! empty( $text ) ) {
					$field->add_message( $text, $type );
				}
			}
		}
	}

	/**
	 * Render form HTML.
	 *
	 * @return string
	 */
	public function render(): string {

		do_action( 'leira_auth_form_before_render', $this );

		$attr = array_filter( [
			'method'               => 'post',
			'id'                   => $this->get( 'id' ),
			'class'                => Html::merge_classes( $this->get( 'class', 'leira-auth-form' ) ),
			'action'               => $this->get( 'action' ),
			'data-leira-auth-ajax' => $this->is_ajax() ? '1' : null,
		] );

		$html = '<div ' . get_block_wrapper_attributes() . '>';
		$html .= '<div class="leira-auth">';
		$html .= '<form ' . Html::render_attributes( $attr ) . '>';
		$html .= $this->render_messages();

		$fields = [];
		foreach ( $this->all_fields() as $field ) {
			$fields[] = $field->render();
		}
		$fields = apply_filters( 'leira_auth_form_fields_render', $fields, $this );
		$html   .= implode( '', $fields );

		$html .= '</form>';
		$html .= '</div>';
		$html .= '</div>';

		return (string) apply_filters( 'leira_auth_render_form', $html, $this, null );
	}

	/**
	 * Validate submitted payload.
	 *
	 * @param  mixed  $data
	 *
	 * @return bool
	 */
	public function validate( mixed $data ): bool {
		$data  = is_array( $data ) ? $data : [];
		$valid = true;

		foreach ( $this->all_fields() as $field ) {
			$name  = $field->get_name();
			$value = $data[ $name ] ?? null;
			if ( ! $field->validate( $value ) ) {
				$valid = false;
			}
		}

		return $valid;
	}

	/**
	 * Render form-level messages grouped by type.
	 *
	 * @return string
	 */
	protected function render_messages(): string {
		$success = '';
		$error   = '';

		foreach ( $this->messages() as $message ) {
			$type = $message->is_success() ? Message::SUCCESS : Message::ERROR;
			$role = Message::SUCCESS === $type ? 'status' : 'alert';
			$item = sprintf(
				'<div class="leira-form-message leira-form-message--%1$s" role="%2$s"><p>%3$s</p></div>',
				esc_attr( $type ),
				esc_attr( $role ),
				esc_html( $message->text() )
			);

			if ( Message::SUCCESS === $type ) {
				$success .= $item;
			} else {
				$error .= $item;
			}
		}

		if ( '' === $success && '' === $error ) {
			return '';
		}

		$html = '<div class="leira-form-messages">';
		if ( '' !== $success ) {
			$html .= '<div class="leira-form-messages-success">' . $success . '</div>';
		}
		if ( '' !== $error ) {
			$html .= '<div class="leira-form-messages-error">' . $error . '</div>';
		}
		$html .= '</div>';

		return $html;
	}

}
