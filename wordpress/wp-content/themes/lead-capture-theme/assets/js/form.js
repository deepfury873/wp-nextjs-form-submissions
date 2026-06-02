(function () {
	'use strict';

	var config = window.leadCaptureConfig || {};
	var form = document.getElementById('lead-capture-form');
	if (!form) return;

	var statusEl = document.getElementById('lc-form-status');
	var submitBtn = document.getElementById('submit-btn');
	var i18n = config.i18n || {};

	var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	var phonePattern = /^[\d\s\-+().]{6,50}$/;

	function showError(fieldId, message) {
		var input = document.getElementById(fieldId);
		var errorEl = document.getElementById(fieldId + '-error');
		if (input) {
			input.classList.add('lc-field__input--invalid');
			input.setAttribute('aria-invalid', 'true');
		}
		if (errorEl) {
			errorEl.textContent = message;
			errorEl.hidden = false;
		}
	}

	function clearError(fieldId) {
		var input = document.getElementById(fieldId);
		var errorEl = document.getElementById(fieldId + '-error');
		if (input) {
			input.classList.remove('lc-field__input--invalid');
			input.removeAttribute('aria-invalid');
		}
		if (errorEl) {
			errorEl.textContent = '';
			errorEl.hidden = true;
		}
	}

	function clearAllErrors() {
		['first_name', 'last_name', 'email', 'phone', 'date_of_birth', 'consent'].forEach(clearError);
	}

	function validate() {
		clearAllErrors();
		var valid = true;

		var firstName = form.first_name.value.trim();
		if (!firstName) {
			showError('first_name', i18n.required || 'Required');
			valid = false;
		}

		var lastName = form.last_name.value.trim();
		if (!lastName) {
			showError('last_name', i18n.required || 'Required');
			valid = false;
		}

		var email = form.email.value.trim();
		if (!email || !emailPattern.test(email)) {
			showError('email', i18n.invalidEmail || 'Invalid email');
			valid = false;
		}

		var phone = form.phone.value.trim();
		if (phone && !phonePattern.test(phone)) {
			showError('phone', i18n.invalidPhone || 'Invalid phone');
			valid = false;
		}

		var dob = form.date_of_birth.value;
		if (dob) {
			var parsed = new Date(dob + 'T00:00:00');
			if (isNaN(parsed.getTime())) {
				showError('date_of_birth', i18n.invalidDate || 'Invalid date');
				valid = false;
			}
		}

		if (!form.consent.checked) {
			showError('consent', i18n.consentRequired || 'Consent required');
			valid = false;
		}

		return valid;
	}

	function setStatus(message, type) {
		statusEl.textContent = message;
		statusEl.className = 'lc-form__status' + (type ? ' lc-form__status--' + type : '');
	}

	form.addEventListener('submit', function (event) {
		event.preventDefault();
		setStatus('');

		if (!validate()) {
			setStatus(i18n.genericError || 'Please fix the errors above.', 'error');
			return;
		}

		submitBtn.disabled = true;

		var payload = {
			first_name: form.first_name.value.trim(),
			last_name: form.last_name.value.trim(),
			email: form.email.value.trim(),
			phone: form.phone.value.trim() || '',
			country: form.country.value || '',
			date_of_birth: form.date_of_birth.value || '',
			consent: form.consent.checked,
			source: 'wordpress',
		};

		fetch(config.restUrl, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': config.nonce,
			},
			body: JSON.stringify(payload),
			credentials: 'same-origin',
		})
			.then(function (response) {
				return response.json().then(function (data) {
					return { ok: response.ok, data: data };
				});
			})
			.then(function (result) {
				if (result.ok && result.data.success) {
					setStatus(result.data.message || i18n.success, 'success');
					form.reset();
					return;
				}

				if (result.data && result.data.errors) {
					Object.keys(result.data.errors).forEach(function (key) {
						showError(key, result.data.errors[key]);
					});
				}
				setStatus(
					(result.data && result.data.message) || i18n.genericError,
					'error'
				);
			})
			.catch(function () {
				setStatus(i18n.genericError, 'error');
			})
			.finally(function () {
				submitBtn.disabled = false;
			});
	});

	['first_name', 'last_name', 'email', 'phone', 'date_of_birth'].forEach(function (id) {
		var el = document.getElementById(id);
		if (el) {
			el.addEventListener('input', function () {
				clearError(id);
			});
		}
	});

	form.consent.addEventListener('change', function () {
		clearError('consent');
	});
})();
