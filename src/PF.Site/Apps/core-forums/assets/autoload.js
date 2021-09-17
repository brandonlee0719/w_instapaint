$Ready(function () {
    if (($('#form_main_search') && $('#js_forum_search_result')).length) {
        var search_icon = $('#form_main_search').find('a.form-control-feedback');
        $('#js_forum_search_result').detach().insertBefore(search_icon);
        $('#form_main_search').addClass('header-bar-advanced-search');
    }

    if ($('.forum_quick_link_wrapper').length) {
        $('.forum_quick_link_wrapper').detach().appendTo('.header-page-title');
    }

    if ($('.rss-link-forum').length) {
        $('.rss-link-forum').detach().appendTo('.header-page-title').show();
    }
});

$Core.forum =
    {
        aParams: {},

        init: function (aParams) {
            this.aParams = aParams;
        },

        action: function (oObj, sAction) {
            aParams = $.getParams(oObj.href);

            this.aParams['id'] = aParams['id'];

            $('.dropContent').hide();

            switch (sAction) {
                case 'permission':
                    window.location.href = this.aParams['url'] + 'permission/id_' + aParams['id'] + '/';
                    break;
                case 'moderator':
                    $('#js_form_actual_content').hide();
                    $('#js_forum_edit_content').show();
                    $('#js_forum_edit_content').html($.ajaxProcess(oTranslations['loading'], 'large'));
                    //	window.location.href = '#moderator/';
                    $.ajaxCall('forum.getModerators', 'id=' + aParams['id']);
                    break;
                case 'delete':
                    console.log('delete');
                    tb_show('', $.ajaxBox('forum.deleteForumForm', 'id=' + aParams['id']));
                    break;
                case 'view':
                    window.location.href = this.aParams['url'] + 'view_' + aParams['id'] + '/';
                    break;
                case 'edit':
                    window.location.href = this.aParams['url'] + 'add/id_' + aParams['id'] + '/';
                    break;
                case 'add':
                    window.location.href = this.aParams['url'] + 'add/child_' + aParams['id'] + '/';
                    break;
                default:

                    break;
            }

            return false;
        },

        getParam: function (sParam) {
            return this.aParams[sParam];
        },

        cancel: function () {
            $('.js_cached_user_name').removeClass('row_focus');
            $('#js_actual_user_id').val('');
            $('#js_perm_title').html(oTranslations['global_moderator_permissions']);

            return false;
        },

        build: function (aParams) {
            $('.js_radio_true').attr('checked', false);
            $('.js_radio_false').attr('checked', true);

            for (sVar in aParams) {
                $('#js_true_' + sVar).attr('checked', true);
            }
        },
        quickReply: function () {
            if (function_exists('__callBackForumAddReply')) {
                __callBackForumAddReply();
            }

            $('#js_reply_process').html($.ajaxProcess(oTranslations['adding_your_reply']));
            $('#js_quick_reply_form .button').attr('disabled', true).addClass('disabled');

            $('#js_quick_reply_form').ajaxCall('forum.addReply');

            return false;
        },

        goAdvanced: function () {
            $('#js_advance_reply_textarea').val(Editor.getContent());
            $('#js_advance_reply_form').submit();
        },

        processReply: function (iPostId) {
            tb_remove();

            $.scrollTo('#post' + iPostId, 800);
        },

        deletePost: function (iPostId) {
            $Core.jsConfirm({message: oTranslations['are_you_sure']}, function () {
                $.ajaxCall('forum.deletePost', 'id=' + iPostId);
                $('#post' + iPostId).parent().html('<div class="valid_message" style="margin:0;">' + oTranslations['post_successfully_deleted'] + '</div>').fadeOut(5000);

                var iCnt = 0;
                $('.js_post_count').each(function () {
                    iCnt++;
                });
            }, function () {
            });


            return false;
        },

        deleteThread: function (iThread) {
            $Core.jsConfirm({message: oTranslations['are_you_sure_you_want_to_delete_this_thread_permanently']}, function () {
                $.ajaxCall('forum.deleteThread', 'thread_id=' + iThread);
            }, function () {
            });

            return false;
        },

        stickThread: function (iThread, iType) {
            $('.dropContent').hide();

            $.ajaxCall('forum.stickThread', 'thread_id=' + iThread + '&type_id=' + iType);

            return false;
        },

        closeThread: function (iThread, iType) {
            $('.dropContent').hide();

            $.ajaxCall('forum.closeThread', 'thread_id=' + iThread + '&type_id=' + iType);

            return false;
        },

        selected: function (oObj, iPostId) {
            if ($(oObj).hasClass('selected')) {
                var sCookie = getCookie('forum_quote');

                setCookie('forum_quote', sCookie.replace(iPostId + ',', ''));
                if ($('selected').length < 1) {
                    $('#btnGoAdvanced').val(this.sGoAdvanced);
                }
                $(oObj).removeClass('selected');
            }
            else {
                $(oObj).addClass('selected');
                this.sGoAdvanced = $('#btnGoAdvanced').val();
                $('#btnGoAdvanced').val(oTranslations['reply_multi_quoting']);
                setCookie('forum_quote', getCookie('forum_quote') + iPostId + ',');
            }

            return false;
        },

        processQuotes: function () {
            var sValue = getCookie('forum_quote');

            if (!empty(sValue)) {
                var aParts = explode(',', sValue);

                for (i in aParts) {
                    if (empty(aParts[i])) {
                        continue;
                    }

                    $('#js_forum_quote_' + aParts[i]).addClass('selected');
                }
            }
        },
        submitQuickReply: function (ele) {
            if (typeof CKEDITOR != "undefined" && CKEDITOR.instances.reply_text != "undefined") {
                CKEDITOR.instances.reply_text.updateElement();
            }
            $('#js_forum_reply_submit_btn').addClass('disabled');

            $(ele).ajaxCall('forum.addReply');
            return false;
        },
        resetQuickReply: function () {
          Editor.setId('reply_text').setContent('');
        },
        switchSubscribe: function(iSubscribe, iThreadId) {
            var ele = $('#js_reply_subscribe');

            ele.removeClass('item_selection_active').removeClass('item_selection_not_active');

            if (iSubscribe) {
                $('#js_unsubscribe_' + iThreadId).show();
                $('.js_thread_subscribe').hide();
                $('#js_subscribe_' + iThreadId).hide();
                ele.find('.item_is_active input').prop('checked', true);
                ele.addClass('item_selection_active');
            } else {
                $('#js_unsubscribe_' + iThreadId).hide();
                $('.js_thread_subscribe').show();
                $('#js_subscribe_' + iThreadId).show();
                ele.find('.item_is_not_active input').prop('checked', true);
                ele.addClass('item_selection_not_active');
            }
        }
    };

$Behavior.videoAttachment = function () {
    if ($('.forum_holder').length) {
        $('.is_toggled .level-2').show();
    }

    $('.forum_holder .toggle').on('click', function () {
        var t = $(this), parent = t.parents('.forum_holder:first');

        if (parent.hasClass('is_toggled')) {
            setCookie('forum_toggle_' + parent.data('forum-id'), 1);
            parent.removeClass('is_toggled'); // .find('.content').show();
            parent.find('.forum-app.level-2').slideUp('100');

            return;
        }

        parent.find('.forum-app.level-2').slideDown('100');
        parent.addClass('is_toggled'); // .find('.content').hide();
        deleteCookie('forum_toggle_' + parent.data('forum-id'));
    });

    var oVideoAttachments = $('span[id^=js_attachment_id_]');
    $.each(oVideoAttachments, function (i, selector) {
        sId = $(selector).attr('id');
        rId = /[0-9]+/;
        iId = rId.exec(sId).toString();
        $('#' + sId + ' a').attr('onClick', "$.ajaxCall('attachment.playVideo', 'attachment_id=" + iId + "', 'GET'); return false;");
    });
};
