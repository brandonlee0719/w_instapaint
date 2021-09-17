/**
 * Created by phpFox on 5/25/17.
 */

$Ready(function () {
    if ($('#js_poll_form').length) {
        $('#js_poll_form').find('textarea[name="val[description]"]').prop('tabindex', 2);
    }
    $('.yn-dropdown-not-hide-poll').find('span[data-dismiss="dropdown"]').on('click', function () {
        $(this).parents('.dropdown').trigger('click');
    });

    $('.vote-member-inner').on('show.bs.dropdown', function () {
        $(this).parents('.answers_container').addClass('active');
    });
    $('.vote-member-inner').on('hide.bs.dropdown', function () {
        $(this).parents('.answers_container').removeClass('active');
    });
    $('.js_poll_expire').on('click',function(){
       $('.js_poll_expire_select_time').toggleClass('hide');
    });
});
var iMaxAnswers = 0;
var iMinAnswers = 0;

$Behavior.buildSortableAnswers = function () {
    $('.js_answers').each(function () {
        var sVal = $(this).val();
        var sOriginal = $(this).val();
        sVal = (sVal.replace(/\D/g, ""));
        // dummy check
        if ("Answer " + sVal + "..." == sOriginal) {
            // this is a default answer
            $(this).addClass('default_value');
            $(this).focus(function () {
                if ($(this).val() == sOriginal) {
                    $(this).val('');
                    $(this).removeClass('default_value');
                }
            });
            $(this).blur(function () {
                if ($(this).val() == '') {
                    $(this).val(sOriginal);
                    $(this).addClass('default_value');
                }
            });
        }

    });
}


function appendAnswer(sId) {
    iCnt = 0;
    $('.js_answers').each(function () {
        if ($(this).parents('.placeholder:visible').length)
            iCnt++;
    });
    if (iCnt >= iMaxAnswers) {
        tb_show(oTranslations['notice'], '', '', oTranslations['you_have_reached_your_limit']);
        $('#' + $sCurrentId).find('.js_box_close').show();
        return false;
    }


    //iCnt++;
    var oCloned = $('.placeholder:first').clone();
    oCloned.find('.js_answers').val(oTranslations['answer'] + ' ' + iCnt + '...');
    oCloned.find('.js_answers').addClass('default_value');
    oCloned.find('.hdnAnswerId').remove();

    var sInput = '<input type="text" class="form-control js_answers" size="30" value="" name="val[answer][][answer]"/>';
    oCloned.find('.class_answer').html(sInput);
    oCloned.find('.js_answers').attr('name', 'val[answer][' + (iCnt + 1) + '][answer]');
    var oFirst = oCloned.clone();

    var firstAnswer = oFirst.html();

    $(sId).closest('.poll-app').find('.sortable').append('<div class="placeholder ui-sortable-handle">' + firstAnswer + '</div>')
    return false;
}

/**
 * Uses JQuery to count the answers and validate if user is allowed one less answer
 * Effect used fadeOut(1200)
 */
function removeAnswer(sId) {
    /* Take in count hidden input */
    iCnt = -1;

    $('.js_answers').each(function () {
        iCnt++;
    });

    if (iCnt == iMinAnswers) {
        tb_show(oTranslations['notice'], '', '', oTranslations['you_must_have_a_minimum_of_total_answers'].replace('{total}', iMinAnswers));
        $('#' + $sCurrentId).find('.js_box_close').show();
        return false;
    }

    $(sId).parents('.placeholder').remove();

    return false;
}

$Behavior.poll_poll_appendClick = function () {
    $('.append_answer').click(function () {
        return false;
    });
};


$Core.poll =
    {
        aParams: {},
        iTotalQuestions: 1,

        init: function (aParams) {
            this.aParams = aParams;
        },

        build: function () {
        },

        deleteImage: function (iPoll) {
            $Core.jsConfirm({message: oTranslations['are_you_sure']}, function () {
                $.ajaxCall('poll.deleteImage', 'iPoll=' + iPoll);
            }, function () {
            });
            return false;
        },

        showFormForEditAgain: function ($answerId, iPollId) {
            $('.poll_question input.js_poll_answer').each(function () {
                if ($('#js_answer_' + $(this).val()).hasClass('user_answered_this'))
                    $(this).prop('checked', true);
                else
                    $(this).prop('checked', false);
            });
            if ($('#vote_list_' + iPollId)) {
                $('.poll_answer_button').hide();
                $('#vote_list_' + iPollId).hide();
            }
            if ($('#vote_' + iPollId)) {
                $('#vote_' + iPollId).show();
            }
        },

        hideFormForEditAgain: function (iPollId) {
            if ($('#vote_' + iPollId)) {
                $('#vote_' + iPollId).hide();
                $('.poll_answer_button').show();
            }
            if ($('#vote_list_' + iPollId)) {
                $('#vote_list_' + iPollId).show();
            }
        },

        submitPoll: function (bCanChangePoll, iPollId) {
            // check select poll
            var formId = '#js_poll_form_' + iPollId;
            if ($(formId).find(':checked').length > 0) {
                if (bCanChangePoll) {
                    $(this).parent().hide();
                }
                $(this).parents('.p_4:first').find('.js_poll_image_ajax:first').show();
                $('#js_poll_form_' + iPollId).ajaxCall('poll.addVote');
            }
            return false;
        },
        submitCustomPoll: function (ele) {
            if (typeof CKEDITOR != "undefined" && CKEDITOR.instances.description != "undefined") {
                CKEDITOR.instances.description.updateElement();
            }
            $('.js_poll_submit_button').addClass('disabled');

            $(ele).ajaxCall('poll.addCustom');
            return false;
        },
        dropzoneOnSuccess: function (ele, file, response) {
            $Core.poll.processResponse(ele, file, response);
        },

        dropzoneOnAddedFile: function (ele) {
            if ($Core.dropzone.instance['poll'].files.length > 1) {
                $Core.dropzone.instance['poll'].removeFile($Core.dropzone.instance['poll'].files[0]);
            }
        },
        processResponse: function (ele, file, response) {
            response = JSON.parse(response);

            // process error
            if (typeof response.error !== 'undefined') {
                tb_show(oTranslations['notice'], '', null, response.error);
                $('.js_poll_submit_button').removeAttr('disabled');
                return $Core.dropzone.setFileError(file, response.error);
            }

            // upload successfully
            if (typeof response.file !== 'undefined') {
                $('#image_path').val(response.file);
                $('#server_id').val(response.server_id);
                $('#js_poll_form').submit();
            }
        },
    };


$Behavior.design_page = function () {
    $('.js_cancel_change_poll_question').click(function () {
        if (document.getElementById('js_current_poll_question').style.display == '' || document.getElementById('js_current_poll_question').style.display == 'inline') {
            $('#js_current_poll_question').hide();
            $('#js_update_poll_question').show();
        }
        else {
            $('#js_current_poll_question').show();
            $('#js_update_poll_question').hide();
        }

        return false;
    });

    $('.js_current_poll_question').click(function () {
        // hide the label
        $('#js_current_poll_question').hide();
        // show the input field
        $('#js_update_poll_question').show();

        return false;
    });

    // Colorpicker
    $('#js_poll_design_wrapper ._colorpicker:not(.built)').each(function () {
        var t = $(this),
            h = t.parent().find('._colorpicker_holder');

        t.addClass('built');
        h.css('background-color', '#' + t.val());

        h.colpick({
            layout: 'hex',
            submit: false,
            onChange: function (hsb, hex, rgb, el, bySetColor) {
                t.val(hex);
                h.css('background-color', '#' + hex);
                var rel = t.data('rel');
                switch (rel) {
                    case 'backgroundChooser':
                        $('.poll_answer_container').css('backgroundColor', '#' + hex);
                        break;
                    case 'percentageChooser':
                        $('.poll_answer_percentage').css('backgroundColor', '#' + hex);
                        break;
                    default:
                        $('.poll_answer_container').css('border', '1px solid #' + hex);
                        break;
                }
            },
            onHide: function () {
                t.trigger('change');
            }
        });

    });

    // Answers
    $('.js_update_answer').click(function () {
        var iId = $(this).get(0).id.replace('js_text_answer_', '');
        $('#js_text_answer_' + iId).hide();
        $('#js_input_answer_' + iId).show();
    });

    $('.js_cancel_change_answer').click(function () {
        // get the id of the answer
        var iId = $(this).get(0).id.replace('js_cancel_change_answer_', '');

        // set the value of the input to the current value of the 'label', this step should not be needed
        // $('#js_input_answer_text_' + iId).val(trim($('#js_text_answer_' + iId).html()));

        // hide the input field
        $('#js_input_answer_' + iId).hide();

        // show the 'label' field
        $('#js_text_answer_' + iId).show();

        return false;
    });

    // this function cancels editing an answer
    $('.js_commit_change_answer').click(function () {
        // get the id of the answer
        var iId = $(this).get(0).id.replace('js_commit_change_answer_', '');

        // hide the input field
        $('#js_input_answer_' + iId).hide();
        // commit the changes with a beautiful ajax call
        $.ajaxCall('poll.changeAnswer', 'iId=' + iId + '&sTxt=' + $('#js_input_answer_' + iId).val());

        // show the 'label'
        $('#js_text_answer_' + iId).html(trim($('#js_input_answer_text_' + iId).val()));
        $('#js_text_answer_' + iId).show();

        // we need nothing else because the input is still there
        return false;
    });

}

function approvePoll(iPoll) {
    $Core.jsConfirm({}, function () {
        $.ajaxCall('poll.moderatePoll', 'iResult=0&iPoll=' + iPoll);
    }, function () {
    });
    return false;

}

function deletePoll(iPoll) {
    $Core.jsConfirm({
        message: oTranslations['are_you_sure_you_want_to_delete_this_poll']
    }, function () {
        $.ajaxCall('poll.moderatePoll', 'iResult=2&iPoll=' + iPoll);
    }, function () {
    });
    return false;
}