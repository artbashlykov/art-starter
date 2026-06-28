(function ($) {
	'use strict';

	var form = document.getElementById('art-starter-homepage-form');
	var previewRoot = document.getElementById('art-starter-homepage-preview');
	var dataEl = document.getElementById('art-starter-homepage-data');
	var linksRoot = document.getElementById('art-starter-homepage-links');
	var addLinkBtn = document.getElementById('art-starter-homepage-add-link');
	var socialsRoot = document.getElementById('art-starter-homepage-socials');
	var addSocialBtn = document.getElementById('art-starter-homepage-add-social');
	var iconPicker = document.getElementById('art-starter-icon-picker');
	var iconPickerGrid = document.getElementById('art-starter-icon-picker-grid');
	var templateSelect = document.getElementById('art-starter-homepage-template');
	var previewShell = document.getElementById('art-starter-homepage-preview-shell');

	if (!form || !previewRoot || !dataEl) {
		return;
	}

	var config = window.artStarterHomepageAdmin || {};
	var optionName = config.optionName || 'art_starter_homepage';
	var strings = config.strings || {};
	var icons = config.icons || {};
	var iconCategories = config.iconCategories || {};
	var socialNetworks = config.socialNetworks || {};
	var maxSocials = parseInt(config.maxSocials, 10) || 5;
	var iconPickerCategories = config.iconPickerCategories || 'social,action,general';
	var linkDefaultIcon = config.linkDefaultIcon || 'link';
	var defaultTemplate = config.defaultTemplate || 'light-blue';
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

	function getSocialLabel(network) {
		return socialNetworks[safeText(network)] || '';
	}

	function getSocialUrlPlaceholder(network) {
		if (safeText(network) === 'mail') {
			return strings.socialEmailPlaceholder || 'name@example.com';
		}
		return strings.linkUrlPlaceholder || 'example.com или https://...';
	}

	function updateSocialRowUI(row) {
		if (!row) {
			return;
		}

		var networkEl = row.querySelector('.art-starter-social-item-row__network');
		var urlEl = row.querySelector('.art-starter-social-item-row__url');

		if (!networkEl || !urlEl) {
			return;
		}

		urlEl.placeholder = getSocialUrlPlaceholder(safeText(networkEl.value));
	}

	function updateAddSocialButton() {
		if (!addSocialBtn || !socialsRoot) {
			return;
		}

		var count = socialsRoot.querySelectorAll('.art-starter-social-item-row').length;
		addSocialBtn.disabled = count >= maxSocials;
	}

	function socialRowFieldName(index, field) {
		return optionName + '[socials][' + index + '][' + field + ']';
	}

	function collectSocialsFromRows() {
		var socials = [];

		if (!socialsRoot) {
			return socials;
		}

		socialsRoot.querySelectorAll('.art-starter-social-item-row').forEach(function (row) {
			var networkEl = row.querySelector('.art-starter-social-item-row__network');
			var urlEl = row.querySelector('.art-starter-social-item-row__url');
			var network = networkEl ? safeText(networkEl.value) : '';
			var url = urlEl ? readSocialUrl(network, urlEl.value) : '';

			if (!network || !url) {
				return;
			}

			socials.push({
				network: network,
				label: getSocialLabel(network),
				url: url
			});
		});

		return socials;
	}

	function reindexSocialRows() {
		if (!socialsRoot) {
			return;
		}

		socialsRoot.querySelectorAll('.art-starter-social-item-row').forEach(function (row, index) {
			var networkEl = row.querySelector('.art-starter-social-item-row__network');
			var urlEl = row.querySelector('.art-starter-social-item-row__url');

			if (networkEl) {
				networkEl.name = socialRowFieldName(index, 'network');
			}
			if (urlEl) {
				urlEl.name = socialRowFieldName(index, 'url');
			}
		});
	}

	function syncSocialsPayload() {
		var payload = document.getElementById('art-starter-socials-payload');
		if (!payload) {
			return;
		}

		payload.value = JSON.stringify(collectSocialsFromRows());
	}

	function buildSortButtonsHTML() {
		return (
			'<div class="art-starter-sortable-row__move">' +
				'<button type="button" class="button art-starter-sortable-row__up" aria-label="' + (strings.moveUp || 'Выше') + '">&#8593;</button>' +
				'<button type="button" class="button art-starter-sortable-row__down" aria-label="' + (strings.moveDown || 'Ниже') + '">&#8595;</button>' +
			'</div>'
		);
	}

	function moveRow(row, direction) {
		if (!row || !row.parentNode) {
			return;
		}

		if (direction < 0) {
			var prev = row.previousElementSibling;
			if (prev) {
				row.parentNode.insertBefore(row, prev);
			}
			return;
		}

		var next = row.nextElementSibling;
		if (next) {
			row.parentNode.insertBefore(next, row);
		}
	}

	function readSocialUrl(network, value) {
		var url = safeText(value);
		if (!url) {
			return '';
		}
		if (safeText(network) === 'mail') {
			if (/^mailto:/i.test(url)) {
				return url;
			}
			return 'mailto:' + url.replace(/^mailto:/i, '');
		}
		return normalizeExternalUrl(url);
	}

	function getLinkIconSlug(storedSlug) {
		return getEffectiveIconSlug(storedSlug, linkDefaultIcon);
	}

	function isIconAtDefault(storedSlug, defaultSlug) {
		var stored = safeText(storedSlug);
		defaultSlug = safeText(defaultSlug);
		return stored === defaultSlug || (stored === '' && defaultSlug !== '');
	}

	function updateIconFieldUI($field) {
		var $input = $field.find('.art-starter-icon-field__input');
		var $preview = $field.find('.art-starter-icon-field__preview');
		var $reset = $field.find('.art-starter-icon-field__reset');
		var storedSlug = safeText($input.val());
		var defaultSlug = safeText($field.data('icon-default'));
		var displaySlug = getEffectiveIconSlug(storedSlug, defaultSlug);
		var icon = displaySlug ? getIcon(displaySlug) : null;

		if (icon) {
			$preview.html(iconMarkup(displaySlug, 'art-starter-icon-field__icon'));
		} else {
			$preview.html('<span class="art-starter-icon-field__placeholder">' + (strings.noIconShort || 'Без') + '</span>');
		}

		$reset.prop('disabled', isIconAtDefault(storedSlug, defaultSlug));
	}

	function buildIconFieldHTML(inputName, categories, defaultSlug) {
		defaultSlug = safeText(defaultSlug);
		var previewHtml = defaultSlug && getIcon(defaultSlug)
			? iconMarkup(defaultSlug, 'art-starter-icon-field__icon')
			: '<span class="art-starter-icon-field__placeholder">' + (strings.noIconShort || 'Без') + '</span>';

		return (
			'<div class="art-starter-icon-field" data-art-starter-icon-field data-icon-categories="' + categories + '" data-icon-allow-none="1" data-icon-default="' + defaultSlug + '">' +
				'<input type="hidden" class="art-starter-icon-field__input" name="' + inputName + '" value="' + defaultSlug + '">' +
				'<div class="art-starter-icon-field__controls">' +
					'<button type="button" class="art-starter-icon-field__preview-btn art-starter-icon-field__toggle" title="' + (strings.selectIcon || 'Выбрать иконку') + '">' +
						'<span class="art-starter-icon-field__preview">' + previewHtml + '</span>' +
					'</button>' +
					'<button type="button" class="button art-starter-icon-field__toggle">' + (strings.selectIcon || 'Выбрать иконку') + '</button>' +
					'<button type="button" class="button-link art-starter-icon-field__reset" disabled>' + (strings.resetIcon || 'Сбросить') + '</button>' +
				'</div>' +
			'</div>'
		);
	}

	function renderAvatar(url, name) {
		var wrap = previewRoot.querySelector('[data-bind="avatar"]');
		if (!wrap) {
			return;
		}

		var existing = wrap.querySelector('img');
		var placeholder = wrap.querySelector('.art-starter-homepage-avatar__placeholder');

		if (url) {
			if (!existing) {
				existing = document.createElement('img');
				existing.alt = '';
				existing.decoding = 'async';
				existing.loading = 'lazy';
				wrap.insertBefore(existing, wrap.firstChild);
			}
			existing.src = url;
			if (placeholder) {
				placeholder.style.display = 'none';
			}
		} else {
			if (existing && existing.parentNode) {
				existing.parentNode.removeChild(existing);
			}
			if (placeholder) {
				placeholder.textContent = safeText(name).slice(0, 1).toUpperCase() || 'A';
				placeholder.style.display = '';
			}
		}
	}

	function renderRecommendImage(url) {
		var wrap = previewRoot.querySelector('[data-bind="recommend-image"]');
		if (!wrap) {
			return;
		}

		if (!url) {
			wrap.innerHTML = '';
			wrap.hidden = true;
			return;
		}

		wrap.hidden = false;
		wrap.innerHTML = '<img src="' + url.replace(/"/g, '&quot;') + '" alt="" decoding="async" loading="lazy">';
	}

	function renderCtaIcon(slug, label) {
		var wrap = previewRoot.querySelector('[data-bind="cta-icon"]');
		if (!wrap) {
			return;
		}

		var effectiveSlug = getEffectiveIconSlug(slug, '');
		if (!effectiveSlug) {
			wrap.innerHTML = '';
			wrap.hidden = true;
			return;
		}

		wrap.innerHTML = iconMarkup(effectiveSlug, 'art-starter-homepage-cta__icon-svg');
		wrap.hidden = false;
	}

	function isBlockHidden(blockKey) {
		var el = form.querySelector('[data-art-starter-block-visibility="' + blockKey + '"]');
		return el ? el.checked : false;
	}

	function isSocialLabelsEnabled() {
		var el = form.querySelector('[data-art-starter-social-labels-toggle]');
		return !!(el && el.checked);
	}

	function readBlocksState() {
		return {
			profile: { hidden: isBlockHidden('profile') },
			cta: { hidden: isBlockHidden('cta') },
			links: { hidden: isBlockHidden('links') },
			recommend: { hidden: isBlockHidden('recommend') },
			socials: {
				hidden: isBlockHidden('socials'),
				show_labels: isSocialLabelsEnabled()
			}
		};
	}

	function applyBlockVisibility(blocks) {
		var selectors = {
			profile: '.art-starter-homepage-profile',
			cta: '[data-bind="cta"]',
			links: '[data-bind="links"]',
			recommend: '[data-bind="recommend"]',
			socials: '[data-bind="social"]'
		};

		Object.keys(selectors).forEach(function (blockKey) {
			var el = previewRoot.querySelector(selectors[blockKey]);
			if (!el) {
				return;
			}

			var hidden = blocks && blocks[blockKey] && blocks[blockKey].hidden;

			if (hidden) {
				el.style.display = 'none';
				return;
			}

			if (blockKey === 'profile' || blockKey === 'cta') {
				el.style.display = '';
			}
		});
	}

	function readState() {
		function v(name) {
			var el = form.querySelector('[name="' + name + '"]');
			return el ? safeText(el.value) : '';
		}

		var state = {
			profile: {
				avatar_url: v(optionName + '[profile][avatar_url]'),
				name: v(optionName + '[profile][name]'),
				roles: v(optionName + '[profile][roles]'),
				bio: v(optionName + '[profile][bio]')
			},
			cta: {
				label: v(optionName + '[cta][label]'),
				url: normalizeExternalUrl(v(optionName + '[cta][url]')),
				icon: v(optionName + '[cta][icon]')
			},
			links: [],
			recommend: {
				badge: v(optionName + '[recommend][badge]'),
				title: v(optionName + '[recommend][title]'),
				description: v(optionName + '[recommend][description]'),
				button_label: v(optionName + '[recommend][button_label]'),
				button_url: normalizeExternalUrl(v(optionName + '[recommend][button_url]')),
				image_url: v(optionName + '[recommend][image_url]')
			},
			socials: []
		};

		state.blocks = readBlocksState();

		var rows = form.querySelectorAll('.art-starter-link-row');
		rows.forEach(function (row) {
			var labelEl = row.querySelector('[data-art-starter-link-label]');
			var urlEl = row.querySelector('[data-art-starter-link-url]');
			var iconEl = row.querySelector('.art-starter-icon-field__input');

			state.links.push({
				label: labelEl ? safeText(labelEl.value) : '',
				url: urlEl ? normalizeExternalUrl(urlEl.value) : '',
				icon: iconEl ? safeText(iconEl.value) : ''
			});
		});

		var socialRows = form.querySelectorAll('.art-starter-social-item-row');
		socialRows.forEach(function (row) {
			var networkEl = row.querySelector('.art-starter-social-item-row__network');
			var urlEl = row.querySelector('.art-starter-social-item-row__url');
			var network = networkEl ? safeText(networkEl.value) : '';
			var url = urlEl ? readSocialUrl(network, urlEl.value) : '';

			if (!network || !url) {
				return;
			}

			state.socials.push({
				network: network,
				label: getSocialLabel(network),
				url: url
			});
		});

		return state;
	}

	function renderLinks(links) {
		var root = previewRoot.querySelector('[data-bind="links"]');
		if (!root) {
			return;
		}

		root.innerHTML = '';
		var visible = 0;

		(links || []).forEach(function (item) {
			var label = safeText(item && item.label);
			var url = safeText(item && item.url);
			var icon = safeText(item && item.icon);
			if (!label && !url) {
				return;
			}

			visible += 1;

			var row = document.createElement('div');
			row.className = 'art-starter-homepage-link';

			var left = document.createElement('div');
			left.className = 'art-starter-homepage-link__left';

			var iconWrap = document.createElement('span');
			iconWrap.className = 'art-starter-homepage-link__icon';
			iconWrap.innerHTML = iconMarkup(getLinkIconSlug(icon), 'art-starter-homepage-link__icon-svg');

			var text = document.createElement('span');
			text.className = 'art-starter-homepage-link__text';
			text.textContent = label || url;

			var arrow = document.createElement('span');
			arrow.innerHTML = linkArrowMarkup();
			var arrowEl = arrow.firstElementChild || arrow;

			left.appendChild(iconWrap);
			left.appendChild(text);
			row.appendChild(left);
			row.appendChild(arrowEl);
			root.appendChild(row);
		});

		root.style.display = visible ? '' : 'none';
	}

	function renderSocial(socials, showLabels) {
		var root = previewRoot.querySelector('[data-bind="social"]');
		if (!root) {
			return;
		}

		root.innerHTML = '';
		var visible = 0;

		(socials || []).forEach(function (item) {
			var network = safeText(item && item.network);
			var url = safeText(item && item.url);
			var label = getSocialLabel(network);

			if (!network || !url) {
				return;
			}

			visible += 1;

			var node = document.createElement('div');

			if (showLabels && label) {
				node.className = 'art-starter-homepage-social__item art-starter-homepage-social__item--labeled';

				var iconWrap = document.createElement('span');
				iconWrap.className = 'art-starter-homepage-social__icon-wrap';
				iconWrap.innerHTML = renderIconOrLetter(network, label, 'art-starter-homepage-social__icon-svg');

				var labelEl = document.createElement('span');
				labelEl.className = 'art-starter-homepage-social__label';
				labelEl.textContent = label;

				node.appendChild(iconWrap);
				node.appendChild(labelEl);
			} else {
				node.className = 'art-starter-homepage-social__item';
				node.innerHTML = renderIconOrLetter(network, label, 'art-starter-homepage-social__icon-svg');
			}

			root.appendChild(node);
		});

		root.style.display = visible ? '' : 'none';
	}

	function render(state) {
		setText('[data-bind="name"]', safeText(state.profile.name) || 'Ваше имя');
		setText('[data-bind="roles"]', safeText(state.profile.roles));
		setText('[data-bind="bio"]', safeText(state.profile.bio));

		var ctaLabel = safeText(state.cta.label) || 'Главная кнопка';
		setText('.art-starter-homepage-cta__label', ctaLabel);
		renderCtaIcon(safeText(state.cta.icon), ctaLabel);

		var ctaEl = previewRoot.querySelector('[data-bind="cta"]');
		if (ctaEl) {
			ctaEl.classList.toggle('art-starter-homepage-cta--with-icon', !!getEffectiveIconSlug(state.cta.icon, ''));
		}

		renderAvatar(safeText(state.profile.avatar_url), safeText(state.profile.name));
		renderLinks(state.links);
		renderSocial(
			state.socials,
			state.blocks && state.blocks.socials && state.blocks.socials.show_labels
		);

		setText('[data-bind="recommend-badge"]', safeText(state.recommend.badge) || 'Рекомендуем');
		setText('.art-starter-homepage-recommend__title', safeText(state.recommend.title));
		setText('.art-starter-homepage-recommend__desc', safeText(state.recommend.description));
		setText('.art-starter-homepage-recommend__button', safeText(state.recommend.button_label) || 'Смотреть');
		renderRecommendImage(safeText(state.recommend.image_url));

		var rec = previewRoot.querySelector('[data-bind="recommend"]');
		if (rec) {
			var hasRec = safeText(state.recommend.title) || safeText(state.recommend.description) || safeText(state.recommend.image_url) || safeText(state.recommend.badge);
			rec.style.display = hasRec ? '' : 'none';
		}

		applyBlockVisibility(state.blocks);
		syncPreviewFrameScale();
	}

	function syncPreviewFrameScale() {
		var scale = previewRoot && previewRoot.querySelector('.art-starter-homepage-preview__scale');
		var card = scale && scale.querySelector('.art-starter-homepage-card');

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

	function applyPreviewTemplate() {
		if (!templateSelect) {
			return;
		}

		var template = safeText(templateSelect.value) || defaultTemplate;
		var bodyClass = 'art-starter-homepage art-starter-homepage--' + template;

		if (previewShell) {
			previewShell.className = 'art-starter-homepage-shell ' + bodyClass;
		}

		if (previewRoot) {
			previewRoot.className = 'art-starter-homepage-preview__frame';
			if (template !== 'classic') {
				previewRoot.classList.add('art-starter-homepage-preview__frame--' + template);
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

	function linkArrowMarkup() {
		var svg = config.linkArrowSvg || '';
		if (!svg) {
			return '';
		}

		return (
			'<span class="art-starter-homepage-link__arrow" aria-hidden="true">' +
				'<span class="art-starter-homepage-link__arrow-svg">' + svg + '</span>' +
			'</span>'
		);
	}

	function buildSocialNetworkOptions(selected) {
		var html = '<option value="">' + (strings.socialSelect || '— выберите —') + '</option>';
		Object.keys(socialNetworks).forEach(function (slug) {
			var selectedAttr = safeText(selected) === slug ? ' selected' : '';
			html += '<option value="' + slug + '"' + selectedAttr + '>' + socialNetworks[slug] + '</option>';
		});
		return html;
	}

	function createSocialRow() {
		var index = socialsRoot ? socialsRoot.querySelectorAll('.art-starter-social-item-row').length : 0;
		var row = document.createElement('div');
		row.className = 'art-starter-social-item-row';

		row.innerHTML =
			buildSortButtonsHTML() +
			'<div class="art-starter-social-item-row__fields">' +
				'<p class="art-starter-social-item-row__field">' +
					'<label class="screen-reader-text">' + (strings.socialNetwork || 'Соцсеть') + '</label>' +
					'<select class="art-starter-field art-starter-social-item-row__network" name="' + socialRowFieldName(index, 'network') + '">' +
						buildSocialNetworkOptions('') +
					'</select>' +
				'</p>' +
				'<p class="art-starter-social-item-row__field">' +
					'<label class="screen-reader-text">' + (strings.socialUrl || 'Ссылка') + '</label>' +
					'<input type="text" class="art-starter-field art-starter-social-item-row__url" name="' + socialRowFieldName(index, 'url') + '" placeholder="' + (strings.linkUrlPlaceholder || 'example.com или https://...') + '">' +
				'</p>' +
			'</div>' +
			'<button type="button" class="button-link-delete art-starter-social-item-row__remove" aria-label="' + (strings.removeSocialAria || 'Удалить соцсеть') + '">' +
				(strings.removeSocial || 'Удалить') +
			'</button>';

		return row;
	}

	function linkRowFieldName(index, field) {
		return optionName + '[links][' + index + '][' + field + ']';
	}

	function reindexLinkRows() {
		if (!linksRoot) {
			return;
		}

		linksRoot.querySelectorAll('.art-starter-link-row').forEach(function (row, index) {
			var iconInput = row.querySelector('.art-starter-icon-field__input');
			var labelInput = row.querySelector('[data-art-starter-link-label]');
			var urlInput = row.querySelector('[data-art-starter-link-url]');

			if (iconInput) {
				iconInput.name = linkRowFieldName(index, 'icon');
			}
			if (labelInput) {
				labelInput.name = linkRowFieldName(index, 'label');
			}
			if (urlInput) {
				urlInput.name = linkRowFieldName(index, 'url');
			}
		});
	}

	function createLinkRow() {
		var index = linksRoot ? linksRoot.querySelectorAll('.art-starter-link-row').length : 0;
		var row = document.createElement('div');
		row.className = 'art-starter-link-row';

		row.innerHTML =
			buildSortButtonsHTML() +
			'<div class="art-starter-link-row__icon">' +
				buildIconFieldHTML(linkRowFieldName(index, 'icon'), iconPickerCategories, linkDefaultIcon) +
			'</div>' +
			'<div class="art-starter-link-row__fields">' +
				'<p class="art-starter-link-row__field">' +
					'<label class="screen-reader-text">' + (strings.linkText || 'Текст ссылки') + '</label>' +
					'<input type="text" class="art-starter-field" name="' + linkRowFieldName(index, 'label') + '" placeholder="' + (strings.linkText || 'Текст ссылки') + '" data-art-starter-link-label autocomplete="off">' +
				'</p>' +
				'<p class="art-starter-link-row__field">' +
					'<label class="screen-reader-text">' + (strings.linkUrl || 'URL') + '</label>' +
					'<input type="text" class="art-starter-field" name="' + linkRowFieldName(index, 'url') + '" placeholder="' + (strings.linkUrlPlaceholder || 'example.com или https://...') + '" data-art-starter-link-url autocomplete="off">' +
				'</p>' +
			'</div>' +
			'<button type="button" class="button-link-delete art-starter-link-row__remove" aria-label="' + (strings.removeLinkAria || 'Удалить ссылку') + '">' +
				(strings.removeLink || 'Удалить') +
			'</button>';

		return row;
	}

	function getFieldCategories($field) {
		return safeText($field.data('icon-categories')).split(',').filter(Boolean);
	}

	function buildIconPickerGrid($field) {
		if (!iconPickerGrid) {
			return;
		}

		var categories = getFieldCategories($field);
		var allowNone = String($field.data('icon-allow-none')) === '1';
		var html = '';

		if (allowNone) {
			html += '<div class="art-starter-icon-picker__section"><h3>' + (strings.noIcon || 'Без иконки') + '</h3><div class="art-starter-icon-picker__grid">';
			html += '<button type="button" class="art-starter-icon-picker__item" data-icon-slug=""><span class="art-starter-icon-picker__item-label">' + (strings.noIcon || 'Без иконки') + '</span></button>';
			html += '</div></div>';
		}

		Object.keys(iconCategories).forEach(function (category) {
			if (categories.length && categories.indexOf(category) === -1) {
				return;
			}

			var sectionIcons = Object.keys(icons).filter(function (slug) {
				return icons[slug].category === category;
			});

			if (!sectionIcons.length) {
				return;
			}

			html += '<div class="art-starter-icon-picker__section"><h3>' + iconCategories[category] + '</h3><div class="art-starter-icon-picker__grid">';
			sectionIcons.forEach(function (slug) {
				html += '<button type="button" class="art-starter-icon-picker__item" data-icon-slug="' + slug + '">' + iconMarkup(slug, 'art-starter-icon-picker__item-icon') + '<span class="art-starter-icon-picker__item-label">' + icons[slug].label + '</span></button>';
			});
			html += '</div></div>';
		});

		iconPickerGrid.innerHTML = html;
	}

	function openIconPicker($field) {
		if (!iconPicker) {
			return;
		}

		activeIconField = $field;
		buildIconPickerGrid($field);
		iconPicker.hidden = false;
		document.body.classList.add('art-starter-icon-picker-open');
	}

	function closeIconPicker() {
		if (!iconPicker) {
			return;
		}

		activeIconField = null;
		iconPicker.hidden = true;
		document.body.classList.remove('art-starter-icon-picker-open');
	}

	try {
		var initial = JSON.parse(dataEl.textContent || '{}');
		render(initial);
	} catch (e) {
		render(readState());
	}

	applyPreviewTemplate();

	if (templateSelect) {
		templateSelect.addEventListener('change', function () {
			applyPreviewTemplate();
			syncPreviewFrameScale();
		});
	}

	form.querySelectorAll('[data-art-starter-icon-field]').forEach(function (field) {
		updateIconFieldUI($(field));
	});

	form.querySelectorAll('.art-starter-social-item-row').forEach(function (row) {
		updateSocialRowUI(row);
	});
	updateAddSocialButton();

	form.addEventListener('submit', function () {
		reindexLinkRows();
		reindexSocialRows();
		syncSocialsPayload();
	});

	form.addEventListener(
		'input',
		function () {
			render(readState());
		},
		{ passive: true }
	);

	if (addLinkBtn && linksRoot) {
		addLinkBtn.addEventListener('click', function () {
			var row = createLinkRow();
			linksRoot.appendChild(row);
			reindexLinkRows();
			var firstInput = row.querySelector('input[type="text"]');
			if (firstInput) {
				firstInput.focus();
			}
			triggerPreviewUpdate();
		});
	}

	if (addSocialBtn && socialsRoot) {
		addSocialBtn.addEventListener('click', function () {
			if (socialsRoot.querySelectorAll('.art-starter-social-item-row').length >= maxSocials) {
				return;
			}

			var row = createSocialRow();
			socialsRoot.appendChild(row);
			reindexSocialRows();
			updateAddSocialButton();
			var select = row.querySelector('.art-starter-social-item-row__network');
			if (select) {
				select.focus();
			}
			triggerPreviewUpdate();
		});
	}

	if (linksRoot) {
		linksRoot.addEventListener('click', function (event) {
			var target = event.target;
			if (!target) {
				return;
			}

			if (target.classList.contains('art-starter-sortable-row__up') || target.classList.contains('art-starter-sortable-row__down')) {
				var sortRow = target.closest('.art-starter-link-row');
				if (sortRow) {
					moveRow(sortRow, target.classList.contains('art-starter-sortable-row__up') ? -1 : 1);
					reindexLinkRows();
					triggerPreviewUpdate();
				}
				return;
			}

			if (!target.classList.contains('art-starter-link-row__remove')) {
				return;
			}

			var row = target.closest('.art-starter-link-row');
			if (row && row.parentNode) {
				row.parentNode.removeChild(row);
				reindexLinkRows();
				triggerPreviewUpdate();
			}
		});
	}

	if (socialsRoot) {
		socialsRoot.addEventListener('click', function (event) {
			var target = event.target;
			if (!target) {
				return;
			}

			if (target.classList.contains('art-starter-sortable-row__up') || target.classList.contains('art-starter-sortable-row__down')) {
				var sortRow = target.closest('.art-starter-social-item-row');
				if (sortRow) {
					moveRow(sortRow, target.classList.contains('art-starter-sortable-row__up') ? -1 : 1);
					reindexSocialRows();
					triggerPreviewUpdate();
				}
				return;
			}

			if (!target.classList.contains('art-starter-social-item-row__remove')) {
				return;
			}

			var row = target.closest('.art-starter-social-item-row');
			if (row && row.parentNode) {
				row.parentNode.removeChild(row);
				reindexSocialRows();
				updateAddSocialButton();
				triggerPreviewUpdate();
			}
		});
	}

	form.addEventListener('change', function (event) {
		var target = event.target;
		if (!target) {
			return;
		}

		if (target.hasAttribute('data-art-starter-block-visibility')) {
			triggerPreviewUpdate();
			return;
		}

		if (target.hasAttribute('data-art-starter-social-labels-toggle')) {
			triggerPreviewUpdate();
			return;
		}

		if (!target.classList.contains('art-starter-social-item-row__network')) {
			return;
		}

		updateSocialRowUI(target.closest('.art-starter-social-item-row'));
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
