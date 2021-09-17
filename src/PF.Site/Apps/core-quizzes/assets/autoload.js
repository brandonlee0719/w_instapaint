$Ready(function () {

});


$Behavior.quizAddQuestionClick = function () {
    $('#js_add_question').off('click').click(function () {
        $Core.quiz.addQuestion();
        return false;
    });

    $('.add_class').click(function () {
        /* $(this).parents('.answer_holder:first .answer_parent').each(function()		{}); */

        var iCnt = 0;
        $('.answer_holder:first .answer_parent').each(function () {
            iCnt++;
        });

        $(this).parents('.answer_holder:first').append('<div class="answer_parent"><input type="text" class="answer" value="' + oTranslations['answer'] + ' ' + (iCnt + 1) + '..." /> <a href="#" class="remove_class">' + oTranslations['delete'] + '</a></div>');

        return false;
    });

    $('.remove_class').click(function () {
        $(this).parents('.answer_parent:first').remove();

        var iCnt = 0;
        $('.answer_holder:first .answer_parent').each(function () {
            iCnt++;
            $(this).find('.answer').val('' + oTranslations['answer'] + ' ' + iCnt + '...');
        });

        return false;
    });
}

$Core.quiz =
    {
        aParams: {},
        iTotalQuestions: 1,
        iFirstLoad: false,

        init: function (aParams) {
            if ($Core.quiz.iFirstLoad) return;
            $Core.quiz.iFirstLoad = true;
            this.aParams = aParams;
            if ($Core.quiz.aParams.isAdd == true) {
                $(document).ready(function () {
                    if ($Core.quiz.aParams.bErrors == false) {
                        for (i = 0; i < $Core.quiz.aParams.iMinQuestions; i++) {
                            $Core.quiz.addQuestion();
                        }
                    }
                });
            }
        },

        build: function () {

        },

        addQuestion: function () {
            var iCntQuestions = 0;
            $('.full_question_holder').each(function () {
                iCntQuestions++;
            });
            /* this conter has to be fixed in account of the hidden full_question_holder */
            iCntQuestions = iCntQuestions - 1;
            if (iCntQuestions >= $Core.quiz.aParams.iMaxQuestions) {
                tb_show(oTranslations['notice'], '', '', oTranslations['you_have_reached_the_maximum_questions_allowed_per_quiz']);
                $('#' + $sCurrentId).find('.js_box_close').show();
                return false;
            }

            /* append the full question */
            $('#hiddenQuestion').find(':text').each(function () {
                $(this).val('');
            });

            $('#js_quiz_container').append('' + $('#hiddenQuestion').html() + '');

            $Core.quiz.fixQuestionsIndexes();

            $('.full_question_holder:last').find('.hdnCorrectAnswer:first').val('1');
            $('.full_question_holder:last').find('.p_2:first').addClass('correctAnswer');


            return false;
        },

        submitForm: function () {
            $('#js_quiz_layout_default').html('');
            return true;
        },

        fixQuestionsIndexes: function () {
            var iCntQuestions = 1;
            /*
             * When editing a quiz, if you add another question this function breaks the
             * relation between the question id and the question text
             **/
            var oDate = new Date();
            // loop through every question:
            $('#js_quiz_container').find('.full_question_holder').each(function () {
                /* Count the answers inside this question */
                var iCntAnswers = 0;

                /* change the name of the question input */
                $(this).find('.question_title').attr('name', 'val[q][' + (iCntQuestions) + '][question]');

                /* Fix values inside each answer */
                $(this).find('.answer_parent').each(function () {
                    // set the name of the text input="text" properly
                    $(this).find('.answer').attr('name', 'val[q][' + (iCntQuestions) + '][answers][' + iCntAnswers + '][answer]');
                    $(this).find('.hdnCorrectAnswer').attr('name', 'val[q][' + iCntQuestions + '][answers][' + iCntAnswers + '][is_correct]');
                    $(this).find('.answer').attr('name', 'val[q][' + iCntQuestions + '][answers][' + iCntAnswers + '][answer]');
                    $(this).find('.hdnAnswerId').attr('name', 'val[q][' + iCntQuestions + '][answers][' + iCntAnswers + '][answer_id]');
                    $(this).find('.hdnQuestionId').attr('name', 'val[q][' + iCntQuestions + '][answers][' + iCntAnswers + '][question_id]');
                    if ($(this).find('.hdnQuestionId').val() == undefined) {
                        $(this).find('.hdnQuestionId').val(iCntQuestions + iCntAnswers + '123321');
                    }
                    iCntAnswers++;
                });
                /* fix the name for the title */
                $(this).find('.question_title').attr('name', 'val[q][' + iCntQuestions + '][question]');
                /* change the Question # for the current question number:
                 this has to be after the increment of the questions counter*/
                if (iCntQuestions <= $Core.quiz.aParams.iMinQuestions) {
                    $(this).find('.question_number_title').html($Core.quiz.aParams.sRequired + oTranslations['question_count'].replace('{count}', iCntQuestions));
                }
                else {
                    $(this).find('.question_number_title').html(oTranslations['question_count'].replace('{count}', iCntQuestions));
                    $(this).find("#removeQuestion").show();
                }
                /* increase the counter for the questions*/
                iCntQuestions++;
            });
            /* end of looping through questions*/
            /* Set the tab index properly*/
            var tabIndex = 1;
            $('.full_question_holder').each(function () {
                $(':input', this).not('input[type=hidden]').each(function () {
                    if ($(this).attr('type') == 'text' || $(this).attr('type') == 'textarea') {
                        $(this).attr('tabindex', tabIndex);
                        tabIndex++;
                    }
                });
            });


        },
        removeQuestion: function (oObj) {

            var iCntQuestions = 0;
            $('.full_question_holder').each(function () {
                iCntQuestions++;
            });

            /* this counter is tweaked because there is a hidden full_question_holder: */
            iCntQuestions = iCntQuestions - 1;
            if (iCntQuestions <= $Core.quiz.aParams.iMinQuestions) {
                tb_show(oTranslations['notice'], '', '', oTranslations['you_are_required_a_minimum_of_total_questions'].replace('{total}', $Core.quiz.aParams.iMinQuestions));
                $('#' + $sCurrentId).find('.js_box_close').show();
                return false;
            }
            $Core.quiz.iTotalQuestions = iCntQuestions;

            $(oObj).parents('.full_question_holder:first').remove();
            $Core.quiz.fixQuestionsIndexes();
            return false;
        },

        appendAnswer: function (oObj) {
            var iCnt = 0;
            var iTime = new Date();
            $(oObj).parent('.answer_parent').parent('.answer_holder').find('.answer_parent').each(function () {
                iCnt++;
            });
            if (iCnt >= $Core.quiz.aParams.iMaxAnswers) {
                tb_show(oTranslations['notice'], '', '', oTranslations['you_have_reached_the_maximum_answers_allowed_per_question']);
                $('#' + $sCurrentId).find('.js_box_close').show();
                return false;
            }

            var parentLast = $(oObj).parents('.answers_holder').find('.answer_parent:first').clone(),
            /* now we re-set the info for the new answer */
                iQuestionId = parentLast.find('.hdnQuestionId').val(),
                iNextAnswer = iQuestionId + parentLast.find('.hdnAnswerId').val() + '' + 123 + '' + iTime.getMilliseconds();

            parentLast.find('.hdnAnswerId').attr('name', 'val[q][' + iQuestionId + '][answers][' + iNextAnswer + '][answer_id]');
            parentLast.find('.answer').attr('name', 'val[q][' + iQuestionId + '][answers][' + iNextAnswer + '][answer]');
            var sAnswerValue = parentLast.find('.answer').val();
            parentLast.find('.answer').attr('value','');
            parentLast.find('.answer').attr('placeholder',oTranslations['answer']);
            parentLast.find('.hdnQuestionId').attr('name', 'val[q][' + iQuestionId + '][answers][' + iNextAnswer + '][question_id]');
            parentLast.find('.hdnCorrectAnswer').attr('name', 'val[q][' + iQuestionId + '][answers][' + iNextAnswer + '][is_correct]');
            parentLast.find('.hdnAnswerId').remove();
            parentLast.find('.hdnCorrectAnswer').val('0');

            parentLast = parentLast.html();
            if ($Core.quiz.aParams.isAdd == false) {
                parentLast.replace('"' + sAnswerValue + '"', '');
            }
            iCnt++;
            $(oObj).parent('.answer_parent').after('<div class="p_2 answer_parent" id="sample_' + iNextAnswer + '">' + parentLast + '</div>');
            this.fixQuestionsIndexes();
            return false;
        },

        deleteAnswer: function (oObj) {
            var iCnt = 0;

            $(oObj).parent('.answer_parent').parent('.answer_holder').find('.answer_parent').each(function () {
                iCnt++;
            });

            if ($Core.quiz.aParams.iMinAnswers < 2) {
                $Core.quiz.aParams.iMinAnswers = 2;
            }
            if (iCnt <= $Core.quiz.aParams.iMinAnswers) {
                tb_show(oTranslations['notice'], '', '', oTranslations['you_are_required_a_minimum_of_total_answers_per_question'].replace('{total}', $Core.quiz.aParams.iMinAnswers));
                $('#' + $sCurrentId).find('.js_box_close').show();
                return false;
            }
            $(oObj).parents('.answer_parent:first').remove();
            return false;
        },

        setCorrect: function (oObj) {
            $(oObj).parent('.answer_parent').parent('.answer_holder').find('.answer_parent').each(function () {
                $(this).removeClass('correctAnswer');
                $(this).find('.hdnCorrectAnswer').attr('value', 0);
            });


            $(oObj).parent('.answer_parent').find('.hdnCorrectAnswer').val(1);
            $(oObj).parent('.answer_parent').addClass('correctAnswer');

            return false;
        },

        checkGetFriends: function (oObj) {

            if ($('#privacy').val() == 4) {
                $Core.getFriends({
                    input: 'allow_list'
                });
            }
        },

        deleteImage: function (iQuiz) {
            $Core.jsConfirm({message: oTranslations['are_you_sure']}, function () {
                $.ajaxCall('quiz.deleteImage', 'iQuiz=' + iQuiz);
            }, function () {
            });
            return false;
        },

        dropzoneOnSuccess: function (ele, file, response) {
            // reset clickable button
            //$Core.quiz.resetClickableBtn(ele);
            $Core.quiz.processResponse(ele, file, response);
        },
        dropzoneOnAddedFile: function (ele) {
            if ($Core.dropzone.instance['quiz'].files.length > 1) {
                $Core.dropzone.instance['quiz'].removeFile($Core.dropzone.instance['quiz'].files[0]);
            }
            //$Core.quiz.resetClickableBtn(ele);
        },

        resetClickableBtn: function (ele) {
            // add insert photo button
            $('.dropzone-clickable').remove();
            ele.append(
                '<div class="dropzone-clickable" data-dropzone-button-id="dropzone-button-quiz"><span><i class="ico ico-photos-plus-o"></i></span></div>');

            // bind click event again
            $('.dropzone-clickable').unbind('click').click(function () {
                //$Core.dropzone.instance['quiz'].removeAllFiles();
                $('#quiz-dropzone').addClass('dz-started');
                var t = $(this);
                if (t.data('dropzone-button-id')) {
                    $('#' + t.data('dropzone-button-id')).trigger('click');
                }

                return false;
            });
        },
        processResponse: function (ele, file, response) {
            response = JSON.parse(response);

            // process error
            if (typeof response.error !== 'undefined') {
                tb_show(oTranslations['notice'], '', null, response.error);
                $('#js_quiz_submit_button').removeAttr('disabled');
                return $Core.dropzone.setFileError(file, response.error);
            }

            // upload successfully
            if (typeof response.file !== 'undefined') {
                $('#image_path').val(response.file);
                $('#server_id').val(response.server_id);
                $('#js_add_quiz_form').submit();
            }
        },
        submitAddForm: function (ele) {
            $(ele).attr('disabled', 'disabled');
            if (typeof $Core.dropzone.instance['quiz'] != 'undefined') {
                if ($Core.dropzone.instance['quiz'].getQueuedFiles().length) {
                    $Core.dropzone.instance['quiz'].processQueue();
                }
                else {
                    if (!$('#quiz-dropzone').find('.dz-preview.dz-error').length) {
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
            return false;
        }
    };

function plugin_addFriendToSelectList() {
    $('#js_allow_list_input').show();
}

$Core.quiz_moderate =
    {
        approve: function (iQuiz, iUser, sTitle) {
            // simple ajax call
            $('.moderation_block_' + iQuiz).ajaxCall('quiz.approve', 'iQuiz=' + iQuiz + '&iUser=' + iUser + '&sTitle=' + sTitle + '');

            return false;
        },

        deleteQuiz: function (iQuiz, sType) {
            // confirm delete
            $Core.jsConfirm({message: oTranslations['are_you_sure_you_want_to_delete_this_quiz']}, function () {
                $('.moderation_block_' + iQuiz).ajaxCall('quiz.delete', 'iQuiz=' + iQuiz + '&type=' + sType);
            }, function () {
            });

            return false;
        },

        decreaseCounters: function () {
            // this is just a visual tweak
            var iTotal = parseInt($('#js_pager_total').html());
            if (iTotal > 1) {
                // we decrease them
                $('#js_pager_total').html(parseInt(iTotal - 1));
                $('#js_pager_to').html(parseInt(iTotal - 1));
            }
            else {
                $('#js_pager_total').html('0');
                $('#js_pager_to').html('0');
            }
        }
    }

