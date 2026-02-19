<?php

namespace Leira_Auth\Public\Collections;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

/**
 * Generic typed collection.
 *
 * Stores a list of items that may optionally be constrained by a type.
 *
 * @since 1.0.0
 */
class Collection implements IteratorAggregate, Countable{

	/**
	 * Allowed class/interface name.
	 *
	 * @var string|null
	 */
	protected ?string $type = null;

	/**
	 * Collection items.
	 *
	 * @var array<int|string, mixed>
	 */
	protected array $items = [];

	/**
	 * Constructor.
	 *
	 * @param  string|null  $type
	 */
	public function __construct( ?string $type = null ) {
		if ( null !== $type ) {
			$type = trim( $type );
			if ( '' === $type ) {
				throw new InvalidArgumentException( 'Collection type cannot be empty.' );
			}

			if ( ! interface_exists( $type ) && ! class_exists( $type ) ) {
				throw new InvalidArgumentException( sprintf( 'Type "%s" does not exist.', $type ) );
			}
		}

		$this->type = $type;
	}

	/**
	 * Get collection type.
	 *
	 * @return string|null
	 */
	public function type(): ?string {
		return $this->type;
	}

	/**
	 * Add one item to the collection.
	 *
	 * @param  mixed  $item
	 *
	 * @return self
	 */
	public function add( mixed $item ): self {
		$this->assert_type( $item );
		$this->items[] = $item;

		return $this;
	}

	/**
	 * Set one item by key.
	 *
	 * @param  int|string  $key
	 * @param  mixed  $item
	 *
	 * @return self
	 */
	public function set( int|string $key, mixed $item ): self {
		$this->assert_type( $item );
		$this->items[ $key ] = $item;

		return $this;
	}

	/**
	 * Remove one item by key.
	 *
	 * @param  int|string  $key
	 *
	 * @return self
	 */
	public function remove( int|string $key ): self {
		unset( $this->items[ $key ] );

		return $this;
	}

	/**
	 * Get one item by key.
	 *
	 * @param  int|string  $key
	 * @param  mixed  $default
	 *
	 * @return mixed
	 */
	public function get( int|string $key, mixed $default = null ): mixed {
		return $this->items[ $key ] ?? $default;
	}

	/**
	 * Filter items with callback and return a new collection.
	 *
	 * @param  callable  $callback
	 *
	 * @return static
	 */
	public function filter( callable $callback ): static {
		$items = array_filter(
			$this->items,
			static fn( mixed $item, int|string $key ): bool => (bool) $callback( $item, $key ),
			ARRAY_FILTER_USE_BOTH
		);

		return static::from( $items, $this->type );
	}

	/**
	 * Find the first item matching callback.
	 *
	 * @param  callable  $callback
	 *
	 * @return mixed
	 */
	public function find( callable $callback ): mixed {
		foreach ( $this->items as $key => $item ) {
			if ( (bool) $callback( $item, $key ) ) {
				return $item;
			}
		}

		return null;
	}

	/**
	 * Check whether an item is present in the collection.
	 *
	 * @param  mixed  $needle
	 * @param  bool  $strict
	 *
	 * @return bool
	 */
	public function includes( mixed $needle, bool $strict = true ): bool {
		return in_array( $needle, $this->items, $strict );
	}

	/**
	 * Transform items with callback and return a new collection.
	 *
	 * @param  callable  $callback
	 *
	 * @return static
	 */
	public function map( callable $callback ): static {
		$items = [];

		foreach ( $this->items as $key => $item ) {
			$items[ $key ] = $callback( $item, $key );
		}

		return static::from( $items, $this->type );
	}

	/**
	 * Return true if any item matches callback.
	 *
	 * @param  callable  $callback
	 *
	 * @return bool
	 */
	public function some( callable $callback ): bool {
		foreach ( $this->items as $key => $item ) {
			if ( (bool) $callback( $item, $key ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Return a sorted copy of the collection.
	 *
	 * @param  callable|null  $callback
	 *
	 * @return static
	 */
	public function sort( ?callable $callback = null ): static {
		$items = $this->items;

		if ( is_callable( $callback ) ) {
			uasort( $items, $callback );
		} elseif ( $this->can_sort_without_callback( $items ) ) {
			asort( $items );
		}

		return static::from( $items, $this->type );
	}

	/**
	 * Build a collection from an array.
	 *
	 * @param  array<int|string, mixed>  $items
	 * @param  string|null  $type
	 *
	 * @return static
	 */
	public static function from( array $items, ?string $type = null ): static {
		if ( self::class === static::class ) {
			$collection = new self( $type );
		} else {
			try {
				$collection = new static();
			} catch ( \ArgumentCountError ) {
				$collection = new static( $type );
			}
		}

		foreach ( $items as $key => $item ) {
			$collection->set( $key, $item );
		}

		return $collection;
	}

	/**
	 * Export items as an array.
	 *
	 * @return array<int|string, mixed>
	 */
	public function to_array(): array {
		return $this->items;
	}

	/**
	 * Get all items.
	 *
	 * @return array<int|string, mixed>
	 */
	public function all(): array {
		return $this->to_array();
	}

	/**
	 * Check whether a key exists or whether a collection has any items.
	 *
	 * @param  int|string|null  $key
	 *
	 * @return bool
	 */
	public function has( int|string|null $key = null ): bool {
		if ( null === $key ) {
			return ! $this->is_empty();
		}

		return array_key_exists( $key, $this->items );
	}

	/**
	 * Whether a collection has no items.
	 *
	 * @return bool
	 */
	public function is_empty(): bool {
		return empty( $this->items );
	}

	/**
	 * Search an item by callback or value.
	 *
	 * @param  mixed  $needle
	 * @param  bool  $strict
	 *
	 * @return int|string|null
	 */
	public function search( mixed $needle, bool $strict = true ): int|string|null {
		if ( is_callable( $needle ) ) {
			foreach ( $this->items as $key => $item ) {
				if ( $needle( $item, $key ) ) {
					return $key;
				}
			}

			return null;
		}

		$key = array_search( $needle, $this->items, $strict );

		return false === $key ? null : $key;
	}

	/**
	 * Remove all items.
	 *
	 * @return self
	 */
	public function clear(): self {
		$this->items = [];

		return $this;
	}

	/**
	 * Number of items.
	 *
	 * @return int
	 */
	public function count(): int {
		return count( $this->items );
	}

	/**
	 * Iterator for foreach support.
	 *
	 * @return Traversable<int|string, mixed>
	 */
	public function getIterator(): Traversable {
		return new ArrayIterator( $this->items );
	}

	/**
	 * Validate one item against the configured type.
	 *
	 * @param  mixed  $item
	 *
	 * @return void
	 */
	protected function assert_type( mixed $item ): void {
		if ( null === $this->type ) {
			return;
		}

		if ( ! is_object( $item ) || ! ( $item instanceof $this->type ) ) {
			throw new InvalidArgumentException(
				sprintf( 'Collection expects instances of "%s", got "%s".', $this->type, get_debug_type( $item ) )
			);
		}
	}

	/**
	 * Determine if scalar/null default sorting is safe.
	 *
	 * @param  array<int|string, mixed>  $items
	 *
	 * @return bool
	 */
	protected function can_sort_without_callback( array $items ): bool {
		foreach ( $items as $item ) {
			if ( ! is_scalar( $item ) && null !== $item ) {
				return false;
			}
		}

		return true;
	}
}
