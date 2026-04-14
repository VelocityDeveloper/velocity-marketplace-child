(function ($) {
    function updateStore($repeater) {
        var items = [];

        $repeater.find('.vmc-repeater-item').each(function () {
            var item = {};

            $(this).find('[data-field]').each(function () {
                item[$(this).data('field')] = $(this).val();
            });

            items.push(item);
        });

        $repeater.find('.vmc-repeater-store').val(JSON.stringify(items)).trigger('change');
        updateSummaries($repeater);
    }

    function updateSummaries($repeater) {
        var base = $repeater.data('default-label') || 'Item';

        $repeater.find('.vmc-repeater-item').each(function (index) {
            $(this).find('.vmc-repeater-item-label').text(base + ' ' + (index + 1));
        });
    }

    function toggleItem($item, forceOpen) {
        var open = typeof forceOpen === 'boolean' ? forceOpen : $item.hasClass('is-collapsed');

        if (open) {
            $item.removeClass('is-collapsed');
            $item.find('.vmc-repeater-toggle').attr('aria-expanded', 'true');
        } else {
            $item.addClass('is-collapsed');
            $item.find('.vmc-repeater-toggle').attr('aria-expanded', 'false');
        }
    }

    function bindMedia($context, $repeater) {
        $context.find('.vmc-repeater-media-select').off('click').on('click', function (event) {
            event.preventDefault();

            var $button = $(this);
            var $field = $button.closest('.vmc-repeater-field');
            var $input = $field.find('input[data-field]');
            var $preview = $field.find('.vmc-repeater-media-preview');

            var frame = wp.media({
                title: 'Pilih Gambar',
                multiple: false,
                library: { type: 'image' }
            });

            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();
                var thumb = attachment.sizes && attachment.sizes.medium_large ? attachment.sizes.medium_large.url
                    : attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url
                    : attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url
                    : attachment.url;
                $input.val(attachment.id).trigger('change');
                $preview.addClass('has-image').html('<img src="' + thumb + '" alt="">');
                updateStore($repeater);
            });

            frame.open();
        });

        $context.find('.vmc-repeater-media-remove').off('click').on('click', function (event) {
            event.preventDefault();

            var $field = $(this).closest('.vmc-repeater-field');
            $field.find('input[data-field]').val('').trigger('change');
            $field.find('.vmc-repeater-media-preview').removeClass('has-image').empty();
            updateStore($repeater);
        });
    }

    function bindRepeater($repeater) {
        var template = $repeater.find('.vmc-repeater-template').html();

        bindMedia($repeater, $repeater);
        updateSummaries($repeater);

        $repeater.on('input change', '[data-field]', function () {
            updateStore($repeater);
        });

        $repeater.on('click', '.vmc-repeater-toggle', function (event) {
            event.preventDefault();
            toggleItem($(this).closest('.vmc-repeater-item'));
        });

        $repeater.find('.vmc-repeater-add').on('click', function (event) {
            event.preventDefault();

            var $item = $(template);
            $repeater.find('.vmc-repeater-items').append($item);
            bindMedia($item, $repeater);
            toggleItem($item, true);
            updateStore($repeater);
        });

        $repeater.on('click', '.vmc-repeater-remove', function (event) {
            event.preventDefault();
            $(this).closest('.vmc-repeater-item').remove();
            updateStore($repeater);
        });

        $repeater.on('click', '.vmc-repeater-clone', function (event) {
            event.preventDefault();

            var $current = $(this).closest('.vmc-repeater-item');
            var $clone = $current.clone();
            $repeater.find('.vmc-repeater-items').append($clone);
            bindMedia($clone, $repeater);
            toggleItem($clone, true);
            updateStore($repeater);
        });

        $repeater.find('.vmc-repeater-item').each(function () {
            toggleItem($(this), false);
        });
    }

    $(function () {
        $('.vmc-repeater').each(function () {
            bindRepeater($(this));
        });
    });
})(jQuery);
