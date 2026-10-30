/* global jQuery */
jQuery(function ($) {

    // --------------------------------------------------------
    // Staff Rating — live preview khi thay đổi điểm
    // --------------------------------------------------------
    $('#jsmb_staff_rating').on('input change', function () {
        var val = parseFloat($(this).val()) || 0;
        val = Math.max(0, Math.min(5, val));

        var html = '<span class="jsmb-stars">';
        for (var i = 1; i <= 5; i++) {
            if (val >= i) {
                html += '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ea751e" width="18" height="18"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>';
            } else {
                html += '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ea751e" stroke-width="2" width="18" height="18"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>';
            }
        }
        html += '</span> <span class="jsmb-rating-val">' + val.toFixed(1) + '</span>';
        $('#jsmb_rating_preview').html(html);
    });

    // --------------------------------------------------------
    // Company Photos — Media Uploader
    // --------------------------------------------------------
    var mediaFrame;

    $('#jsmb_add_photos_btn').on('click', function (e) {
        e.preventDefault();

        if (mediaFrame) {
            mediaFrame.open();
            return;
        }

        mediaFrame = wp.media({
            title: 'Chọn ảnh công ty',
            button: { text: 'Thêm ảnh đã chọn' },
            library: { type: 'image' },
            multiple: true
        });

        mediaFrame.on('select', function () {
            var selection = mediaFrame.state().get('selection');
            var currentPhotos = getPhotoList();

            selection.each(function (attachment) {
                var url = attachment.toJSON().url;
                if (currentPhotos.indexOf(url) === -1) {
                    currentPhotos.push(url);
                    appendPhotoItem(url);
                }
            });

            updatePhotosData(currentPhotos);
        });

        mediaFrame.open();
    });

    // Remove a photo
    $(document).on('click', '.jsmb-photo-remove', function () {
        var $item = $(this).closest('.jsmb-photo-item');
        var url = $item.data('url');
        var photos = getPhotoList().filter(function (u) { return u !== url; });
        $item.remove();
        updatePhotosData(photos);
    });

    // Read current photo list from hidden input
    function getPhotoList() {
        try {
            return JSON.parse($('#jsmb_photos_data').val()) || [];
        } catch (e) {
            return [];
        }
    }

    // Update hidden input
    function updatePhotosData(photos) {
        $('#jsmb_photos_data').val(JSON.stringify(photos));
    }

    // Append a photo thumbnail item to the grid
    function appendPhotoItem(url) {
        var html = '<div class="jsmb-photo-item" data-url="' + url + '">' +
            '<img src="' + url + '" alt="" />' +
            '<button type="button" class="jsmb-photo-remove" title="Xóa ảnh">\u00d7</button>' +
            '</div>';
        $('#jsmb_photos_preview').append(html);
    }

});
