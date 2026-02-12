<?php

namespace Leira_Auth\Public\Forms;

use Leira_Auth\Public\Contracts\Field;
use Leira_Auth\Public\Contracts\Form as FormInterface;
use Leira_Auth\Public\Messages\Bag;
use Leira_Auth\Public\Messages\Message;

/**
 * Base form implementation.
 *
 * @since 1.0.0
 */
class Form implements FormInterface{

	/**
	 * Form name.
	 *
	 * @var string
	 */
	protected string $name;

	/**
	 * Ordered list of form fields.
	 *
	 * @var Field[]
	 */
	protected array $fields = [];

	/**
	 * Form-level messages.
	 *
	 * @var Bag
	 */
	protected Bag $messages;

	/**
	 * Default submit button label.
	 *
	 * @var string
	 */
	protected string $submit_label = 'Submit';

	/**
	 * Class constructor.
	 *
	 * @param  string  $name  Form name.
	 */
	public function __construct( string $name ) {
		$this->name     = $name;
		$this->messages = new Bag();
	}

	/**
	 * Get the form name.
	 *
	 * @return string
	 */
	public function name(): string {
		return $this->name;
	}

	/**
	 * Set submit button label.
	 *
	 * @param  string  $label
	 *
	 * @return self
	 */
	public function set_submit_label( string $label ): self {
		if ( '' !== trim( $label ) ) {
			$this->submit_label = $label;
		}

		return $this;
	}

	/**
	 * Add field to the form.
	 *
	 * @param  Field  $field
	 *
	 * @return $this
	 */
	public function add_field( Field $field ): self {
		$this->fields[ $field->get_name() ] = $field;
		$field->set_form( $this );

		return $this;
	}

	/**
	 * Get field by name.
	 *
	 * @param  string  $name
	 *
	 * @return Field|null
	 */
	public function get_field( string $name ): ?Field {
		return $this->fields[ $name ] ?? null;
	}

	/**
	 * Remove field by name.
	 *
	 * @param  string  $name
	 *
	 * @return $this
	 */
	public function remove_field( string $name ): self {
		unset( $this->fields[ $name ] );

		return $this;
	}

	/**
	 * Remove all fields from the form.
	 *
	 * @return self
	 */
	public function empty_fields(): self {
		$this->fields = [];

		return $this;
	}

	/**
	 * Get form fields.
	 *
	 * @return Field[]
	 */
	public function fields(): array {
		return $this->fields;
	}

	/**
	 * Get submitted data from current field values.
	 *
	 * @return array
	 */
	public function data(): array {
		$data = [];
		foreach ( $this->fields() as $field ) {
			$data[ $field->get_name() ] = $field->get_value();
		}

		return $data;
	}

	/**
	 * Validate the form fields against incoming data.
	 *
	 * @param  mixed  $data
	 *
	 * @return bool
	 */
	public function validate( mixed $data = null ): bool {
		$data = is_array( $data ) ? $data : [];

		$valid = true;
		foreach ( $this->fields() as $name => $field ) {
			$value = $data[ $name ] ?? null;
			if ( ! $field->validate( $value ) ) {
				$valid = false;
			}
		}

		if ( ! $valid ) {
			$this->append_non_inline_field_messages();
		}

		return $valid;
	}

	/**
	 * Get form-level messages.
	 *
	 * @return Bag
	 */
	public function messages(): Bag {
		return $this->messages;
	}

	/**
	 * Handle form POST request.
	 *
	 * @return bool
	 */
	public function handle(): bool {
		$this->messages()->clear();

		$method = strtoupper( $_SERVER['REQUEST_METHOD'] ?? '' );
		if ( 'POST' !== $method ) {
			return false;
		}

		if ( ! wp_verify_nonce( $_POST['_wpnonce'] ?? '', $this->name() ) ) {
			$this->messages()->add( __( 'Invalid form submission.', 'leira-auth' ), Message::ERROR );

			return false;
		}

		$data  = is_array( $_POST ) ? wp_unslash( $_POST ) : [];
		$valid = $this->validate( $data );

		if ( ! $valid ) {
			$this->messages()->add( __( 'Please fix the errors below.', 'leira-auth' ), Message::ERROR );
		}

		return $valid;
	}

	/**
	 * Render form markup.
	 *
	 * @return string
	 */
	public function render(): string {
		$html = [];

		// Open form + nonce.
		$html[] = '<form method="post" class="leira-auth-form">';
		$html[] = wp_nonce_field( $this->name(), '_wpnonce', true, false );

		// Form-level errors.
		$messages = array_filter(
			$this->messages()->all(),
			static function ( Message $message ): bool {
				return $message->is_error();
			}
		);
		if ( ! empty( $messages ) ) {
			$html[] = '<div class="alert alert-danger">';
			if ( 1 === count( $messages ) ) {
				$html[] = '<div>' . esc_html( $messages[0]->text() ) . '</div>';
			} else {
				foreach ( $messages as $message ) {
					$html[] = '<div>' . esc_html( $message->text() ) . '</div>';
				}
			}
			$html[] = '</div>';
		}

		// Fields.
		foreach ( $this->fields as $field ) {
			$html[] = $field->render();
		}

		// Submit button.
		$html[] = sprintf(
			'<button type="submit" class="btn btn-primary">%s</button>',
			esc_html( $this->submit_label )
		);

		// Close form.
		$html[] = '</form>';

		$html = array_filter( $html, static fn( string $line ): bool => '' !== trim( $line ) );

		return implode( PHP_EOL, $html );
	}

	/**
	 * Build form fields from options.
	 *
	 * @param  array  $options
	 *
	 * @return void
	 */
	public function build( array $options ): void {
		// Base class intentionally empty.
	}

	/**
	 * Export form state.
	 *
	 * @return array
	 */
	public function state(): array {
		$state = [
			'messages' => $this->messages()->to_array(),
			'fields'   => [],
		];

		foreach ( $this->fields() as $field ) {
			$state['fields'][ $field->get_name() ] = $field->state();
		}

		return $state;
	}

	/**
	 * Restore a previous form state.
	 *
	 * @param  array  $state
	 *
	 * @return void
	 */
	public function restore( array $state ): void {
		$this->messages()->clear();
		$messages = $state['messages'] ?? [];
		if ( is_array( $messages ) ) {
			foreach ( $messages as $message ) {
				if ( ! is_array( $message ) ) {
					continue;
				}
				$text = isset( $message['text'] ) ? (string) $message['text'] : '';
				if ( '' === $text ) {
					continue;
				}
				$type = isset( $message['type'] ) ? (string) $message['type'] : Message::ERROR;
				$this->messages()->add( $text, $type );
			}
		}

		$fields = $state['fields'] ?? [];
		if ( ! is_array( $fields ) ) {
			return;
		}

		foreach ( $fields as $name => $field_state ) {
			$field = $this->get_field( (string) $name );
			if ( ! $field || ! is_array( $field_state ) ) {
				continue;
			}
			$field->restore( $field_state );
		}
	}

	/**
	 * Move errors from non-inline fields (for example hidden fields) to form-level messages.
	 *
	 * @return void
	 */
	protected function append_non_inline_field_messages(): void {
		foreach ( $this->fields() as $field ) {
			$should_render_inline = true;
			if ( method_exists( $field, 'should_render_errors_inline' ) ) {
				$should_render_inline = (bool) $field->should_render_errors_inline();
			}

			if ( $should_render_inline ) {
				continue;
			}

			foreach ( $field->messages()->all() as $message ) {
				$this->messages()->add( $message->text(), $message->type() );
			}
		}
	}
}
