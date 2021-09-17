$Behavior.forum_admin = function () {
    if ($('#js_form_actual_content').prop('built')) return true;
    $('#js_form_actual_content').prop('built',true).addClass('dont-unbind-children');
    $('.js_drop_down').click(function () {
        eleOffset = $(this).offset();
        aParams = $.getParams(this.href);
        $('#js_cache_menu').remove();

        $('body').prepend('<div id="js_cache_menu" style="position:absolute; left:' + eleOffset.left + 'px; top:' + (eleOffset.top + 15) + 'px; z-index:100; background:red;">' + $('#js_menu_drop_down').html() + '</div>');

        $('#js_cache_menu .link_menu li a').each(function () {
            this.href = '#?id=' + aParams['id'];
        });

        $('.dropContent').show();

        $('.dropContent').hover(function () {

            },
            function () {
                $('.dropContent').hide();
                $('.sJsDropMenu').removeClass('is_already_open');
            });

        return false;
    });

    $('.sortable ul').sortable({
            axis: 'y',
            update: function (element, ui) {
                var iCnt = 0;
                $('.sortable li input').each(function () {
                    iCnt++;
                    $(this).val(iCnt);
                });
            },
            opacity: 0.4
        }
    );
};

function plugin_userLinkClick(oObj) {
    if ($(oObj).parents('.js_cached_user_name:first').hasClass('row1')) {
        var iUserId = $(oObj).parents('.js_cached_user_name:first').get(0).id.replace('js_user_id_', '');

        $('.js_cached_user_name').removeClass('row_focus');
        $(oObj).parents('.js_cached_user_name:first').addClass('row_focus');
        $.ajaxCall('forum.getModerator', 'user_id=' + iUserId + '&forum_id=' + $Core.forum.getParam('id'));
        $('#js_actual_user_id').val(iUserId);
        $('#js_perm_title').html(oTranslations['moderator_permissions'] + ': ' + $(oObj).html() + ' - <a href="#" onclick="return $Core.forum.cancel();">' + oTranslations['cancel'] + '</a>');
    }
    else {
        window.location.href = oObj.href;
    }

    return false;
}

var core_forums_onchangeDeleteForumType = function (type) {
    if (type == 2)
        $('#forum_select').show();
    else
        $('#forum_select').hide();
};