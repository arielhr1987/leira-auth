(function () {
	'use strict';

	var config = window.leiraAuthFrontend || {};
	var ajaxUrl = config.ajaxUrl || window.ajaxurl || '';
	var ajaxAction = config.ajaxAction || 'leira_auth_submit';
	var genericError = config.errorText || 'Unable to submit the form right now.';

	if (!ajaxUrl) {
		return;
	}

	function replaceFormHtml(form, html) {
		if (typeof html !== 'string' || html.trim() === '') {
			return;
		}

		var wrapper = document.createElement('div');
		wrapper.innerHTML = html.trim();
		var nextForm = wrapper.querySelector('form');
		if (!nextForm) {
			return;
		}

		form.replaceWith(nextForm);
	}

	function showGenericError(form, message) {
		var text = typeof message === 'string' && message ? message : genericError;
		var container = form.querySelector('.alert.alert-danger');
		if (!container) {
			container = document.createElement('div');
			container.className = 'alert alert-danger';
			form.prepend(container);
		}
		container.innerHTML = '';
		var row = document.createElement('div');
		row.textContent = text;
		container.appendChild(row);
	}

	document.addEventListener('submit', function (event) {
		var form = event.target;
		if (!(form instanceof HTMLFormElement)) {
			return;
		}

		if (form.getAttribute('data-leira-auth-ajax') !== '1') {
			return;
		}

		event.preventDefault();

		var payload = new FormData(form);
		if (!payload.get('action')) {
			payload.set('action', ajaxAction);
		}

		fetch(ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: payload,
		})
			.then(function (response) {
				return response
					.json()
					.catch(function () {
						return null;
					})
					.then(function (json) {
						return {
							ok: response.ok,
							json: json,
						};
					});
			})
			.then(function (result) {
				var data = result.json && result.json.data ? result.json.data : {};

				if (result.ok && result.json && result.json.success) {
					if (typeof data.redirect === 'string' && data.redirect) {
						window.location.assign(data.redirect);
						return;
					}

					if (typeof data.html === 'string' && data.html) {
						replaceFormHtml(form, data.html);
					}

					return;
				}

				if (typeof data.html === 'string' && data.html) {
					replaceFormHtml(form, data.html);
					return;
				}

				var message = data && data.message ? data.message : genericError;
				showGenericError(form, message);
			})
			.catch(function () {
				showGenericError(form, genericError);
			});
	});
})();
