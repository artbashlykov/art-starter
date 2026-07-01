(function ($) {
	'use strict';

	var form = document.getElementById('art-starter-not-found-form');
	var previewRoot = document.getElementById('art-starter-not-found-preview');
	var dataEl = document.getElementById('art-starter-not-found-data');
	var extraButtonsRoot = document.getElementById('art-starter-not-found-extra-buttons');
	var addButtonBtn = document.getElementById('art-starter-not-found-add-button');
	var extraButtonTemplate = document.getElementById('art-starter-not-found-extra-button-template');
	var iconPicker = document.getElementById('art-starter-icon-picker');
	var iconPickerGrid = document.getElementById('art-starter-icon-picker-grid');
	var templateSelect = document.getElementById('art-starter-not-found-template');
	var previewShell = document.getElementById('art-starter-not-found-preview-shell');

	if (!form || !previewRoot || !dataEl) {
		return;
	}

	var config = window.artStarterNotFoundAdmin || {};
	var optionName = config.optionName || 'art_starter_not_found';
	var strings = config.strings || {};
	var icons = config.icons || {};
	var iconCategories = config.iconCategories || {};
	var maxExtraButtons = parseInt(config.maxExtraButtons, 10) || 2;
	var iconPickerCategories = config.iconPickerCategories || 'social,action,general';
	var primaryDefaultIcon = config.primaryDefaultIcon || 'home';
	var extraDefaultIcon = config.extraDefaultIcon || 'link';
	var defaultTemplate = config.defaultTemplate || 'light-blue';
	var homeUrl = config.homeUrl || '/';
	var mediaFrame = null;
	var activeIconField = null;

	function safeText(value) {
		return (value || '').toString().trim();
	}

	function normalizeExternalUrl(value) {
		var url = safeText(value);
		if (!url) {
			return '';
		}
		if (/^(https?:\/\/|mailto:|tel:)/i.test(url)) {
			return url;
		}
		return 'https://' + url.replace(/^\/+/, '');
	}

	function getIcon(slug) {
		return icons[safeText(slug)] || null;
	}

	function getEffectiveIconSlug(storedSlug, defaultSlug) {
		var slug = safeText(storedSlug);
		if (slug && getIcon(slug)) {
			return slug;
		}
		defaultSlug = safeText(defaultSlug);
		if (defaultSlug && getIcon(defaultSlug)) {
			return defaultSlug;
		}
		return '';
	}

	function iconMarkup(slug, className) {
		var icon = getIcon(slug);
		if (!icon) {
			return '';
		}
		return '<span class="' + className + '" aria-hidden="true">' + icon.svg + '</span>';
	}

	function letterMarkup(text, className) {
		var letter = safeText(text).slice(0, 1).toUpperCase() || '•';
		return '<span class="' + className + ' art-starter-icon--letter">' + letter + '</span>';
	}

	function renderIconOrLetter(slug, fallbackText, className) {
		if (slug && getIcon(slug)) {
			return iconMarkup(slug, className);
		}
		return letterMarkup(fallbackText, className);
	}

	function setText(selector, value) {
		var el = previewRoot.querySelector(selector);
		if (!el) {
			return;
		}
		el.textContent = value;
		el.style.display = value ? '' : 'none';
	}

	function triggerPreviewUpdate() {
		form.dispatchEvent(new Event('input', { bubbles: true }));
	}

	function resolveButtonUrl(url, isPrimary) {
		var normalized = normalizeExternalUrl(url);
		if (!normalized && isPrimary) {
			return homeUrl;
		}
		return normalized;
	}

	function readState() {
		var imageInput = document.getElementById('art-starter-not-found-image');
		var codeInput = document.getElementById('art-starter-not-found-code');
		var titleInput = document.getElementById('art-starter-not-found-title');

		return {
			template: templateSelect ? safeText(templateSelect.value) || defaultTemplate : defaultTemplate,
			image_url: imageInput ? safeText(imageInput.value) : '',
			code: codeInput ? safeText(codeInput.value) : '',
			title: titleInput ? safeText(titleInput.value) : '',
			buttons: [readPrimaryButton()].concat(readExtraButtons())
		};
	}

	function readPrimaryButton() {
		var labelInput = document.getElementById('art-starter-not-found-primary-label');
		var urlInput = document.getElementById('art-starter-not-found-primary-url');
		var iconInput = document.getElementById('art-starter-not-found-primary-icon');

		return {
			label: labelInput ? safeText(labelInput.value) : '',
			url: urlInput ? safeText(urlInput.value) : '',
			icon: iconInput ? safeText(iconInput.value) : ''
		};
	}
	function readExtraButtons() {
		var rows = extraButtonsRoot ? extraButtonsRoot.querySelectorAll('[data-not-found-extra-button]') : [];
		var items = [];

		rows.forEach(function (row) {
			var labelInput = row.querySelector('[data-art-starter-button-label]');
			var urlInput = row.querySelector('[data-art-starter-button-url]');
			var iconInput = row.querySelector('.art-starter-icon-field__input');

			items.push({
				label: labelInput ? safeText(labelInput.value) : '',
				url: urlInput ? safeText(urlInput.value) : '',
				icon: iconInput ? safeText(iconInput.value) : ''
			});
		});

		return items;
	}

	function renderButtons(buttons) {
		var root = previewRoot.querySelector('[data-bind="buttons"]');
		if (!root) {
			return;
		}

		root.innerHTML = '';

		(buttons || []).forEach(function (button, index) {
			var label = safeText(button.label);
			var url = resolveButtonUrl(button.url, index === 0);
			var iconSlug = getEffectiveIconSlug(button.icon, index === 0 ? primaryDefaultIcon : extraDefaultIcon);

			if (index > 0 && !label && !safeText(button.url)) {
				return;
			}

			if (index === 0 && !label) {
				label = strings.primaryButtonDefault || 'Вернуться на главную';
			}

			if (!label || !url) {
				return;
			}

			var el = document.createElement('a');
			el.className = 'art-starter-not-found-button ' + (index === 0 ? 'art-starter-not-found-button--primary' : 'art-starter-not-found-button--secondary');
			el.href = url;
			el.innerHTML =
				'<span class="art-starter-not-found-button__icon">' +
					renderIconOrLetter(iconSlug, label, 'art-starter-not-found-button__icon-svg') +
				'</span>' +
				'<span class="art-starter-not-found-button__label">' + label + '</span>';
			root.appendChild(el);
		});
	}

	function renderImage(url) {
		var wrap = previewRoot.querySelector('[data-bind="image"]');
		if (!wrap) {
			return;
		}

		url = safeText(url);
		if (!url) {
			wrap.hidden = true;
			wrap.innerHTML = '';
			return;
		}

		wrap.hidden = false;
		wrap.innerHTML = '<img src="' + url.replace(/"/g, '&quot;') + '" alt="" decoding="async">';
	}

	function render(state) {
		setText('[data-bind="code"]', safeText(state.code) || '404');
		setText('[data-bind="title"]', safeText(state.title));
		renderImage(state.image_url);
		renderButtons(state.buttons);
		applyPreviewTemplate(state.template);
		syncPreviewFrameScale();
	}

	function syncPreviewFrameScale() {
		var scale = previewRoot && previewRoot.querySelector('.art-starter-not-found-preview__scale');
		var card = scale && scale.querySelector('.art-starter-not-found-card');

		if (!scale || !card) {
			return;
		}

		if (typeof CSS !== 'undefined' && CSS.supports && CSS.supports('zoom', '1')) {
			scale.style.marginBottom = '';
			return;
		}

		var factor = 0.6666667;
		var fullHeight = card.offsetHeight;

		if (fullHeight > 0) {
			scale.style.marginBottom = (-fullHeight * (1 - factor)) + 'px';
		}
	}

	function applyPreviewTemplate(template) {
		template = safeText(template) || defaultTemplate;
		var bodyClass = 'art-starter-not-found-shell art-starter-not-found art-starter-not-found--' + template;

		if (previewShell) {
			previewShell.className = bodyClass;
		}

		if (previewRoot) {
			previewRoot.className = 'art-starter-not-found-preview__frame';
			if (template !== 'classic') {
				previewRoot.classList.add('art-starter-not-found-preview__frame--' + template);
			}
		}
	}

	function updateMediaFieldUI($field) {
		var $input = $field.find('.art-starter-media-field__input');
		var $preview = $field.find('.art-starter-media-field__preview');
		var $img = $preview.find('img');
		var $select = $field.find('.art-starter-media-field__select');
		var $remove = $field.find('.art-starter-media-field__remove');
		var url = safeText($input.val());

		if (url) {
			$preview.prop('hidden', false);
			$img.attr('src', url);
			$select.text(strings.changeImage || 'Заменить изображение');
			$remove.prop('disabled', false);
		} else {
			$preview.prop('hidden', true);
			$img.attr('src', '');
			$select.text(strings.selectImage || 'Выбрать изображение');
			$remove.prop('disabled', true);
		}
	}

	function updateIconFieldUI($field) {
		var slug = safeText($field.find('.art-starter-icon-field__input').val());
		var defaultSlug = safeText($field.data('icon-default'));
		var effectiveSlug = getEffectiveIconSlug(slug, defaultSlug);
		var icon = effectiveSlug ? getIcon(effectiveSlug) : null;
		var $preview = $field.find('.art-starter-icon-field__preview');

		if (icon) {
			$preview.html(iconMarkup(effectiveSlug, 'art-starter-icon-field__icon'));
		} else {
			$preview.html('<span class="art-starter-icon-field__placeholder">' + (strings.noIconShort || 'Без') + '</span>');
		}

		var $reset = $field.find('.art-starter-icon-field__reset');
		$reset.prop('disabled', slug === defaultSlug || (!slug && defaultSlug !== ''));
	}

	function buildIconPickerGrid(categories) {
		if (!iconPickerGrid) {
			return;
		}

		iconPickerGrid.innerHTML = '';
		categories = safeText(categories) || iconPickerCategories;
		categories.split(',').forEach(function (category) {
			category = safeText(category);
			if (!category) {
				return;
			}

			var heading = document.createElement('h3');
			heading.className = 'art-starter-icon-picker__category';
			heading.textContent = iconCategories[category] || category;
			iconPickerGrid.appendChild(heading);

			var grid = document.createElement('div');
			grid.className = 'art-starter-icon-picker__grid';

			Object.keys(icons).forEach(function (slug) {
				var icon = icons[slug];
				if (!icon || icon.category !== category) {
					return;
				}

				var button = document.createElement('button');
				button.type = 'button';
				button.className = 'art-starter-icon-picker__item';
				button.setAttribute('data-icon-slug', slug);
				button.innerHTML =
					'<span class="art-starter-icon-picker__item-icon">' + icon.svg + '</span>' +
					'<span class="art-starter-icon-picker__item-label">' + icon.label + '</span>';
				grid.appendChild(button);
			});

			iconPickerGrid.appendChild(grid);
		});
	}

	function openIconPicker($field) {
		if (!iconPicker) {
			return;
		}

		activeIconField = $field;
		buildIconPickerGrid($field.data('icon-categories'));
		iconPicker.hidden = false;
		document.body.classList.add('art-starter-icon-picker-open');
	}

	function closeIconPicker() {
		if (!iconPicker) {
			return;
		}

		iconPicker.hidden = true;
		document.body.classList.remove('art-starter-icon-picker-open');
		activeIconField = null;
	}

	function extraButtonFieldName(index, field) {
		return optionName + '[buttons][' + index + '][' + field + ']';
	}

	function reindexExtraButtons() {
		if (!extraButtonsRoot) {
			return;
		}

		var rows = extraButtonsRoot.querySelectorAll('[data-not-found-extra-button]');
		rows.forEach(function (row, index) {
			var buttonIndex = index + 1;
			var iconInput = row.querySelector('.art-starter-icon-field__input');
			var labelInput = row.querySelector('[data-art-starter-button-label]');
			var urlInput = row.querySelector('[data-art-starter-button-url]');

			if (iconInput) {
				iconInput.name = extraButtonFieldName(buttonIndex, 'icon');
				iconInput.id = 'art-starter-not-found-extra-icon-' + buttonIndex;
			}
			if (labelInput) {
				labelInput.name = extraButtonFieldName(buttonIndex, 'label');
			}
			if (urlInput) {
				urlInput.name = extraButtonFieldName(buttonIndex, 'url');
			}
		});
	}

	function getExtraButtonCount() {
		return extraButtonsRoot ? extraButtonsRoot.querySelectorAll('[data-not-found-extra-button]').length : 0;
	}

	function updateAddButtonState() {
		if (!addButtonBtn) {
			return;
		}

		addButtonBtn.disabled = getExtraButtonCount() >= maxExtraButtons;
	}

	function createExtraButtonRow() {
		if (!extraButtonTemplate || !extraButtonsRoot) {
			return null;
		}

		var row;
		if (extraButtonTemplate.content && extraButtonTemplate.content.firstElementChild) {
			row = extraButtonTemplate.content.firstElementChild.cloneNode(true);
		} else {
			var wrapper = document.createElement('div');
			wrapper.innerHTML = extraButtonTemplate.innerHTML.trim();
			row = wrapper.firstElementChild;
		}

		if (!row) {
			return null;
		}

		var iconInput = row.querySelector('.art-starter-icon-field__input');
		var labelInput = row.querySelector('[data-art-starter-button-label]');
		var urlInput = row.querySelector('[data-art-starter-button-url]');

		if (iconInput) {
			iconInput.value = extraDefaultIcon;
		}
		if (labelInput) {
			labelInput.value = '';
		}
		if (urlInput) {
			urlInput.value = '';
		}

		extraButtonsRoot.appendChild(row);
		reindexExtraButtons();
		updateAddButtonState();
		return row;
	}

	try {
		var initial = JSON.parse(dataEl.textContent || '{}');
		render(initial);
	} catch (e) {
		render(readState());
	}

	applyPreviewTemplate(templateSelect ? templateSelect.value : defaultTemplate);

	form.querySelectorAll('[data-art-starter-icon-field]').forEach(function (field) {
		updateIconFieldUI($(field));
	});

	form.querySelectorAll('[data-art-starter-media-field]').forEach(function (field) {
		updateMediaFieldUI($(field));
	});

	updateAddButtonState();

	form.addEventListener('submit', function () {
		reindexExtraButtons();
	});

	form.addEventListener(
		'input',
		function () {
			render(readState());
		},
		{ passive: true }
	);

	if (templateSelect) {
		templateSelect.addEventListener('change', function () {
			applyPreviewTemplate(templateSelect.value);
			syncPreviewFrameScale();
		});
	}

	if (addButtonBtn && extraButtonsRoot) {
		addButtonBtn.addEventListener('click', function () {
			if (getExtraButtonCount() >= maxExtraButtons) {
				return;
			}

			var row = createExtraButtonRow();
			if (!row) {
				return;
			}

			updateIconFieldUI($(row).find('[data-art-starter-icon-field]'));
			var firstInput = row.querySelector('input[type="text"]');
			if (firstInput) {
				firstInput.focus();
			}
			triggerPreviewUpdate();
		});
	}

	$(document).on('click', '.art-starter-not-found-button-row__remove', function (event) {
		event.preventDefault();
		var row = $(this).closest('[data-not-found-extra-button]');
		row.remove();
		reindexExtraButtons();
		updateAddButtonState();
		triggerPreviewUpdate();
	});

	$(document).on('click', '.art-starter-icon-field__toggle', function (event) {
		event.preventDefault();
		openIconPicker($(this).closest('[data-art-starter-icon-field]'));
	});

	$(document).on('click', '.art-starter-icon-field__reset', function (event) {
		event.preventDefault();
		var $field = $(this).closest('[data-art-starter-icon-field]');
		var defaultSlug = safeText($field.data('icon-default'));
		$field.find('.art-starter-icon-field__input').val(defaultSlug);
		updateIconFieldUI($field);
		triggerPreviewUpdate();
	});

	$(document).on('click', '[data-art-starter-icon-picker-close]', function (event) {
		event.preventDefault();
		closeIconPicker();
	});

	$(document).on('click', '.art-starter-icon-picker__item', function (event) {
		event.preventDefault();
		if (!activeIconField) {
			return;
		}

		var slug = safeText($(this).attr('data-icon-slug'));
		activeIconField.find('.art-starter-icon-field__input').val(slug);
		updateIconFieldUI(activeIconField);
		closeIconPicker();
		triggerPreviewUpdate();
	});

	$(document).on('click', '.art-starter-media-field__select', function (event) {
		event.preventDefault();

		if (!window.wp || !window.wp.media) {
			return;
		}

		var $field = $(this).closest('[data-art-starter-media-field]');
		var $input = $field.find('.art-starter-media-field__input');

		if (mediaFrame) {
			mediaFrame.off('select');
		}

		mediaFrame = window.wp.media({
			title: strings.selectImage || 'Выбрать изображение',
			button: { text: strings.selectImage || 'Выбрать изображение' },
			multiple: false,
			library: { type: 'image' }
		});

		mediaFrame.on('select', function () {
			var attachment = mediaFrame.state().get('selection').first().toJSON();
			var url = attachment.url || '';

			$input.val(url).trigger('change');
			updateMediaFieldUI($field);
			triggerPreviewUpdate();
		});

		mediaFrame.open();
	});

	$(document).on('click', '.art-starter-media-field__remove', function (event) {
		event.preventDefault();

		var $field = $(this).closest('[data-art-starter-media-field]');
		var $input = $field.find('.art-starter-media-field__input');

		$input.val('').trigger('change');
		updateMediaFieldUI($field);
		triggerPreviewUpdate();
	});

	$(document).on('change', '.art-starter-media-field__input', function () {
		var $field = $(this).closest('[data-art-starter-media-field]');
		updateMediaFieldUI($field);
		triggerPreviewUpdate();
	});
})(jQuery);
