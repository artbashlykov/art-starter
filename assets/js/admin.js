(function () {
	'use strict';

	var config = window.artStarterAdmin || {};
	var strings = config.strings || {};
	var copyResetTimer = null;

	function selectInput(input) {
		if (!input || typeof input.select !== 'function') {
			return;
		}

		input.focus();
		input.select();
	}

	function copyText(text) {
		if (!text) {
			return Promise.reject(new Error('empty'));
		}

		if (navigator.clipboard && window.isSecureContext) {
			return navigator.clipboard.writeText(text);
		}

		return new Promise(function (resolve, reject) {
			var textarea = document.createElement('textarea');
			textarea.value = text;
			textarea.setAttribute('readonly', '');
			textarea.style.position = 'absolute';
			textarea.style.left = '-9999px';
			document.body.appendChild(textarea);
			textarea.select();

			try {
				if (document.execCommand('copy')) {
					resolve();
				} else {
					reject(new Error('copy failed'));
				}
			} catch (error) {
				reject(error);
			} finally {
				document.body.removeChild(textarea);
			}
		});
	}

	function resetCopyButton(button) {
		if (!button) {
			return;
		}

		button.classList.remove('is-copied');
		button.setAttribute('title', strings.copy || 'Скопировать');
		button.setAttribute('aria-label', strings.copyLink || strings.copy || 'Скопировать');
	}

	function markCopyButtonCopied(button) {
		if (!button) {
			return;
		}

		if (copyResetTimer) {
			window.clearTimeout(copyResetTimer);
		}

		button.classList.add('is-copied');
		button.setAttribute('title', strings.copied || 'Скопировано');
		button.setAttribute('aria-label', strings.copied || 'Скопировано');

		copyResetTimer = window.setTimeout(function () {
			resetCopyButton(button);
			copyResetTimer = null;
		}, 1600);
	}

	function getCopyValue(button) {
		var directValue = button.getAttribute('data-copy-value');

		if (directValue) {
			return directValue;
		}

		var targetSelector = button.getAttribute('data-copy-target');

		if (!targetSelector) {
			return '';
		}

		var target = document.querySelector(targetSelector);

		if (!target) {
			return '';
		}

		if ('INPUT' === target.tagName || 'TEXTAREA' === target.tagName) {
			return target.value || '';
		}

		return (target.textContent || '').trim();
	}

	document.addEventListener(
		'focus',
		function (event) {
			if (event.target && event.target.matches('.art-starter-copy-url-field__input')) {
				selectInput(event.target);
			}
		},
		true
	);

	document.addEventListener('click', function (event) {
		var input = event.target;

		if (input && input.matches('.art-starter-copy-url-field__input')) {
			selectInput(input);
		}

		var button = event.target.closest('.art-starter-copy-url-field__copy');

		if (!button) {
			return;
		}

		event.preventDefault();

		var text = getCopyValue(button);

		copyText(text)
			.then(function () {
				markCopyButtonCopied(button);
			})
			.catch(function () {
				window.alert(strings.copyFailed || 'Не удалось скопировать.');
			});
	});
})();
