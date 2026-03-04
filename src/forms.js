/* imports the forms CSS */
import './scss/forms.scss';

/**
 * Production runtime for all Leira Auth AJAX forms.
 * Features:
 * - Single delegated submit listener
 * - Prevents double submissions
 * - Adds loading / aria-busy state
 * - Safe JSON parsing
 * - Supports redirect OR HTML replacement
 * - Graceful network failure handling
 * - Accessible error rendering
 */
(function () {
	'use strict';

	var config = window.leiraAuthFrontend || {};
	var genericError = config.errorText || 'Unable to submit the form right now.';

	/**
	 * Toggle loading state on a form.
	 * @param {HTMLFormElement} form
	 * @param {boolean} state
	 */
	function setLoading(form, state) {
		if (state) {
			form.classList.add('is-submitting');
			form.setAttribute('aria-busy', 'true');

			var buttons = form.querySelectorAll('button, input[type="submit"]');
			buttons.forEach(function (btn) {
				btn.dataset._disabled = btn.disabled ? '1' : '';
				btn.disabled = true;
			});
		} else {
			form.classList.remove('is-submitting');
			form.removeAttribute('aria-busy');

			var buttons = form.querySelectorAll('button, input[type="submit"]');
			buttons.forEach(function (btn) {
				if (btn.dataset._disabled !== '1') {
					btn.disabled = false;
				}
				delete btn.dataset._disabled;
			});
		}
	}

	/**
	 * Replace the current form with new server HTML.
	 * @param {HTMLFormElement} form
	 * @param {string} html
	 */
	function replaceFormHtml(form, html) {
		if (typeof html !== 'string' || !html.trim()) {
			return;
		}
		var wrapper = document.createElement('div');
		wrapper.innerHTML = html.trim();

		var nextForm = wrapper.querySelector('form');
		if (nextForm) {
			form.replaceWith(nextForm);
		}
	}

	/**
	 * Render an accessible error notice inside the form.
	 * @param {HTMLFormElement} form
	 * @param {string} message
	 */
	function showGenericError(form, message) {
		var text = typeof message === 'string' && message ? message : genericError;

		var container = form.querySelector('.alert.alert-danger,[data-leira-error]');
		if (!container) {
			container = document.createElement('div');
			container.className = 'alert alert-danger';
			container.setAttribute('role', 'alert');
			container.setAttribute('data-leira-error', '1');
			form.prepend(container);
		}

		container.textContent = text;
	}

	/**
	 * Parse JSON safely from a fetch response.
	 * @param {Response} response
	 * @returns {Promise<{ok:boolean,json:any}|null>}
	 */
	function parseJsonSafe(response) {
		return response.json()
			.then(function (json) {
				return { ok: response.ok, json: json };
			})
			.catch(function () {
				return null;
			});
	}

	/**
	 * Delegated submit handler for all AJAX-enabled Leira forms.
	 */
	document.addEventListener('submit', function (event) {

		var form = event.target;

		if (!(form instanceof HTMLFormElement)) return;
		if (form.getAttribute('data-leira-auth-ajax') !== '1') return;

		event.preventDefault();

		/* prevent double submit */
		if (form.classList.contains('is-submitting')) {
			return;
		}

		setLoading(form, true);

		var payload = new FormData(form);

		/* Ensure form type is present for server routing. */
		if (!payload.get('_leira_auth_form')) {
			var submittedAction = payload.get('action');
			if (typeof submittedAction === 'string' && submittedAction) {
				payload.set('_leira_auth_form', submittedAction);
			}
		}

		var submitUrl = form.getAttribute('action') || window.location.href;
		submitUrl = submitUrl.split('#')[0];

		fetch(submitUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
				'Accept': 'application/json'
			},
			body: payload,
		})
		.then(parseJsonSafe)
		.then(function (result) {

			setLoading(form, false);

			if (!result || !result.json) {
				showGenericError(form, genericError);
				return;
			}

			var data = result.json && result.json.data ? result.json.data : {};

			/* SUCCESS */
			if (result.ok && result.json.success) {

				if (typeof data.redirect === 'string' && data.redirect) {
					window.location.assign(data.redirect);
					return;
				}

				if (typeof data.html === 'string' && data.html) {
					replaceFormHtml(form, data.html);
				}

				return;
			}

			/* VALIDATION FAILURE WITH HTML SNAPSHOT */
			if (typeof data.html === 'string' && data.html) {
				replaceFormHtml(form, data.html);
				return;
			}

			/* FALLBACK ERROR */
			showGenericError(form, data.message || genericError);
		})
		.catch(function () {
			setLoading(form, false);
			showGenericError(form, genericError);
		});

	});

})();
