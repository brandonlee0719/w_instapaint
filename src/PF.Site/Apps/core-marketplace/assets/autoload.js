$Ready(function () {

});

$Behavior.marketplaceAdd = function () {
    $('.js_mp_category_list').change(function () {
        var iParentId = parseInt(this.id.replace('js_mp_id_', ''));
        $('.js_mp_category_list').each(function () {
            if (parseInt(this.id.replace('js_mp_id_', '')) > iParentId) {
                $('#js_mp_holder_' + this.id.replace('js_mp_id_', '')).hide();
                this.value = 0;
            }
        });

        $('#js_mp_holder_' + $(this).val()).show();
    });
    if ($('#js_marketplace_form').length > 0 && typeof($Core.dropzone.instance['marketplace']) != 'undefined') {
        $Core.dropzone.instance['marketplace'].files = [];
    }
}


$Core.marketplace =
    {
        sUrl: '',

        url: function (sUrl) {
            this.sUrl = sUrl;
        },

        action: function (oObj, sAction) {
            aParams = $.getParams(oObj.href);

            $('.dropContent').hide();

            switch (sAction) {
                case 'edit':
                    window.location.href = this.sUrl + 'add/id_' + aParams['id'] + '/';
                    break;
                case 'delete':
                    var url = this.sUrl;
                    $Core.jsConfirm({}, function () {
                        window.location.href = url + 'delete_' + aParams['id'] + '/';
                    }, function () {
                    });
                    break;
                default:

                    break;
            }

            return false;
        },

        dropzoneOnSending: function (data, xhr, formData) {
            $('#js_marketplace_form').find('input[type="hidden"]').each(function () {
                formData.append($(this).prop('name'), $(this).val());
            });
        },

        dropzoneOnSuccess: function (ele, file, response) {
            $Core.marketplace.processResponse(ele, file, response);
        },

        dropzoneOnError: function (ele, file) {

        },

        dropzoneQueueComplete: function () {
            $('#js_listing_done_upload').show();
        },
        processResponse: function (t, file, response) {
            response = JSON.parse(response);
            if (typeof response.id !== 'undefined') {
                file.item_id = response.id;
                if (typeof t.data('submit-button') !== 'undefined') {
                    var ids = '';
                    if (typeof $(t.data('submit-button')).data('ids') !== 'undefined') {
                        ids = $(t.data('submit-button')).data('ids');
                    }
                    $(t.data('submit-button')).data('ids', ids + ',' + response.id);
                }
            }
            // show error message
            if (typeof response.errors != 'undefined') {
                for (var i in response.errors) {
                    if (response.errors[i]) {
                        $Core.dropzone.setFileError('marketplace', file, response.errors[i]);
                        return;
                    }
                }
            }
            return file.previewElement.classList.add('dz-success');
        },
        toggleUploadSection: function (id) {
            var parent = $('#js_mp_block_customize'),
                show_upload = 1;
            if (parent.hasClass('show_form')) {
                parent.removeClass('show_form');
                show_upload = 0;
                $Core.dropzone.instance['marketplace'].files = [];
            }
            else {
                parent.addClass('show_form');
            }

            parent.html('<div class="js_loading_form text-center "><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></div>');
            $.ajaxCall('marketplace.toggleUploadSection', 'show_upload=' + show_upload + '&id=' + id);
        },
        deleteListing: function (ele) {

            if (!ele.data('id')) return false;

            $Core.jsConfirm({message: ele.data('message')}, function () {
                $.ajaxCall('marketplace.delete', 'id=' + ele.data('id') + '&is_detail=' + ele.data('is-detail'));
            }, function () {
            });

            return false;
        },
    };

$Behavior.marketplaceShowImage = function () {

    $('.listing_view_images ._thumbs img').each(function () {
        var t = $(this),
            src = t.attr('src').replace('_120_square', '_400_square'),
            img = new Image();

        if (src == $('.listing_view_images ._main img').attr('src')) {
            t.addClass('active');
        }

        img.src = src;
    });

    $('.listing_view_images ._thumbs img').click(function () {
        var t = $(this),
            src = t.attr('src').replace('_120_square', '_400_square');

        $('.listing_view_images ._thumbs img.active').removeClass('active');
        $('.listing_view_images ._main img').attr('src', src);
        t.addClass('active');
    });
}
var core_marketplace_onchangeDeleteCategoryType = function (type) {
    if (type == 2)
        $('#category_select').show();
    else
        $('#category_select').hide();
};