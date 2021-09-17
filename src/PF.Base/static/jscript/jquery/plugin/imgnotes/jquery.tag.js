$Core.photo_tag =
    {
        aParams: {},
        sHtml: '',
        error: function(msg){
            window.parent.sCustomMessageString = msg;
            $(document).one('jsbox.closed',function(){
                $('#js_tag_photo').trigger('click');
            });
            tb_show('Error', $.ajaxBox('core.message', 'height=150&width=300'));
        },
        init: function (aParams) {

            this.aParams = aParams;
            notes = aParams['js_notes'];
            $(function () {
                if ($Core.photo_tag.aParams['id'] == '#js_photo_view_image') {
                    if ($($Core.photo_tag.aParams['id']).is(':hidden')) {
                        $Core.photo_tag.aParams['id'] = '#js_photo_view_image_small';
                    }
                }
                if ($($Core.photo_tag.aParams['id']).length <= 0) {
                    return;
                }
                $('.note , .notep, div#noteform').remove();
                if (isset(aParams['js_notes'])) {
                    $($Core.photo_tag.aParams['id']).imgNotes();
                }
                $(aParams['tag_link_id']).unbind('click').on('click', function () {
                    $($Core.photo_tag.aParams['id']).imgAreaSelect({
                        onInit: showaddnote,
                        onSelectChange: showaddnote,
                        x1: 5,
                        y1: 5,
                        x2: 50,
                        y2: 50
                    });
                    var doneBtn = $('<a />', {
                        'text': oTranslations['done_tagging'],
                        'class': 'done-tagging-btn btn btn-default dont-unbind',
                        'href': 'javascript:void(0)'
                    }).on('click', function () {
                        $($Core.photo_tag.aParams['id']).imgAreaSelect({remove: true});
                        $('.done-tagging-btn').remove();
                        $($Core.photo_tag.aParams['tagged_class']).removeClass('edit');
                        $($Core.photo_tag.aParams['active_class']).removeClass('active');
                        $('div#noteform').hide();
                    });

                    $($Core.photo_tag.aParams['id']).closest('.photos_view').append(doneBtn);

                    return false;
                });

                if (isset($Core.photo_tag.aParams['in_photo']) && !empty($Core.photo_tag.aParams['notes'])) {
                    var sNotes = '';
                    var iNoteCount = 0;
                    $(aParams['notes']).each(function () {
                        iNoteCount++;
                        if(iNoteCount != 1) {
                            sNotes += '<span class="tag_sperator">, </span>';
                        }
                        sNotes += '<span onmouseover="$(\'#js_note_' + this.note_id + '\').addClass(\'note_hover\').show().css(\'z-index\', 10000);" onmouseout="$(\'#js_note_' + this.note_id + '\').removeClass(\'note_hover\').hide();">' + this.note + '</span>';
                    });
                    $($Core.photo_tag.aParams['in_photo']).html(sNotes).parent().show();
                }
                else {
                    $($Core.photo_tag.aParams['in_photo']).parent().hide();
                }

                this.sHtml = '<div id="noteform">';
                this.sHtml += '<form id="NoteAddForm" method="post" action="#" onsubmit="$(\'#noteform\').hide(); $(\'' + $Core.photo_tag.aParams['id'] + '\').imgAreaSelect({ hide: true }); $(this).ajaxCall(\'photo.addPhotoTag\'); return false;">';
                this.sHtml += '<input name="' + $Core.photo_tag.aParams['name'] + '[item_id]" type="hidden" value="' + $Core.photo_tag.aParams['item_id'] + '" />';
                this.sHtml += '<input name="' + $Core.photo_tag.aParams['name'] + '[x1]" type="hidden" value="" id="NoteX1" />';
                this.sHtml += '<input name="' + $Core.photo_tag.aParams['name'] + '[y1]" type="hidden" value="" id="NoteY1" />';
                this.sHtml += '<input name="' + $Core.photo_tag.aParams['name'] + '[height]" type="hidden" value="" id="NoteHeight" />';
                this.sHtml += '<input name="' + $Core.photo_tag.aParams['name'] + '[width]" type="hidden" value="" id="NoteWidth" />';
                this.sHtml += '<input name="' + $Core.photo_tag.aParams['name'] + '[photo_width]" type="hidden" value="" id="NotePhotoWidth" />';
                this.sHtml += '<input name="' + $Core.photo_tag.aParams['name'] + '[tag_user_id]" type="hidden" value="0" id="js_tag_user_id" />';
                this.sHtml += '<div class="table">' +
                    '<div class="table_right">' +
                    '<input placeholder=" ' + oTranslations['search_for_your_friends_dot'] + ' " autocomplete="off" size="20" class="v_middle form-control" type="text" name="' + $Core.photo_tag.aParams['name'] + '[note]" id="NoteNote" value="" onkeyup="$.ajaxCall(\'friend.searchDropDown\', \'search=\' + this.value + \'&amp;div_id=js_photo_tag_search_content&amp;input_id=js_tag_user_id&amp;text_id=NoteNote\', \'GET\');" />' +
                    '<div style="display:none;"><div class="input_drop_layer" id="js_photo_tag_search_content"></div></div>' +
                    '</div>' +
                    '</div>';

                this.sHtml += '' +
                    '<a class="cancel_tagging" href="#" onclick="$(\'#noteform\').hide(); $(\'' + $Core.photo_tag.aParams['id'] + '\').imgAreaSelect({ hide: true }); return false;"><i class="fa fa-remove"></i></a>';

                if ($Core.photo_tag.aParams['user_id'] && $('#js_photo_tag_user_id_' + $Core.photo_tag.aParams['user_id']).length == 0) {
                    this.sHtml += '<div class="extra_info"><a href="#" onclick="$(\'#js_tag_user_id\').val(\'' + $Core.photo_tag.aParams['user_id'] + '\'); $(\'#noteform\').hide(); $(\'' + $Core.photo_tag.aParams['id'] + '\').imgAreaSelect({ hide: true }); $(\'#NoteAddForm\').ajaxCall(\'photo.addPhotoTag\'); return false;">' + oTranslations['click_here_to_tag_as_yourself'] + '</a></div>';
                }
                this.sHtml += '</form>';
                this.sHtml += '</div>';

                $('body').prepend(this.sHtml);
                $('#js_photo_tag_search_content').click(function () {
                    $('#NoteAddForm').trigger('submit');
                });
            });
        }
    };

function showaddnote(img, area) {

    imgOffset = $(img).offset();
    imgWidth = $(img).width();
    form_left = parseInt(imgOffset.left) + parseInt(area.x1);
    form_width = 224;
    if ((area.x1 + form_width) > imgWidth) {
        form_left = form_left - (form_width - parseInt(area.width));
        $('#noteform').addClass('is_right');
    } else {
        $('#noteform').removeClass('is_right');
    }
    if(imgWidth <= 490) {
        form_left = parseInt(imgOffset.left);
        form_width = imgWidth;
    }
    form_top = parseInt(imgOffset.top) + parseInt(area.y1) + parseInt(area.height) + 5;
    $('#noteform').css({left: form_left + 'px', top: form_top + 'px', width: form_width + 'px'});
    $('#noteform').show();
    $('#noteform').css("z-index", 100);
    $('#NoteX1').val(area.x1);
    $('#NoteY1').val(area.y1);
    $('#NoteHeight').val(area.height);
    $('#NoteWidth').val(area.width);
    $('#NotePhotoWidth').val(imgWidth);
}