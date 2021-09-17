var $aMailOldHistory = {},
    $aNotificationOldHistory = {},
    $bNoCloseNotify = false,
    bCloseShareHolder = true,
    bCloseChangeCover = true,
    bCloseViewMoreFeed = true,
    pf_reposition = {
        id: 0,
        module: '',
        top: 0
    };

$Behavior.globalThemeInit = function () {
    p($(window).width());

    /**
     * ###############################
     * Global functions
     * ###############################
     */
    $('#holder_notify ul li').click(function () {
        $bNoCloseNotify = true;
    });

    $('.feed_share_on_item a').click(function () {
        bCloseShareHolder = false;
    });

    $('#js_change_cover_photo').click(function () {
        bCloseChangeCover = false;
    });

    // body clicks
    $((getParam('bJsIsMobile') ? '#content' : 'body')).click(function () {
        $('.header_bar_float').removeClass('active');
        /*
        if (bCloseShareHolder){
            $('.feed_share_on_holder').hide();
        }
        */
        $('.row_edit_bar_holder').hide();

        $('#header_menu_holder ul li ul').removeClass('active');
        $('#header_menu_holder ul li a').removeClass('active');

        if (!$bNoCloseNotify) {
            $('#holder_notify ul li').removeClass('is_active');
            $('#holder_notify ul li').find('.holder_notify_drop').hide();
        }

        $bNoCloseNotify = false;
        bCloseShareHolder = true;

        $('#section_menu_drop').hide();

        $('.welcome_info_holder').hide();
        $('.welcome_quick_link ul li a').removeClass('is_active');

        $('.moderation_drop').removeClass('is_clicked');
        $('.moderation_holder ul').hide();

        $('#header_sub_menu_search_input').parent().find('.js_temp_friend_search_form:first').hide();

        $('.feed_sort_holder').hide();

        if (bCloseChangeCover) {
            $('#cover_section_menu_drop').hide();
        }

        if (bCloseViewMoreFeed) {
            $('.view_more_drop').hide();
        }

        bCloseChangeCover = true;
        bCloseViewMoreFeed = true;
    });

    $('.feed_sort_order_link').click(function () {

        $('.feed_sort_holder').toggle();

        return false;
    });

  $('[data-action="feed_sort_holder_click"] ul li a').click(function() {
    var t = $(this);
    $('[data-action="feed_sort_holder_click"] ul li a').removeClass('active process');
    t.addClass('active process');
    $.ajaxCall('user.updateFeedSort', 'order=' + t.attr('rel'));

    return false;
  });

    $('.activity_feed_share_this_one_link').click(function () {

        var sRel = $(this).attr('rel');

        if ($(this).hasClass('is_active')) {
            $('.' + sRel).hide();
            $(this).removeClass('is_active');
        }
        else {
            $('.timeline_date_holder').hide();
            $('.activity_feed_share_this_one_link').removeClass('is_active');
            $('.' + sRel).show();
            $(this).addClass('is_active');
        }

        if (sRel == 'timeline_date_holder_share') {
            $.ajaxCall('feed.loadDropDates', '', 'GET');
        }

        return false;
    });

    $('#header_menu_holder li a.has_drop_down').click(function () {
        $('#holder_notify ul li').removeClass('is_active');
        $('#holder_notify ul li').find('.holder_notify_drop').hide();

        if ($(this).hasClass('active')) {
            $(this).parent().find('ul').removeClass('active');
            $(this).removeClass('active');
        }
        else {
            $('#header_menu_holder').find('ul').removeClass('active');
            $('#header_menu_holder').find('ul li a').removeClass('active');

            $(this).parent().find('ul').addClass('active');
            $(this).addClass('active');
        }

        return false;
    });

    $('#header_menu_holder ul li ul li a').click(function () {
        $('#header_menu_holder ul li ul').removeClass('active');
        $('#header_menu_holder ul li a').removeClass('active');
    });

    if (oCore['core.site_wide_ajax_browsing']) {
        $('.holder_notify_drop_link').click(function () {
            $(this).parents('.holder_notify_drop:first').hide();
            $(this).parents('.is_active:first').removeClass('is_active');

            return true;
        });
    }

    $('#holder_notify > ul > li > a').click(function () {
        if ($(this).attr('rel') == undefined) {
            return false;
        }

        var $oParent = $(this).parent();
        var $oChild = $oParent.find('.holder_notify_drop');

        $('#header_menu_holder ul li ul').removeClass('active');
        $('#header_menu_holder ul li a').removeClass('active');

        if ($oParent.hasClass('is_active')) {
            $oParent.removeClass('is_active');
            $oChild.hide();
        }
        else {
            $('#holder_notify ul li').removeClass('is_active');
            $('#holder_notify ul li').find('.holder_notify_drop').hide();

            $oParent.addClass('is_active');
            $oChild.show();
            if ($(this).attr('rel') == '_show') {
                return false;
            }
            /*
            if ($oChild.find('.holder_notify_drop_data').find('.holder_notify_drop_loader').length > 0)
            {
            */
            $Core.ajax($(this).attr('rel'),
                {
                    params:
                        {
                            'no_page_update': true
                        },
                    success: function ($sData) {
                        $oChild.find('.holder_notify_drop_data').html($sData);
                        if (oCore['core.site_wide_ajax_browsing']) {
                            $Core.loadInit();
                        }
                    }
                });
        }

        return false;
    });

    $('#section_menu_more').click(function () {
        $('#section_menu_drop').toggle();

        return false;
    });

    /**
     * ###############################
     * Global site search
     * ###############################
     */
    // $('#header_sub_menu_search_input').before('<div id="header_sub_menu_search_input_value" style="display:none;">' + $('#header_sub_menu_search_input').val() + '</div>');

    $('#header_sub_menu_search_input, #header_sub_menu_search_input_xs').focus(function () {

        $(this).parents('form:first').addClass('active');
        $(this).parent().find('#header_sub_menu_search_input').addClass('focus');
        // if ($(this).val() == $('#header_sub_menu_search_input_value').html()){
        $(this).val('');
        // if ((isset(oModules['friend']) ))
        // {
        $Core.searchFriendsInput.init({
            'id': 'header_sub_menu_search',
            'max_search': (getParam('bJsIsMobile') ? 5 : 10),
            'no_build': true,
            'global_search': true,
            'allow_custom': true,
            'panel_mode': true
        });
        $Core.searchFriendsInput.buildFriends(this);
        // }
        // }
    });

    $('#header_sub_menu_search_input').blur(function () {
        $(this).parents('form:first').removeClass('active');
        $(this).parent().find('#header_sub_menu_search_input').removeClass('focus');
    });
    if ((isset(oModules['friend']) )) {
        $('#header_sub_menu_search_input').keyup(function () {
            $Core.searchFriendsInput.getFriends(this);
        });
        $('#header_sub_menu_search_input_xs').keyup(function () {
            $Core.searchFriendsInput.getFriends(this);
        });
    }
    /**
     * ###############################
     * Global section search tool
     * ###############################
     */
    var v = window.location;
    if ($('.header_bar_menu').length && typeof(v.search) == 'string' && v.search.substring(0, 4) == '?s=1') {
        $('.header_bar_menu:first').addClass('focus');
    }
    $('.header_bar_search .txt_input').focus(function () {
        $(this).parents('.header_bar_menu:first').addClass('focus');
        $(this).addClass('input_focus');

        if ($('.header_bar_search_default').html() == $(this).val()) {
            $(this).val('');
        }
    }).blur(function () {
        // $(this).parent().find('.header_bar_search_input').removeClass('focus');
        if (empty($(this).val())) {
            $(this).val($('.header_bar_search_default').html());
            $(this).removeClass('input_focus');
        }
    });

    $('#js_comment_form_holder #text').keydown(function () {
        $Core.resizeTextarea($(this));
    });

    $('.welcome_quick_link ul li a').click(function (e) {
        if ($(this).hasClass('is_active')) {
            $(this).parent().find('.welcome_info_holder:first').hide();
            $(this).removeClass('is_active');

            return false;
        }

        if (oCore['core.site_wide_ajax_browsing'] == false) {
            if (this.href.indexOf('#') < 0) {
                window.location = this.href;
                return false;
            }
            else {
            }
        }
        else {
            if (this.href.indexOf('#') > (-1)) {
            }
            else {
                return false;
            }
        }
        var aParts = explode('#', this.href);
        var sTempCacheId = aParts[1].replace('.', '_');

        $('.welcome_info_holder').hide();
        $('.welcome_quick_link ul li a').removeClass('is_active');

        $(this).addClass('is_active');
        /*
        if ($(this).hasClass('is_cached'))
        {
            $(this).parent().find('.welcome_info_holder:first').show();

            return false;
        }
        */
        $(this).addClass('is_cached');

        var sRel = $(this).attr('rel');
        sCustomClass = '';
        if (!empty(sRel)) {
            sCustomClass = ' welcome_info_holder_custom';
        }

        $(this).parent().append('<div class="welcome_info_holder' + sCustomClass + '"><div class="welcome_info" id="' + sTempCacheId + '"></div></div>');

        $.ajaxCall(aParts[1], 'temp_id=' + sTempCacheId, 'GET');

        return false;
    });

    $('.profile_image').mouseover(function () {
        $(this).find('.p_4:first').show();
    });

    $('.profile_image').mouseout(function () {
        $(this).find('.p_4:first').hide();
    });
};

$Behavior.repositionCoverPhoto = function () {
    function repositionCoverPhoto(sModule, iId) {
        pf_reposition.module = sModule;
        pf_reposition.id = iId;
        $('.profiles_banner').addClass('editing');
        if ($('.pages_header_cover img').length) {
            height = $('.pages_header_cover img').height();
            width = $('.pages_header_cover img').width();
        } else {
            height = $('.profiles_banner_bg .cover img').height();
            width = $('.profiles_banner_bg .cover img').width();
        }
        $.globalVars = {
            originalTop: 0,
            originalLeft: 0,
            maxHeight: height - $("#cover_bg_container").height(),
            maxWidth: width - $("#cover_bg_container").width()
        };
        $('.pages_header_cover img, .profiles_banner_bg .cover img').draggable({
            axis: 'y',
            start: function (event, ui) {
                if (ui.position != undefined) {
                    $.globalVars.originalTop = ui.position.top;
                    $.globalVars.originalLeft = ui.position.left;
                }
            },
            drag: function (event, ui) {
                var newTop = ui.position.top;
                var newLeft = ui.position.left;
                if (ui.position.top < 0 && ui.position.top * -1 > $.globalVars.maxHeight) {
                    newTop = $.globalVars.maxHeight * -1;
                }
                if (ui.position.top > 0) {
                    newTop = 0;
                }
                if (ui.position.left < 0 && ui.position.left * -1 > $.globalVars.maxWidth) {
                    newLeft = $.globalVars.maxWidth * -1;
                }
                if (ui.position.left > 0) {
                    newLeft = 0;
                }
                ui.position.top = newTop;
                ui.position.left = newLeft;
            },
            stop: function (evt, ui) {
                pf_reposition.top = ui.position.top;
            }
        }).closest('.profiles_banner_bg').append('<div id="save_reposition_cover" class="btn btn-primary" data-cmd="profile.reposition_cover_save">' + oTranslations['save'] + '</div>');
    }

    window.repositionCoverPhoto = repositionCoverPhoto;
};