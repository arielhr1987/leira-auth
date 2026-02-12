<?php

namespace Leira_Auth\Public\Contracts;

/**
 * Interface that explains a stateful field
 *
 * @since 1.0.0
 */
interface Stateful{

	/**
	 * Get the current object state.
	 *
	 * @return array
	 */
	public function state(): array;

	/**
	 * Restore a previous object state.
	 *
	 * @param  array  $state  The state to restore
	 *
	 * @return void
	 */
	public function restore( array $state ): void;
}
