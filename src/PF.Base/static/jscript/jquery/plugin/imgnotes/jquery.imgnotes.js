/**
 * imgnotes jQuery plugin
 * version 0.1
 *
 * Copyright (c) 2008 Dr. Tarique Sani <tarique@sanisoft.com>
 *
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * @URL      http://www.sanisoft.com/blog/2008/05/26/img-notes-jquery-plugin/
 * @Example  example.html
 *
 **/

//Wrap in a closure
(function ($) {
    $.fn.imgNotes = function (n) {
        if (undefined != n) {
            notes = n;
        }
        image = this;
        imgOffset = $(image).offset();
        imgWidth = $(image).width();
        $(notes).each(function () {
            appendnote(this);
        });
        $(image).hover(
            function () {
                $('.note, .notep').show();
            },
            function () {
                $('.note, .notep').hide();
            }
        );
        addnoteevents();
        $(window).resize(function () {
            $('.note , .notep').remove();
            imgOffset = $(image).offset();
            imgWidth = $(image).width();
            $(notes).each(function () {
                appendnote(this);
            });
            addnoteevents();
        });
    }

    function addnoteevents() {
        $('.note').hover(
            function () {
                $('.note').hide();
                $('.notep').hide();
                $(this).show();
                $(this).next('.notep').show();
                $(this).next('.notep').css("z-index", 10000);
            },
            function () {
                $('.note').hide();
                $('.notep').hide();
                $(this).next('.notep').css("z-index", 0);
            }
        );
        $('.notep').hover(
            function () {
                $('.note').hide();
                $('.notep').hide();
                $(this).show();
                $(this).css("z-index", 10000);
                $(this).prev('.note').show();
            },
            function () {
                $('.note').hide();
                $('.notep').hide();
                $(this).css("z-index", 10000);
            }
        );
    }

    function appendnote(note_data) {
        ratio = 1;
        if (note_data.photo_width)
            ratio = imgWidth / note_data.photo_width;
        note_left = parseInt(imgOffset.left) + ratio * parseInt(note_data.x1);
        note_top = parseInt(imgOffset.top) + ratio * parseInt(note_data.y1);
        note_p_top = note_top + ratio * (parseInt(note_data.height) + 5);
        note_area_div = $("<div id='js_note_" + note_data.note_id + "' class='note'></div>").css({
            left: note_left + 'px',
            top: note_top + 'px',
            width: ratio * note_data.width + 'px',
            height: ratio * note_data.height + 'px'
        });
        note_text = note_data.name;
        if (note_data.user_href) {
            note_a_user_area = $("<a style='position: absolute; width: 100%; height: 100%' onclick='$(\".note , .notep\").remove();' href='" + note_data.user_href + "'></a>");
            note_area_div.append(note_a_user_area);
            note_text = "<a onclick='$(\".note , .notep\").remove();' href='" + note_data.user_href + "'>" + note_text + "</a>";
        }
        note_text_div = $('<div class="notep" id="notep_' + note_data.note_id + '">' + note_text + '</div>').css({
            left: note_left + 'px',
            top: note_p_top + 'px'
        });
        $('body').append(note_area_div);
        $('body').append(note_text_div);
    }

// End the closure
})(jQuery);