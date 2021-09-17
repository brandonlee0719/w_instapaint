$Core.Photo = {
    iItemId: 1,
    sModule: '',
    iAlbumId: 0,
    iTotal: 0,
    iTotalError: 0,
    bMassEdit: 0,
    oUploadedId: [],
    firstResponse: true,
    aPhotos: [],
    sAjax: '',
    bSubmit: false,
    bSkipUpload: false,
    // custom button
    setCoverPhoto: function (iPhotoId, iItemId, sModuleId) {
        $.ajaxCall(sModuleId + '.setCoverPhoto', 'photo_id=' + iPhotoId +
            '&page_id=' + iItemId);
    },
    slidePhotoStream: function (obj) {
        if ($(obj).hasClass('last_clicked_button')) {
            return;
        }
        var stream = $(obj).parent('.photos_stream').find('div.photos:first'),
            streamWidth = stream.width(),
            screenWidth = window.innerWidth;
        if (streamWidth <= screenWidth) {
            return;
        }
        var streamLeft = stream.outerWidth(true) - stream.outerWidth();
        if (streamLeft < 0) {
            streamLeft = streamLeft * (-1);
        }
        var id = $(obj).attr('id');
        if (id == 'prev_photos') {
            streamLeft = streamLeft - screenWidth;
            if (streamLeft < 0) {
                streamLeft = 0;
            }
        }
        else {
            streamLeft = streamLeft + screenWidth;
            if (streamLeft > streamWidth) {
                return;
            }
        }
        var checkLast = function(){
            var cLeft = stream.outerWidth(true) - stream.outerWidth(),
                bIsFirst = cLeft == 0;
            if (cLeft < 0) {
                cLeft = cLeft * (-1);
            }
            cLeft = cLeft + screenWidth;
            if (cLeft > streamWidth || bIsFirst) {
                $(obj).addClass('last_clicked_button');
            } else {
                $(obj).removeClass('last_clicked_button');
            }
        };
        if (window.rtl == 'ltr') {
            stream.animate({
                marginLeft: '-' + streamLeft,
            }, 500, checkLast);
        }
        if (window.rtl == 'rtl') {
            stream.animate({
                marginRight: '-' + streamLeft,
            }, 500, checkLast);
        }

    },
    updateAddNewAlbum: function (album_id) {
        var ele = $('#js_new_album');
        if (ele.length <= 0) {
            return false;
        }
        var value = ele.val();
        if (value != '') {
            value += ',' + album_id;
        }
        else {
            value = album_id;
        }
        ele.val(value);
    },
    deletePhoto: function (ele) {

        if (!ele.data('id')) return false;

        $Core.jsConfirm({message: ele.data('message')}, function () {
            $.ajaxCall('photo.deletePhoto', 'id=' + ele.data('id') + '&is_detail=' + ele.data('is-detail'));
        }, function () {
        });

        return false;
    },
    deleteAlbumPhoto: function (ele) {

        if (!ele.data('id')) return false;

        $Core.jsConfirm({message: ele.data('message')}, function () {
            $.ajaxCall('photo.deleteAlbumPhoto', 'id=' + ele.data('id') + '&is_detail=' + ele.data('is-detail'));
        }, function () {
        });

        return false;
    },
    toggleEditAction: function (ele, type) {
        var parent = $(ele).closest('.photo-edit-item');
        switch (type) {
            case 'download':
                if ($(ele).prop('checked')) {
                    parent.find('.item-allow-download').removeClass('active');
                }
                else {
                    parent.find('.item-allow-download').addClass('active');
                }
                break;
            case 'album':
                if ($(ele).val() > 0) {
                    parent.find('.photo_edit_holder').addClass('success');
                }
                else if (!$(ele).data('album_id')) {
                    parent.find('.photo_edit_holder').removeClass('success');
                }
                break;
            case 'category':
                if ($(ele).val() != null) {
                    parent.find('.item-categories').addClass('success');
                }
                else {
                    parent.find('.item-categories').removeClass('success');
                }
        }
    },

    // DROPZONE IN PHOTO.ADD
    // ======================================================
    dropzoneOnSending: function (data, xhr, formData) {
        $Core.Photo.bSubmit = true;
        $('#js_photo_done_upload').hide();
        $('#js_photo_form').find('input, select').each(function () {
            formData.append($(this).prop('name'), $(this).val());
        });
    },
    removeUploadedPhoto: function (id) {
        $Core.Photo.iTotal--;
        $Core.Photo.oUploadedId = $.grep($Core.Photo.oUploadedId, function (value) {
            return value != id;
        });
    },
    processResponse: function (t, file, response) {
        response = JSON.parse(response);

        if (typeof response.id !== 'undefined') {
            file.item_id = response.id;
        }

        if (typeof response.errors === 'object') {
            for (var i in response.errors) {
                if (response.errors[i]) {
                    $Core.dropzone.setFileError('photo', file, response.errors[i]);
                    return;
                }
            }
        }

        // upload photo successfully
        if (typeof response.ajax !== 'undefined' && typeof response.photo_info !== 'undefined') {
            $.ajaxCall('photo.process', response.ajax + '&photos=[' + response.photo_info + ']');
        }

        $Core.Photo.iTotal++;
        $Core.Photo.iAlbumId = response.album;
        $Core.Photo.oUploadedId.push(response.id);
        return file.previewElement.classList.add('dz-success');
    },
    dropzoneOnSuccess: function (ele, file, response) {
        $Core.Photo.processResponse(ele, file, response);
    },
    dropzoneOnAddedFile: function () {
        $Core.Photo.bSubmit = false;
        $('#js_photo_done_upload button').text(oTranslations['done']);
        $Core.Photo.bSkipUpload = false;
        $('#js_photo_done_upload').show();
    },
    dropzoneOnError: function () {
        $Core.Photo.iTotalError++;
    },
    dropzoneOnErrorInFeed: function () {
        $bButtonSubmitActive = false;
        $('.activity_feed_form_button .button').addClass('button_not_active');
        $Core.Photo.iTotalError++;
    },
    dropzoneOnComplete: function () {
        if (!$Core.Photo.bSubmit) return;
        $('#js_photo_done_upload').show();
        if ($Core.Photo.iTotalError > 0) {
            $('#js_photo_done_upload button').text(oTranslations['continue']);
            $Core.Photo.bSkipUpload = true;
            $('#photo-dropzone').find('.dz-preview').not('.dz-error').fadeOut(1000);
            return false;
        }
        $Core.Photo.redirectCompleteUpload();
    },
    redirectCompleteUpload: function () {
        $.ajax({
            url: PF.url.make('photo/message'),
            data: {
                valid: $Core.Photo.iTotal,
                upload_ids: JSON.stringify($Core.Photo.oUploadedId),
                album: $Core.Photo.iAlbumId,
                module: $Core.Photo.sModule,
                item: $Core.Photo.iItemId
            }
        }).success(function (data) {
            var oData = JSON.parse(data);
            if (oData.sUrl != "") {
                window.location.href = oData.sUrl;
            } else {
                window.location.href = getParam('sBaseURL') + 'photo';
            }
        });
    },
    // END OF DROPZONE IN PHOTO.ADD
    // ===============================================

    // DROPZONE PHOTO IN FEED
    // =====================================================
    dropzoneOnSendingInFeed: function (data, xhr, formData) {
        $('#js_activity_feed_form').find('input, textarea').each(function () {
            formData.append($(this).prop('name'), $(this).val());
        });
    },
    processResponseInFeed: function (t, file, response) {
        response = JSON.parse(response);

        // show error message
        if (typeof response.errors === 'object') {
            for (var i in response.errors) {
                if (response.errors[i]) {
                    $Core.dropzone.setFileError('photo_feed', file, response.errors[i]);
                    $sCacheFeedErrorMessage.push(file.name + ': ' + response.errors[i]);
                }
            }
        }

        // upload photo successfully
        if (typeof response.ajax !== 'undefined') {
            $Core.Photo.sAjax = response.ajax;
        }

        if (typeof response.photo_info !== 'undefined') {
            $Core.Photo.aPhotos.push(JSON.parse(response.photo_info));
        }

        return file.previewElement.classList.add('dz-success');
    },
    dropzoneOnSuccessInFeed: function (ele, file, response) {
        // process response
        $Core.Photo.processResponseInFeed(ele, file, response);
    },
    dropzoneOnCompleteInFeed: function () {
        if ($Core.Photo.sAjax && $Core.Photo.aPhotos.length > 0) {
            var ajax = $Core.Photo.sAjax + '&photos=' + JSON.stringify($Core.Photo.aPhotos);
            $.fn.ajaxCall('photo.process', ajax, true, 'POST', function () {
                $Core.Photo.dropzoneOnFinishInFeed();
            });
            $Core.Photo.sAjax = '';
            $Core.Photo.aPhotos = [];
        }
    },
    dropzoneOnFinishInFeed: function () {
        if ($Core.Photo.iTotalError > 0) {
            $bButtonSubmitActive = false;
            $('.activity_feed_form_button .button').addClass('button_not_active');
            $ActivityFeedCompleted.resetPhotoDropzone();
        } else {
          $bButtonSubmitActive = true;
        }
    },
    dropzoneOnRemovedFileInFeed: function (ele, file) {
        $('div#activity_feed_upload_error').empty().hide();
        if (file.status == 'error' && $Core.Photo.iTotalError > 0) {
            $Core.Photo.iTotalError--;
        }
        if (!$Core.Photo.iTotalError) {
            $bButtonSubmitActive = true;
            $('.activity_feed_form_button .button').removeClass('button_not_active');
        }
    }
    // END OF DROPZONE PHOTO IN FEED
    // ==============================================
};
PF.event.on('on_show_cache_feed_error_message',function(){
    if ($sCurrentForm == 'global_attachment_photo') {
        $('#activity_feed_upload_error').html('');
        $bButtonSubmitActive = false;
        $('.activity_feed_form_button .button').addClass('button_not_active');
    }
});
$ActivityFeedCompleted.resetPhotoDropzone = function () {
    if (typeof $Core.dropzone.instance.photo_feed !== 'undefined') {
        $Core.dropzone.instance.photo_feed.removeAllSuccessFiles();
    }
}

$Ready(function () {
    window.rtl = $('html').attr('dir');
    $('.pf-dropdown-not-hide-photo').click(function (event) {
        event.stopPropagation();
    });
    $('.pf-dropdown-not-hide-photo').find('span[data-dismiss="dropdown"]').on('click', function () {
        $(this).parents('.dropdown').trigger('click');
    });
    $('.photo-edit-item').find('.item-delete').on('click', function () {
        $(this).parents('.photo-edit-item-inner').addClass('delete');
    });
    $('.photo-edit-item').find('.delete-reverse').on('click', function () {
        $(this).parents('.photo-edit-item-inner').removeClass('delete');
        $(this).parents('.photo-edit-item-inner').find('.item-media.hide .item-delete input').removeAttr('checked');
    });
    if ($('a[rel="global_attachment_photo"]').length) {
        $('a[rel="global_attachment_photo"]').data('allow-checkin', 1);
    }

    if (!$('#page_photo_view').length) {
        $('.note , .notep').remove();
    }
    var $imageLoadHolder = $('.image_load_holder');
    if ($imageLoadHolder.length && !preLoadImages) {
        preLoadImages = true;
        var images = '',
            imageCount = 0;

        if (typeof aPhotos != 'undefined' && aPhotos.length > 0) {
            $.each(aPhotos, function (index, value) {
                imageCount++;
                images += '<a class="stream_photo" href="' + value.link +
                    '" data-photo-id="' + value.photo_id + '">' + value.html + '</a>';
            });
        }
        else if (cacheCurrentBody !== null &&
            typeof(cacheCurrentBody.contentObject) == 'string' && !$('.photos_stream').length) {
            $(cacheCurrentBody.contentObject).find('.photo-listing-item').each(function () {
                var t = $(this), src = t.find('a.item-media');
                t.addClass('pre_load');
                imageCount++;
                images += '<a class="stream_photo" href="' + t.data('url') +
                    '" data-photo-id="' + t.data('photo-id') + '"><span style="background-image:url(\'' + src.css('background-image').replace(/^url(?:\(['"]?)(.*?)(?:['"]?\))/, '$1') +
                    '\')"></span></a>';
            });
        }

        if (imageCount > 0 && !$('.photos_stream').length) {
            $('#content').prepend('<div class="photos_stream"><div class="photos">' + images +
                '</div></div>');
            var photos = $('.photos_stream .photos', '#content').first();
            if (photos && (imageCount * 110) > $(window).width()) {
                photos.parent().prepend(
                    '<a id="prev_photos" class="btn btn-primary last_clicked_button" href="javascript:void(0)" onclick="$Core.Photo.slidePhotoStream(this)"><i class="ico ico-angle-left"></i></a><a id="next_photos" class="btn btn-primary" href="javascript:void(0)" onclick="$Core.Photo.slidePhotoStream(this)"><i class="ico ico-angle-right"></i></a>');
            }
        }
        var img = new Image(), src = $imageLoadHolder.data('image-src'),
            imgAlt = new Image(), srcAlt = $imageLoadHolder.data('image-src-alt');
        imgAlt.onload = function () {
            $imageLoadHolder.html('<img src="' + srcAlt + '" id="js_photo_view_image">');
            $('body').addClass('photo_is_active');
            $Core.loadInit();
        };

        img.onload = function () {
            $imageLoadHolder.html('<img src="' + src + '" id="js_photo_view_image">');
            $('body').addClass('photo_is_active');
            $Core.loadInit();
        };
        img.onerror = function () {
            imgAlt.src = srcAlt;
        };
        img.src = src;
    }

    if (!$imageLoadHolder.length) {
        $('.photos_stream').remove();
    }
    if ($('.photos_stream').length && $imageLoadHolder.length && !preSetActivePhoto) {
        preSetActivePhoto = true;
        $('.photos_stream a.active').removeClass('active');
        if ($('.photos_view').data('photo-id')) {
            var currentPhoto = ($('.photos_stream a[data-photo-id="' +
                $('.photos_view').data('photo-id') + '"]').length > 0)
                ? $('.photos_stream a[data-photo-id="' +
                    $('.photos_view').data('photo-id') + '"]').first()
                : null;
            if (currentPhoto != null) {
                currentPhoto.addClass('active');
                var nextPhoto = currentPhoto.next('.stream_photo');
                if (nextPhoto.length > 0) {
                    var html = '<a id="next_photo" class="button btn-primary photo_btn" href="' +
                        nextPhoto.attr('href') +
                        '"><i class="ico ico-angle-right"></i></a>';
                    $imageLoadHolder.parent().append(html);
                }
                var prevPhoto = currentPhoto.prev('.stream_photo');
                if (prevPhoto.length > 0) {
                    var html = '<a id="previous_photo" class="button btn-primary photo_btn" href="' +
                        prevPhoto.attr('href') + '"><i class="ico ico-angle-left"></i></a>';
                    $imageLoadHolder.parent().append(html);
                }
            }
        }
    }

    if ($('.js_photo_active_items').length > 0) {
        $('.js_photo_active_items').each(function () {
            if (!$(this).prop('built')) {
                $(this).prop('built', true);
                var aParts = explode(',', $(this).html());
                for (i in aParts) {
                    if (empty(aParts[i])) {
                        continue;
                    }
                    $(this).parents('.js_category_list_holder:first').find('.js_photo_category_' + aParts[i] + ':first').attr('selected', true);
                }
            }
        });
    }

    $('#js_photo_album_select').change(function () {
        if (empty(this.value)) {
            $('#js_photo_privacy_holder').slideDown();
        }
        else {
            $('#js_photo_privacy_holder').slideUp();
            $('#js_photo_done_upload').data('album', this.value);
        }
    });

    $('#js_delete_this_album').click(function () {
        $('#js_photo_edit_form_outer').hide();
        $('#js_album_outer_content').hide();
        $('#js_album_edit_form').hide();
        $('#js_album_delete_form').show();

        return false;
    });

    $('#js_edit_this_album').click(function () {
        $('#js_photo_edit_form_outer').hide();
        $('#js_album_outer_content').hide();
        $('#js_album_delete_form').hide();
        $('#js_album_edit_form').show();

        return false;
    });

    $('#js_album_cancel_edit').click(function () {
        $('#js_album_edit_form').hide();
        $('#js_album_outer_content').show();

        return false;
    });

    $('.js_photo_set_cover').click(function () {
        $('.js_photo_set_cover').each(function () {
            $(this).parent().show();
        });

        $(this).parents('div:first').parent().find('.js_photo_set_cover_div').hide();

        $.ajaxCall('photo.setAlbumCover', $Core.getHashParam(this.href));

        return false;
    });
    $('#js_photo_done_upload').on('click', function() {
        if (typeof $Core.dropzone.instance['photo'] !== 'object') {
            return;
        }
        if ($Core.Photo.bSkipUpload) {
            $Core.Photo.redirectCompleteUpload();
            return;
        }
        if ($Core.Photo.iTotalError > 0) {
            tb_show(oTranslations['notice'], '', null, oTranslations['upload_failed_please_remove_all_error_files_and_try_again']);
            return false;
        }
        $Core.dropzone.instance['photo'].processQueue();
    });
    if ($('._a_back').length) {
        $('._a_back').on('click', function(){
            $('#noteform').remove();
        });
    }
});


if (typeof $Core.Photo == 'undefined') {
    $Core.Photo = {};
}

var core_photos_onchangeDeleteCategoryType = function (type) {
    if (type == 2)
        $('#category_select').show();
    else
        $('#category_select').hide();
};

$Core.Photo.updateAddNewAlbum = function (album_id) {
    var ele = $('#js_new_album');
    if (ele.length <= 0) return false;
    var value = ele.val();
    if (value != "") {
        value += ',' + album_id;
    }
    else {
        value = album_id;
    }
    ele.val(value);
};
