$Ready(function () {
    var initPlaySongInterval;
    if (typeof(mejs) == 'undefined') {
        initPlaySongInterval = window.setInterval(function () {
            $Core.loadStaticFiles(oParams.sJsHome.replace('PF.Base', 'PF.Site') + 'Apps/core-music/assets/jscript/mediaelementplayer/mediaelement-and-player.js');
            if (typeof(mejs) == 'undefined') {
            }
            else {
                $(".js_song_player").each(function () {
                    $Core.music.initPlayInFeed(this);
                });
                window.clearInterval(initPlaySongInterval);
            }
        }, 200);
    }
    else {
        $(".js_song_player").each(function () {
            $Core.music.initPlayInFeed(this);
        });
    }

    $('#js_done_upload').on('click', function () {
        window.onbeforeunload = null;
        $.ajax({
            url: PF.url.make('music/message'),
            data: {
                valid: $Core.music.iValidFile,
                module: $Core.music.sModule,
                album: $Core.music.iAlbumId,
                item: $Core.music.iItemId
            }
        }).success(function (data) {
            var aData = JSON.parse(data);
            if (aData.sUrl != "") {
                window.location.href = aData.sUrl;
            } else {
                window.location.href = getParam('sBaseURL') + 'music';
            }
        });
    });

    $(document).on('click', '.dropzone-clickable', function () {
        var t = $(this);
        if (t.data('dropzone-button-id')) {
            $('#' + t.data('dropzone-button-id')).trigger('click');
        }

        return false;
    });
    $('#js_music_album_select').change(function() {
        if (empty(this.value)) {
            $('#js_song_privacy_holder').slideDown();
        }
        else {
            $('#js_song_privacy_holder').slideUp();
        }
    });
});
document.addEventListener('play', function (e) {
    var audios = $('audio');
    for (var i = 0, len = audios.length; i < len; i++) {
        if (audios[i] != e.target) {
            audios[i].pause();
        }
    }
}, true);
var core_music_onchangeDeleteGenreType = function (type) {
    if (type == 3)
        $('#genre_select').show();
    else
        $('#genre_select').hide();
};
//Upload js
$Core.music =
    {
        iValidFile: 0,
        oFeedIds: {},
        oAddFiles: [],
        iAlbumId: 0,
        bRepeatInFeed: false,
        iCurrentPlaying: 0,
        sModule: '',
        iItemId: 0,
        iTotalTimePer: 0,
        dropzoneOnSending: function (data, xhr, formData) {
            $('#js_actual_upload_form').find('input, select').each(function () {
                formData.append($(this).prop('name'), $(this).val());
            });
        },
        dropzoneQueueComplete: function () {
            $Core.music.iTotalTimePer = 0;
        },
        dropzoneAddedFile: function () {
            $Core.music.iTotalTimePer++;
            $('#js_error_message').hide();
            $('#js_total_success_holder').show();
            if (!$('#js_music_uploading').length) {
                var sNoticeHtml = '<div id="js_music_uploading" class="mb-2"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i>&nbsp;' + oTranslations['uploading_three_dot'] + '<span id="dropzone-total-uploading"></span></div><div id="js_uploading_notice" class="mb-2"><b>' + oTranslations['please_do_not_refresh_the_current_page_or_close_the_browser_window'] + '</b></div>';
                $('#music_song-dropzone').find('.dz-default').prepend(sNoticeHtml);
                $('.dropzone-button-music_song').append(' ' + oTranslations['add_more_files']);
            }
            else {
                $('#js_music_uploading').show();
                $('#js_uploading_notice').show();
            }
            $('.dropzone-button-music_song').addClass('uploaded');
        },
        dropzoneOnError: function (ele, file, message) {
            var sErrorHtml = '<li class="js_uploaded_file_holder hide music-item item-outer"><div class="item-inner"><p class="text-danger">' + file.name + '&nbsp;-&nbsp;' + message + '</p><div class="item-actions"><a href="javascript:void(0)" onclick="$(this).parents(\'.js_uploaded_file_holder\').remove();"><i class="ico ico-close"></i></a></div></div>';
            $('#js_music_uploaded_section').prepend(sErrorHtml);
            $Core.music.iTotalTimePer--;
            if (!$Core.music.iTotalTimePer) {
                $Core.music.doneAllFile();
            }
            $Core.dropzone.instance['music_song'].removeFile(file);
        },
        dropzoneOnSuccess: function (ele, file, response) {
            response = JSON.parse(response);
            $Core.music.iAlbumId = 0;
            $Core.music.iTotalTimePer--;
            if ($('#js_album_id').length) {
                $Core.music.iAlbumId = $('#js_album_id').val();
            }
            if (!response.errors && response.id) {
                //append edit form
                $.ajaxCall('music.appendAddedSong','id='+response.id);
            }
            else {
                var sErrorHtml = '<li class="js_uploaded_file_holder hide music-item item-outer"><div class="item-inner"><p class="text-danger">' + file.name + '&nbsp;-&nbsp;' + response.errors + '</p><div class="item-actions"><a href="javascript:void(0)" onclick="$(this).parents(\'.js_uploaded_file_holder\').remove();"><i class="ico ico-close"></i></a></div></div>';
                $('#js_music_uploaded_section').prepend(sErrorHtml);
            }

            return true;
        },
        doneAllFile: function () {
            $('.js_uploaded_file_holder').removeClass('hide');
            $('#js_music_uploading').hide();
            $('#js_uploading_notice').hide();
        },
        setName: function (iSong) {
            if ($("#title").val() != '') {
                $.ajaxCall('music.setName', 'sTitle=' + $("#title").val() + '&iSong=' + iSong);
            }
        },

        showForm: function (ele) {
            $(ele).hide();
            $(ele).closest('.js_uploaded_file_holder').find('.js_music_form_holder').fadeIn();
            $(ele).closest('.js_uploaded_file_holder').find('.js_hide_form').show();
        },

        hideForm: function (ele) {
            $(ele).hide();
            $(ele).closest('.js_uploaded_file_holder').find('.js_music_form_holder').fadeOut();
            $(ele).closest('.js_uploaded_file_holder').find('.js_show_form').show();
        },

        editSong: function (ele, isAjax) {
            var song_id = $(ele).data('id'),
                description = $('#description_' + song_id).val();
            $(ele).addClass('disabled');

            if (isAjax) {
                if ($("[name='val[" + song_id + "][title]']").val() == '') {
                    tb_show(oTranslations['notice'], '', null, oTranslations['provide_a_name_for_this_song']);
                    $(ele).removeClass('disabled');
                    return false;
                }
                if (typeof(CKEDITOR) !== 'undefined') {
                    if (typeof(CKEDITOR.instances["description_" + song_id]) !== 'undefined') {
                        description = CKEDITOR.instances["description_" + song_id].getData();
                        $("textarea[name='val[description_" + song_id + "]']").val(description);
                    }
                }
                var temp_file = '',
                    remove_photo = '';
                if ($('#js_upload_form_file_music_song_'+ song_id).length) {
                    temp_file = $('#js_upload_form_file_music_song_'+ song_id).val();
                }
                if ($('#js_upload_remove_file_music_song_' + song_id).length) {
                    remove_photo = $('#js_upload_remove_file_music_song_' + song_id).val();
                }
                $.ajaxCall('music.updateSong', 'song_id=' + song_id + '&description=' + description + '&temp_file=' + temp_file + '&remove_photo=' + remove_photo + '&' + $('#js_music_upload_form').serialize());
                $('#js_song_title_' + song_id).html($("[name='val[" + song_id + "][title]']").val());
            }
            else {
                $('#js_file_holder_' + song_id).closest('form').submit();
            }

        },
        removeTempFile: function (ele) {
            var file_path = $(ele).data('path'),
                index = $(ele).data('index');
            $Core.music.iValidFile = $Core.music.iValidFile == 0 ? 0 : ($Core.music.iValidFile - 1);
            $.ajaxCall('music.removeTempFile', 'path=' + file_path + '&index=' + index);
            return true;
        },
        deleteSongInAddForm: function (ele) {
            var song_id = $(ele).data('id'),
                album_id = $(ele).data('album-id');
            if (song_id > 0) {
                $Core.dropzone.instance['music_song'].files.shift();
                $Core.jsConfirm({message: oTranslations['are_you_sure_you_want_to_delete_this_song']}, function () {
                    $.ajaxCall('music.deleteSong', 'id=' + song_id + '&inline=1&album_id=' + album_id + '&time_stamp=' + $('#js_upload_time_stamp').val());
                }, function () {
                });
            }
        },
        initPlayInFeed: function (divId, bAutoPlay) {
            if ($(divId).hasClass('built')) {
                return;
            }
            $(divId).addClass('built', true);
            var block_content = $('._block_content');
            block_content.find('.music_row.active').each(function () {
                var temp_audio = $(this).find('.music_player .js_player_holder audio');
                if (temp_audio.length > 0) {
                    (temp_audio[0]).pause();
                }
            });
            var css_href = oParams.sJsHome.replace('PF.Base', 'PF.Site') + 'Apps/core-music/assets/jscript/mediaelementplayer/mediaelementplayer.css';
            if (!$("link[href='" + css_href + "']").length) {
                var css = document.createElement('link');
                css.href = css_href;
                css.rel = 'stylesheet';
                css.type = 'text/css';
                document.getElementsByTagName("head")[0].appendChild(css);
            }
            $(divId).mediaelementplayer({
                alwaysShowControls: true,
                features: ['playpause', 'current', 'progress', 'duration', 'volume'],
                audioVolume: 'horizontal',
                startVolume: 0.5,
                setDimensions: false,
                success: function (mediaPlayer, domObject) {
                    if (bAutoPlay) {
                        mediaPlayer.play();
                    }
                    mediaPlayer.addEventListener('play', function () {
                        if ($('.music_row.active').data('songid') != $Core.music.iCurrentPlaying) {
                            $Core.music.iCurrentPlaying = $('.music_row.active').data('songid');
                            $('.js_music_repeat').removeClass('active');
                            $Core.music.bRepeatInFeed = false;
                        }
                    });
                    mediaPlayer.addEventListener('loadedmetadata', function () {
                        $('.js_music_repeat').off('click').on('click', function () {
                            $Core.music.bRepeatInFeed = !$Core.music.bRepeatInFeed;
                            if ($Core.music.bRepeatInFeed) {
                                $(this).addClass('active');
                            }
                            else {
                                $(this).removeClass('active');
                            }
                        });
                    });
                    mediaPlayer.addEventListener('ended', function () {
                        if ($Core.music.bRepeatInFeed) {
                            mediaPlayer.play();
                        }
                    });
                },
                error: function (error) {
                    console.log(error);
                }
            });
        },
        playSongRow: function (obj) {
            var parent = $(obj).closest('.music_row');
            var audio = parent.find('.music_player .js_player_holder audio');
            var song_id = parent.data('songid');
            if (audio.length > 0) {
                audio = audio[0];
            }
            else {
                return false;
            }

            if (parent.hasClass('active')) {
                audio.pause();
                audio.currentTime = 0;
                parent.removeClass('active');
                parent.find('.music_player').slideToggle();
                return true;
            }
            var block_content = $('body').find('._block_content, ._block');
            block_content.find('.music_row.active').each(function () {
                $(this).find('.music_player').slideToggle();
                var temp_audio = $(this).find('.music_player .js_player_holder audio');
                if (temp_audio.length > 0) {
                    (temp_audio[0]).pause();
                    (temp_audio[0]).currentTime = 0;
                }
                $(this).removeClass('active');
            });
            parent.find('.music_player').slideToggle();
            audio.play();
            $.ajaxCall('music.play', 'id=' + song_id, 'POST');
            parent.addClass('active');
        },



        //Submit add/edit album with dropzone
        submitAlbumForm: function(ele) {
            $(ele).attr('disabled', 'disabled');
            if (typeof $Core.dropzone.instance['music-album'] != 'undefined') {
                if ($Core.dropzone.instance['music-album'].getQueuedFiles().length && $('#js_music_add_album_form').find('input[name="val[name]"]').val() != '' && parseInt($('#js_music_add_album_form').find('input[name="val[year]"]').val()) > 0) {
                    $Core.dropzone.instance['music-album'].processQueue();
                }
                else {
                    if (!$('#music-album-dropzone').find('.dz-preview.dz-error').length) {
                        $(ele).closest('form').submit();
                    }
                    else {
                        $(ele).removeAttr('disabled');
                    }
                }
            }
            else {
                $(ele).closest('form').submit();
            }
        },
        deleteSongImage: function (iSongId) {
            $Core.jsConfirm({message: oTranslations['are_you_sure']}, function () {
                $.ajaxCall('music.deleteSongImage', 'id=' + iSongId);
            }, function () {
            });
            return false;
        },
        deleteAlbumImage: function (iAlbumId) {
            $Core.jsConfirm({message: oTranslations['are_you_sure']}, function () {
                $.ajaxCall('music.deleteImage', 'id=' + iAlbumId);
            }, function () {
            });
            return false;
        },
    };