(function ($) {
	'use strict';

	var config = window.artStarterInitialSetupAdmin || {};
	var strings = config.strings || {};
	var mediaFrame = null;

	function safeText(value) {
		return (value || '').toString().trim();
	}

	function updateSiteIconFieldUI($field) {
		var $input = $field.find('.art-starter-site-icon-field__input');
		var $preview = $field.find('.art-starter-media-field__preview');
		var $img = $preview.find('img');
		var $select = $field.find('.art-starter-site-icon-field__select');
		var $remove = $field.find('.art-starter-site-icon-field__remove');
		var attachmentId = parseInt($input.val(), 10) || 0;

		if (attachmentId > 0 && safeText($img.attr('src'))) {
			$preview.prop('hidden', false);
			$select.text(strings.changeFavicon || 'Заменить фавикон');
			$remove.prop('disabled', false);
			return;
		}

		$preview.prop('hidden', true);
		$img.attr('src', '');
		$select.text(strings.selectFavicon || 'Выбрать фавикон');
		$remove.prop('disabled', true);
	}

	$('[data-art-starter-site-icon-field]').each(function () {
		updateSiteIconFieldUI($(this));
	});

	$(document).on('click', '.art-starter-site-icon-field__select', function (event) {
		event.preventDefault();

		if (!window.wp || !window.wp.media) {
			return;
		}

		var $field = $(this).closest('[data-art-starter-site-icon-field]');
		var $input = $field.find('.art-starter-site-icon-field__input');
		var $img = $field.find('.art-starter-media-field__preview img');

		if (mediaFrame) {
			mediaFrame.off('select');
		}

		mediaFrame = window.wp.media({
			title: strings.selectFavicon || 'Выбрать фавикон',
			button: { text: strings.selectFavicon || 'Выбрать фавикон' },
			multiple: false,
			library: { type: 'image' }
		});

		mediaFrame.on('select', function () {
			var attachment = mediaFrame.state().get('selection').first().toJSON();
			var attachmentId = parseInt(attachment.id, 10) || 0;
			var previewUrl = '';

			if (attachment.sizes && attachment.sizes.thumbnail && attachment.sizes.thumbnail.url) {
				previewUrl = attachment.sizes.thumbnail.url;
			} else if (attachment.url) {
				previewUrl = attachment.url;
			}

			$input.val(attachmentId > 0 ? String(attachmentId) : '');
			$img.attr('src', previewUrl);
			updateSiteIconFieldUI($field);
		});

		mediaFrame.open();
	});

	$(document).on('click', '.art-starter-site-icon-field__remove', function (event) {
		event.preventDefault();

		var $field = $(this).closest('[data-art-starter-site-icon-field]');
		var $input = $field.find('.art-starter-site-icon-field__input');
		var $img = $field.find('.art-starter-media-field__preview img');

		$input.val('0');
		$img.attr('src', '');
		updateSiteIconFieldUI($field);
	});
})(jQuery);
