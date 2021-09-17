if ($('#js_block_border__apps_phpfox_shoutbox_block_chat').length) {
    _getShoutboxContent();
    scroll_bottom()
}

function _getShoutboxContent() {
    var first = $(".msg_container_base>.base_receive").last().attr('data-value');
    var module_id = $('[data-name="parent_module_id"]').val();
    var item_id = $('[data-name="parent_item_id"]').val();
    if (typeof module_id == 'undefined') {
        return false;
    }
    var queryString = {
        'timestamp': first,
        'parent_module_id': module_id,
        'parent_item_id': item_id,
        'type': 'pull'
    };

    $.ajax(
        {
            type: 'POST',
            url: oParams.shoutbox_polling,
            data: queryString,
            timeout: 5 * 60 * 1000,//5 minutes
            success: function (data) {
                localStorage.shoutbox_data = jQuery.parseJSON(data);
                r_data(jQuery.parseJSON(data), false);
            }
        }
    ).always(function () {
        setTimeout(function () {
            _getShoutboxContent();
        }, oParams.shoutbox_sleeping_time);
        window.loadTime();
    });
}

function _convertTime(timestamp) {
    if (timestamp == 0) {
        return false;
    }
    var n = new Date();
    var c = new Date(timestamp * 1000);
    var now = Math.round(n.getTime() / 1000);
    var iSeconds = Math.round(now - timestamp);
    var iMinutes = Math.round(iSeconds / 60);
    var hour = Math.round(parseFloat(iMinutes) / 60.0);
    if (hour >= 48) {
        return false;
    }
    if (iMinutes < 1) {
        return oTranslations['just_now'];
    }
    if (iMinutes < 60) {
        if (iMinutes === 0 || iMinutes === 1) {
            return oTranslations['a_minute_ago'];
        }
        return iMinutes + ' ' + oTranslations['minutes_ago'];
    }
    if (hour < 24) {
        if (hour === 0 || hour === 1) {
            return oTranslations['a_hour_ago'];
        }
        return hour + ' ' + oTranslations['hours_ago'];
    }
    if (hour < 48 && ((n.getDay()) - 1) == c.getDay()) {
        return oTranslations['yesterday'] + ', ' + c.getHours() + ':' + c.getMinutes();
    }
}

function shoutboxSubmit() {
    var formTextEle = $('[data-toggle="shoutbox"][data-name="text"]');
    var n = new Date();
    var time_id = n.getTime();
    var new_id = 'new_shoutbox_' + time_id;
    var formTextElevalue = formTextEle.val().replace(/\s/g,'');
    if (formTextElevalue == '') {
        $("#shoutbox_error").modal();
    } else {
        var formItem = $('[data-toggle="shoutbox"]');
        var seconds = new Date().getTime() / 1000;
        var textElement = formTextEle.val();
        //Clean html
        textElement = textElement.replace(/<\/?[^>]+(>|$)/g, "");

        var appendContent = '<div class="row msg_container base_sent" id="shoutbox_message_' + new_id + '">';
        appendContent += "<div class=\"msg_container_row shoutbox-item  item-sent\">";
        appendContent += "<button type=\"button\" class=\"close\" data-toggle=\"shoutbox-dismiss\" data-value=\"" + new_id + "\"><i class=\"ico ico-close-circle\" aria-hidden=\"true\"></i></button>";
        appendContent += "<div class=\"item-outer can-delete\">";
        appendContent += "<div class=\"item-media-source\">";
        appendContent += $('#current_user_avatar').html();
        appendContent += "</div>";
        appendContent += "<div class=\"item-inner\">";
        appendContent += "<div class=\"title_avatar item-shoutbox-body  msg_body_sent \">";
        appendContent += "<div class=\"item-title \">";
        appendContent += "<a href=\"" + oParams.shoutbox_user_profile_link + "\" title=\"" + oParams.shoutbox_user_full_name + "\">";
        appendContent += oParams.shoutbox_user_full_name;
        appendContent += "</a>";
        appendContent += "</div>";
        appendContent += "<div class=\"messages_body item-message \">";
        appendContent += "    <div class=\"item-message-info\">";
        appendContent +=  textElement;
        appendContent += "</div>";
        appendContent += "</div>";
        appendContent += "</div>";
        appendContent += '<span class="message_convert_time item-time" data-id="' + seconds + '">';
        appendContent += oTranslations['sending_dot_dot_dot'] + '</span>';
        appendContent += "</div>";
        appendContent += "</div>";
        appendContent += "</div>";
        appendContent += "</div>";

        var ele = $('.msg_container_base');
        ele.append(appendContent);
        var queryString = {
            'type': 'push',
            'time_id': time_id
        };
        $(formItem).each(function (index) {
            var elementValue = $(this).val();
            queryString[$(this).attr("data-name")] = elementValue.replace(/<\/?[^>]+(>|$)/g, "");
        });
        //clear form text
        formTextEle.val('');
        $.ajax(
            {
                type: 'POST',
                url: oParams.shoutbox_polling,
                data: queryString,
                timeout: 5 * 60 * 1000,//5 minutes
                success: function (data) {
                    var result = jQuery.parseJSON(data);
                    $('[data-value="new_shoutbox_' + time_id + '"]').attr('data-value', result.id);
                    $('#new_shoutbox_' + time_id).attr('id', 'shoutbox_message_' + result.id);
                }
            }
        ).always(function () {
            $('[data-toggle="shoutbox-dismiss"]').click(function () {
                var id = $(this).attr("data-value");
                $.ajaxCall('shoutbox.delete', 'id=' + id, 'GET');
                $('#shoutbox_message_' + id).fadeOut();
            });
            window.loadTime();
        }).fail(function () {
            $("[data-id='" + seconds + "']").removeAttr('data-id').html('Error');
        });
        scroll_bottom()
    }
}

function scroll_bottom() {
    var div = $(".msg_container_base");
    div.scrollTop(div[0].scrollHeight);
}

function r_data(params, prepend) {
    // var params = localStorage.shoutbox_data;
    if (typeof params.shoutbox_id == "undefined") {
        return;
    }
    if ($('#shoutbox_message_' + params.shoutbox_id).length) {
        return;
    }
    var appendContent = '<div class="row msg_container base_receive" id="shoutbox_message_' + params.shoutbox_id + '" data-value="' + params.shoutbox_id + '">';
        appendContent += "<div class=\"msg_container_row shoutbox-item  item-receive\">";
        if (params.user_type == 'a') {
            appendContent += '<button type="button" class="close" data-toggle="shoutbox-dismiss" data-value="' + params.shoutbox_id + '"><i class="ico ico-close-circle" aria-hidden="true"></i></button>';
            appendContent += "<div class=\"item-outer can-delete\">";
        } else{
            appendContent += "<div class=\"item-outer\">";
        }
        appendContent += "<div class=\"item-media-source\">";
        appendContent += params.user_avatar;
        appendContent += "</div>";
        appendContent += "<div class=\"item-inner\">";
        appendContent += "<div class=\"title_avatar item-shoutbox-body  msg_body_receive \">";
        appendContent += "<div class=\"item-title \">";
        appendContent += "<a href=\"" + params.user_profile_link + "\" title=\"" + params.user_full_name  + "\">";
        appendContent += params.user_full_name;
        appendContent += "</a>";
        appendContent += "</div>";
        appendContent += "<div class=\"messages_body item-message \">";
        appendContent += "    <div class=\"item-message-info\">";
        appendContent +=  params.text;
        appendContent += "</div>";
        appendContent += "</div>";
        appendContent += "</div>";
        appendContent += '<span class="message_convert_time item-time" data-id="' + params.timestamp + '">'+ ((params.parsed_time != 'undefined') ? params.parsed_time : '') +'</span>';
        appendContent += "</div>";
        appendContent += "</div>";
        appendContent += "</div>";
        appendContent += "</div>";


    /*var appendContent = '<div class="row msg_container base_receive" id="shoutbox_message_' + params.shoutbox_id + '" data-value="' + params.shoutbox_id + '">';
    appendContent += "<div class=\"msg_container_row shoutbox-item  item-sent\">";
    if (params.user_type == 'a') {
        appendContent += '<button type="button" class="close" data-toggle="shoutbox-dismiss" data-value="' + params.shoutbox_id + '"><i class="fa fa-times" aria-hidden="true"></i></button>';
    }
    appendContent += "<div class=\"title_avatar  msg_avt_receive title=\"" + params.user_full_name + "\" data-toggle=\"tooltip\">";
    appendContent += "<div class=\"avatar\">";
    appendContent += params.user_avatar;
    appendContent += "</div>";
    appendContent += "<div class=\"title_fullname\">";
    appendContent += "<a href=\"" + params.user_profile_link + "\" title=\"" + params.user_full_name + "\">";
    appendContent += params.user_full_name;
    appendContent += "</a>";
    appendContent += "<br/>";
    appendContent += '<span class="message_convert_time" data-id="' + params.timestamp + '">'+ ((params.parsed_time != 'undefined') ? params.parsed_time : '') +'</span>';
    appendContent += "</div>";
    appendContent += "</div>";
    appendContent += "<div class=\"messages_body\">";
    appendContent += "<div class=\"messages msg_receive \">";
    appendContent += '<p>' + params.text + '</p>';
    appendContent += "</div>";
    appendContent += "</div>";
    appendContent += "</div>";
    appendContent += "</div>";*/
    ////
    var ele = $('.msg_container_base');
    if (prepend) {
        ele.prepend(appendContent);
    } else {
        ele.append(appendContent);
        scroll_bottom()
    }
    window.loadTime();
}

function s_data(params, prepend) {
    if (typeof params.shoutbox_id == "undefined") {
        return;
    }
    if ($('#shoutbox_message_' + params.shoutbox_id).length) {
        return;
    }
    var textElement = params.text;
    textElement = textElement.replace(/<\/?[^>]+(>|$)/g, "");

    var appendContent = '<div class="row msg_container base_sent" id="shoutbox_message_' + params.shoutbox_id + '" data-value="' + params.shoutbox_id + '">';
        appendContent += "<div class=\"msg_container_row shoutbox-item  item-sent\">";
        appendContent += '<button type="button" class="close" data-toggle="shoutbox-dismiss" data-value="' + params.shoutbox_id + '"><i class="ico ico-close-circle" aria-hidden="true"></i></button>';
        appendContent += "<div class=\"item-outer can-delete\">";
        appendContent += "<div class=\"item-media-source\">";
        appendContent += params.user_avatar;
        appendContent += "</div>";
        appendContent += "<div class=\"item-inner\">";
        appendContent += "<div class=\"title_avatar item-shoutbox-body  msg_body_sent \">";
        appendContent += "<div class=\"item-title \">";
        appendContent += "<a href=\"" + params.user_profile_link + "\" title=\"" + params.user_full_name  + "\">";
        appendContent += params.user_full_name;
        appendContent += "</a>";
        appendContent += "</div>";
        appendContent += "<div class=\"messages_body item-message \">";
        appendContent += "    <div class=\"item-message-info\">";
        appendContent +=  textElement;
        appendContent += "</div>";
        appendContent += "</div>";
        appendContent += "</div>";
        appendContent += '<span class="message_convert_time item-time" data-id="' + params.timestamp + '">'+ ((params.parsed_time != 'undefined') ? params.parsed_time : '') +'</span>';
        appendContent += "</div>";
        appendContent += "</div>";
        appendContent += "</div>";
        appendContent += "</div>";

    var ele = $('.msg_container_base');
    if (prepend) {
        ele.prepend(appendContent);
    } else {
        ele.append(appendContent);
        scroll_bottom()
    }
}

window.loadTime = function () {
    $('.message_convert_time').each(function (key) {
        if ($(this).attr('data-id') > 0) {
            var time = _convertTime($(this).attr('data-id'));
            if (time !== false) {
                $(this).html(time);
            }
        }
    });
};

$Ready(function () {

    var textarea = document.getElementById('shoutbox_text_message_field');
    if(textarea){
        textarea.addEventListener('keydown', autosize);     
        function autosize(){
          var el = this;
          setTimeout(function(){
            el.style.cssText = 'height:auto; padding:8px';
            // for box-sizing other than "content-box" use:
            // el.style.cssText = '-moz-box-sizing:content-box';
            el.style.cssText = 'height:' + el.scrollHeight + 'px';
          },0);
        }
    }
// $Behavior.shoutbox_init_event = function() {
    $('[data-name="shoutbox-submit"]').click(function () {
        shoutboxSubmit();
    });
    $('[data-toggle="shoutbox-dismiss"]').click(function () {
        var id = $(this).attr("data-value");
        $.ajaxCall('shoutbox.delete', 'id=' + id, 'GET');
        $('#shoutbox_message_' + id).fadeOut();
    });
    $('#shoutbox_text_message_field').keypress(function (e) {
        if (e.which == 13) {
            shoutboxSubmit();
            return false;
        }
    });
    $('.msg_container_base').on('scroll', function () {
        if (!$('#shoutbox_loading_new').length) {
            if ($(this).scrollTop() == 0) {
                var last = $(".msg_container_base>.msg_container").first().attr('data-value');
                var queryString = {
                    'last': last,
                    'parent_module_id': $('[data-name="parent_module_id"]').val(),
                    'parent_item_id': $('[data-name="parent_item_id"]').val(),
                    'type': 'more'
                };

                $.ajax(
                    {
                        type: 'POST',
                        url: oParams.shoutbox_polling,
                        data: queryString,
                        beforeSend: function () {
                            $('.msg_container_base').prepend('<div id="shoutbox_loading_new"><i class="fa fa-spinner fa-spin fa-2x fa-fw"></i></div>');
                        },
                        timeout: 5 * 60 * 1000,//5 minutes
                        success: function (data) {
                            var objectData = jQuery.parseJSON(data);
                            if (typeof objectData.empty != "undefined") {
                                $('#shoutbox_loading_new:not(".stop")').remove();
                                $('.msg_container_base').prepend('<div id="shoutbox_loading_new" class="stop"></div>');
                            } else {
                                $.each(objectData, function (key, value) {
                                    if (typeof value.type != "undefined") {
                                        if (value.type == 'r') {
                                            r_data(value, true);
                                        } else if (value.type == 's') {
                                            s_data(value, true);
                                        }
                                    }
                                });
                                $('[data-toggle="shoutbox-dismiss"]').click(function () {
                                    var id = $(this).attr("data-value");
                                    $.ajaxCall('shoutbox.delete', 'id=' + id, 'GET');
                                    $('#shoutbox_message_' + id).fadeOut();
                                });
                            }
                        }//success
                    }
                ).always(function () {
                    $('#shoutbox_loading_new:not(".stop")').remove();
                    window.loadTime();
                });
            }
        }
    });
    window.loadTime();
// };
});

$(document).on('keyup', '#shoutbox_text_message_field', function() {
  $('#pf_shoutbox_text_counter').html($(this).val().length);
});