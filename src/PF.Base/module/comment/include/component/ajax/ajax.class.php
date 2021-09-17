<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Comment_Component_Ajax_Ajax
 */
class Comment_Component_Ajax_Ajax extends Phpfox_Ajax
{
    public function add()
    {
        $aVals = $this->get('val');
        $bPassCaptcha = true;
        if ($aVals['type'] != 'app' && Phpfox::hasCallback($aVals['type'], 'getAjaxCommentVar')) {
            $sVar = Phpfox::callback($aVals['type'] . '.getAjaxCommentVar');
            if ($sVar !== null) {
                Phpfox::getUserParam($sVar, true);
            }
        }

        if (!Phpfox::getUserParam('comment.can_post_comments')) {
            $this->html('#js_comment_process', '');
            $this->call("$('#js_comment_submit').removeAttr('disabled');");
            $this->hide('.js_feed_comment_process_form');
            $this->alert(_p('Your user group is not allowed to add comments.'));

            return false;
        }

        (($sPlugin = Phpfox_Plugin::get('comment.component_ajax_ajax_add_start')) ? eval($sPlugin) : false);

        if ((isset($bNoCaptcha) && isset($bCaptchaFailed)) && $bCaptchaFailed === true) {
            $this->html('#js_comment_process', '');
            $this->call("$('#js_comment_submit').removeAttr('disabled');");
            $this->alert(_p('captcha_failed_please_try_again'));

            return false;
        }

        if ($aVals['type'] == 'profile' && !Phpfox::getService('user.privacy')->hasAccess($aVals['item_id'],
                'comment.add_comment')) {
            $this->html('#js_comment_process', '');
            return false;
        }

        if (!Phpfox::getUserParam('comment.can_comment_on_own_profile') && $aVals['type'] == 'profile' && $aVals['item_id'] == Phpfox::getUserId() && empty($aVals['parent_id'])) {
            $this->html('#js_comment_process', '');
            $this->call("$('#js_comment_submit').removeAttr('disabled');");
            $this->alert(_p('you_cannot_write_a_comment_on_your_own_profile'));

            return false;
        }

        if (($iFlood = Phpfox::getUserParam('comment.comment_post_flood_control')) !== 0) {
            $aFlood = array(
                'action' => 'last_post', // The SPAM action
                'params' => array(
                    'field'      => 'time_stamp',
                    // The time stamp field
                    'table'      => Phpfox::getT('comment'),
                    // Database table we plan to check
                    'condition'  => 'type_id = \'' . Phpfox_Database::instance()->escape($aVals['type']) . '\' AND user_id = ' . Phpfox::getUserId(),
                    // Database WHERE query
                    'time_stamp' => $iFlood * 60
                    // Seconds);
                )
            );

            // actually check if flooding
            if (Phpfox::getLib('spam')->check($aFlood)) {
                if (isset($aVals['is_via_feed'])) {
                    $this->call('$(\'#js_feed_comment_form_' . $aVals['is_via_feed'] . '\').find(\'.js_feed_add_comment_button:first\').show();');
                    $this->call('$(\'#js_feed_comment_form_' . $aVals['is_via_feed'] . '\').find(\'.js_feed_comment_process_form:first\').hide();');
                } else {
                    $this->html('#js_comment_process', '');
                    $this->call("$('#js_comment_submit').removeAttr('disabled');");
                }

                $this->alert(_p('posting_a_comment_a_little_too_soon_total_time',
                    array('total_time' => Phpfox::getLib('spam')->getWaitTime())));

                return false;
            }
        }

        if (Phpfox::getLib('parse.format')->isEmpty($aVals['text'])
            || (isset($aVals['default_feed_value']) && $aVals['default_feed_value'] == $aVals['text'])) {
            if (isset($aVals['is_via_feed'])) {
                $this->call('$(\'#js_feed_comment_form_' . $aVals['is_via_feed'] . '\').find(\'.js_feed_add_comment_button:first\').show();');
                $this->call('$(\'#js_feed_comment_form_' . $aVals['is_via_feed'] . '\').find(\'.js_feed_comment_process_form:first\').hide();');
            } else {
                $this->html('#js_comment_process', '');
                $this->call("$('#js_comment_submit').removeAttr('disabled');");
            }

            $this->alert(_p('add_some_text_to_your_comment'));
            $this->hide('.js_feed_comment_process_form');

            return false;
        }

        if (Phpfox::isModule('captcha') && !isset($bNoCaptcha) && Phpfox::getUserParam('captcha.captcha_on_comment') && isset($aVals['image_verification']) && !Phpfox::getService('captcha')->checkHash($aVals['image_verification'])) {
            $bPassCaptcha = false;
            $this->call("$('#js_captcha_image').ajaxCall('captcha.reload', 'sId=js_captcha_image&sInput=image_verification');");
            $this->alert(_p('captcha_failed_please_try_again'), _p('error'));
        }

        if ($bPassCaptcha) {
            if (($mId = Phpfox::getService('comment.process')->add($aVals)) === false) {
                $this->html('#js_comment_process', '');
                $this->call("$('#js_comment_submit').removeAttr('disabled');");
                $this->hide('.js_feed_comment_process_form');
                $this->val('.js_comment_feed_textarea', '');

                return false;
            }

            $this->hide('#js_captcha_load_for_check');

            // Comment requires moderation
            if ($mId == 'pending_moderation') {
                $this->call("$('#js_comment_form')[0].reset();");
                $this->alert(_p('your_comment_was_successfully_added_moderated'));
            } else {
                $this->call('if (typeof(document.getElementById("js_no_comments")) != "undefined") { $("#js_no_comments").hide(); }');

                $aRow = Phpfox::getService('comment')->getComment($mId);

                $iNewTotalPoints = (int)Phpfox::getUserParam('comment.points_comment');
                $this->call('if ($Core.exists(\'#js_global_total_activity_points\')){ var iTotalActivityPoints = parseInt($(\'#js_global_total_activity_points\').html().replace(\'(\', \'\').replace(\')\', \'\')); $(\'#js_global_total_activity_points\').html(iTotalActivityPoints + ' . $iNewTotalPoints . '); }');

                if (isset($aVals['is_via_feed'])) {
                    Phpfox::getLib('parse.output')->setImageParser(array('width' => 200, 'height' => 200));
                    Phpfox_Template::instance()->assign(array(
                        'aComment'      => $aRow,
                        'bForceNoReply' => ($aRow['parent_id'] != 0),
                        'bIsAjaxAdd'    => 1
                    ))->getTemplate('comment.block.mini');
                    Phpfox::getLib('parse.output')->setImageParser(array('clear' => true));


                    if (isset($aVals['parent_id']) && $aVals['parent_id'] > 0) {
                        $this->html('#js_comment_form_holder_' . $aVals['parent_id'], '');
                        $this->html('#js_comment_form_holder_' . $aVals['parent_id'], '');
                        $this->append('#js_comment_children_holder_' . $aVals['parent_id'], $this->getContent(false));
                        $this->call('$("#js_comment_form_holder_' . $aVals['parent_id'] . '").detach().insertBefore("#js_comment_mini_child_holder_' . $aVals['parent_id'] . '");');
                        $this->call('$("#js_comment_children_holder_' . $aVals['parent_id'] . '").closest(".js_mini_feed_comment").addClass("has-replies");');
                    } else {
                        if (isset($aVals['is_in_view'])) {
                            $this->call('Editor.setContent(\'\');');
                        } else {
                            $this->call('$(\'#js_feed_comment_form_textarea_' . $aVals['is_via_feed'] . '\').val(\'\').addClass(\'js_comment_feed_textarea_focus\').removeAttr(\'style\');');
                        }

                        $this->call('$(\'#js_feed_comment_form_textarea_' . $aVals['is_via_feed'] . '\').parent().find(\'.js_feed_comment_process_form:first\').hide();');
                        $this->append('#js_feed_comment_view_more_' . $aVals['is_via_feed'], $this->getContent(false));
                    }
                } else {
                    Phpfox::getLib('parse.output')->setImageParser(array('width' => 500, 'height' => 500));
                    Phpfox_Template::instance()->assign(array(
                        'aRow'           => $aRow,
                        'bCanPostOnItem' => false
                    ))->getTemplate('comment.block.entry');
                    Phpfox::getLib('parse.output')->setImageParser(array('clear' => true));

                    if (isset($aVals['parent_id']) && $aVals['parent_id'] > 0) {
                        $this->call("$('#js_comment_form_{$aVals['parent_id']}').slideUp(); $('#js_comment_form_form_{$aVals['parent_id']}').html(''); $('#js_comment_parent{$aVals['parent_id']}').html('<div style=\"margin-left:30px;\">" . $this->getContent() . "</div>' + $('#js_comment_parent{$aVals['parent_id']}').html()).slideDown(); $('#js_comment_form')[0].reset();");
                    } else {
                        $this->call("$('#js_new_comment').html('" . $this->getContent() . "' + $('#js_new_comment').html()).slideDown(); $.scrollTo('#js_new_comment', 800); $('#js_comment_form')[0].reset();");
                    }

                    $this->call('$(\'#js_comment' . $aRow['comment_id'] . '\').find(\'.valid_message:first\').show().fadeOut(5000);');
                }
            }

            if (!isset($aVals['is_via_feed']) && Phpfox::isModule('captcha') && Phpfox::getUserParam('captcha.captcha_on_comment') && !isset($bNoCaptcha)) {
                $this->call("$('#js_captcha_image').ajaxCall('captcha.reload', 'sId=js_captcha_image&sInput=image_verification');");
            }
            (($sPlugin = Phpfox_Plugin::get('comment.component_ajax_ajax_add_passed')) ? eval($sPlugin) : false);
        }

        if (!isset($aVals['is_via_feed'])) {
            $this->html('#js_comment_process', '');
            $this->call("$('#js_comment_submit').removeAttr('disabled'); $('#js_reply_comment').val('0'); $('#js_reply_comment_info').html('');");
        }

        if (Phpfox::isModule('captcha') && !isset($bNoCaptcha) && Phpfox::getUserParam('captcha.captcha_on_comment')) {
            $this->call("$('#js_captcha_image').ajaxCall('captcha.reload', 'sId=js_captcha_image&sInput=image_verification');");
        }

        if ($aVals['type'] == 'photo') {
            $this->call("if (\$Core.exists('.js_feed_comment_view_more_holder')) { $('.js_feed_comment_view_more_holder')[0].scrollTop = $('.js_feed_comment_view_more_holder')[0].scrollHeight; }");
        }

        // get the onclick atrribute
        $sCall = "sOnClick = $('#js_feed_comment_view_more_link_" . $aVals['is_via_feed'] . " .comment_mini_link .no_ajax_link').attr('onclick');";
        // if there is "view all comments" link
        $sCall .= "if (typeof sOnClick != 'undefined') {";
        // regex to get the params for the ajax call in this
        $sCall .= "sPattern = new RegExp('(comment_)?type_id=([a-z]+_?[a-z]*)&(amp;)?item_id=[0-9]+&(amp;)?feed_id=[0-9]+', 'i');";
        // save the current ajax params
        $sCall .= "sOnClickParam = sPattern.exec(sOnClick);";
        // replace the params, adding the new "added" variable
        $sCall .= "sNewOnClick = sOnClick.replace(sOnClickParam[0], sOnClickParam[0]+'&added=1');";
        // replace the onclick attribute
        $sCall .= "$('#js_feed_comment_view_more_link_" . $aVals['is_via_feed'] . " .comment_mini_link .no_ajax_link').attr('onclick', sNewOnClick);";
        // if there is "view all comments" link
        $sCall .= "}";
        // call this JS code
        $this->call($sCall);

        $this->call('$Core.loadInit();');
    }

    public function browse()
    {
        Phpfox::getBlock('comment.view', array(
            'iTotal'  => $this->get('iTotal'),
            'sType'   => $this->get('sType'),
            'iItemId' => $this->get('iItemId'),
            'iPage'   => $this->get('page')
        ));

        (($sPlugin = Phpfox_Plugin::get('comment.component_ajax_browse')) ? eval($sPlugin) : false);

        $this->html('#js_comment_listing', $this->getContent(false));
        $this->call('$Core.loadInit(); $.scrollTo("#js_comment_listing", 340);');
    }

    public function getQuote()
    {
        $aRow = Phpfox::getService('comment')->getQuote($this->get('id'));
        if (isset($aRow['user_id'])) {
            $sText = Phpfox::getLib('parse.output')->ajax(str_replace("'", "\'", $aRow['text']));

            (($sPlugin = Phpfox_Plugin::get('comment.component_ajax_get_quote')) ? eval($sPlugin) : false);

            if (!isset($bHasPluginCall)) {
                $this->call("$('#text').val($('#text').val() + \"\\n\" + '[quote=" . $aRow['user_id'] . "]" . $sText . "[/quote]' + \"\\n\\n\"); $.scrollTo('#add-comment', 340); $('#text').focus();");
            }
        }
    }

    public function updateText()
    {
        $sTxt = $this->get('quick_edit_input');

        if (Phpfox::getLib('parse.format')->isEmpty($sTxt)) {
            $this->alert(_p('add_some_text_to_your_comment'));
            $this->call("$('#js_quick_edit_processingjs_comment_text_" . $this->get('comment_id') . "').hide();");
            return false;
        }

        if (Phpfox::getService('comment.process')->updateText($this->get('comment_id'), $sTxt)) {
            Phpfox::getLib('parse.output')->setImageParser(array('width' => 500, 'height' => 500));
            if (Phpfox::getParam('core.allow_html')) {
                $sTxt = Phpfox::getLib('parse.output')->parse(Phpfox::getLib('parse.input')->prepare($sTxt));
            } else {
                $sTxt = Phpfox::getLib('parse.output')->parse($sTxt);
            }
            Phpfox::getLib('parse.output')->setImageParser(array('clear' => true));

            $sTxt = Phpfox::getLib('parse.output')->replaceUserTag($sTxt);
            $this->call("$('#js_cache_quick_edit" .$this->get('comment_id'). "').remove();");
            $this->call("$('#js_cache_quick_editjs_comment_text_" . $this->get('comment_id') . "').remove();");
            $this->html('#' . $this->get('id'), $sTxt, '.highlightFade();');
            $this->html('#js_update_text_comment_' . $this->get('comment_id'),
                '<i>' . _p('last_update_on_time_stamp_by_full_name', array(
                    'time_stamp' => Phpfox::getTime(Phpfox::getParam('comment.comment_time_stamp'), PHPFOX_TIME),
                    'full_name'  => Phpfox::getUserBy("full_name")
                )) . '</i>');
        }
    }

    public function getText()
    {
        $aRow = Phpfox::getService('comment')->getCommentForEdit($this->get('comment_id'));

        (($sPlugin = Phpfox_Plugin::get('comment.component_ajax_get_text')) ? eval($sPlugin) : false);

        if (!isset($bHasPluginCall)) {
            if ($this->get('simple')) {
                $this->call("$('#js_quick_edit_id" . $this->get('id') . "').html('<textarea class=\"form-control\" name=\"quick_edit_input\" cols=\"90\" rows=\"10\" id=\"js_quick_edit" . $this->get('id') . "\">" . Phpfox::getLib('parse.output')->ajax($aRow['text']) . "</textarea>');");
                $this->call('$Core.attachFunctionTagger(\'#js_quick_edit' . $this->get('id') . "')");
            } else {
                $this->call("$('#js_quick_edit_id" . $this->get('id') . "').html('<div id=\"sJsEditorMenu\" class=\"editor_menu\" style=\"display:block;\">' + Editor.setId('js_quick_edit" . $this->get('id') . "').getEditor(true) + '</div><textarea class=\"form-control\" name=\"quick_edit_input\" cols=\"90\" rows=\"10\" id=\"js_quick_edit" . $this->get('id') . "\">" . Phpfox::getLib('parse.output')->ajax($aRow['text']) . "</textarea>');");
            }
        }
    }

    public function inlineDelete()
    {
        $sTypeId = $this->get('type_id');
        if (Phpfox::getService('comment.process')->deleteInline($this->get('comment_id'), $sTypeId)) {
            $this->slideUp('#js_comment_' . $this->get('comment_id'));
            if ($sTypeId && $iItemId = $this->get('item_id')) {
                $this->call('$Core.updateCommentCounter(\'' . $sTypeId . '\', ' . $iItemId . ', \'-\');');
            }
        }
    }

    public function moderateSpam()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('comment.can_moderate_comments', true);

        if (Phpfox::getService('comment.process')->moderate($this->get('id'), $this->get('action'), true)) {
            if ($this->get('inacp') || $this->get('action') == 'deny') {
                $this->hide('#js_comment' . $this->get('id'));
            }

            $this->call('if ($(\'#js_request_comment_count_total\').length > 0) { var iTotalCommentRequest = parseInt($(\'#js_request_comment_count_total\').html()); $(\'#js_request_comment_count_total\').html(\'\' + parseInt((iTotalCommentRequest - 1)) + \'\'); if ((iTotalCommentRequest - 1) == 0) { $(\'#js_request_comment_holder\').remove(); } requestCheckData(); }');
        }
    }

    public function moderate()
    {
        if (Phpfox::getService('comment.process')->moderate($this->get('id'), $this->get('action'))) {
            if ($this->get('action') == 'approve') {
                $this->hide('#js_comment_' . $this->get('id'))->call('$(\'#js_comment_message_' . $this->get('id') . '\').show(\'slow\').fadeOut(5000);');
            } else {
                $this->hide('#js_comment_' . $this->get('id'));
            }

            $this->call('if ($(\'#js_request_comment_count_total\').length > 0) { var iTotalCommentRequest = parseInt($(\'#js_request_comment_count_total\').html()); $(\'#js_request_comment_count_total\').html(\'\' + parseInt((iTotalCommentRequest - 1)) + \'\'); if ((iTotalCommentRequest - 1) == 0) { $(\'#js_request_comment_holder\').remove(); } requestCheckData(); }');
        }
    }

    public function viewAllComments()
    {
        $aComments = Phpfox::getService('comment')->getCommentsForFeed($this->get('comment_type_id'),
            $this->get('item_id'), 500, null, $this->get('comment_id'));

        foreach ($aComments as $aComment) {
            if (isset($aComment['children'])) {
                foreach ($aComment['children']['comments'] as $aMini) {
                    $this->template()->assign(array(
                        'aComment' => $aMini,
                        'aFeed'    => array('feed_id' => $this->get('item_id'))
                    ))->getTemplate('comment.block.mini');
                }
            }
        }

        $this->html('#js_comment_children_holder_' . $this->get('comment_id'), $this->getContent(false));
        $this->call('$("#comment_mini_child_view_holder_' . $this->get('comment_id') . '").parent().removeClass("comment_mini_child_holder_padding");');
        $this->remove('#comment_mini_child_view_holder_' . $this->get('comment_id'));
        $this->call('$Core.loadInit();');
    }

    public function viewMoreFeed()
    {
        $aComments = Phpfox::getService('comment')->getCommentsForFeed($this->get('comment_type_id'),
            $this->get('item_id'), Phpfox::getParam('comment.comment_page_limit'),
            ($this->get('total') ? (int)$this->get('total') : null), null, $this->get('feed_table_prefix', ''));

        if (!count($aComments)) {
            Phpfox_Error::set(_p('no_comments_found_dot'));

            return false;
        }

        // if the added parameter is 1
        if ($this->get('added') == 1) {
            // remove the last object, or it will be displayed as duplicate
            array_pop($aComments);
        }

        foreach ($aComments as $aComment) {
            $this->template()->assign(array(
                'aComment' => $aComment,
                'aFeed'    => array('feed_id' => $this->get('item_id'))
            ))->getTemplate('comment.block.mini');
        }

        if ($this->get('append')) {
            $this->prepend('#js_feed_comment_view_more_' . ($this->get('feed_id') ? $this->get('feed_id') : $this->get('item_id')),
                $this->getContent(false));

            Phpfox_Pager::instance()->set(array(
                    'ajax'    => 'comment.viewMoreFeed',
                    'page'    => Phpfox_Request::instance()->getInt('page'),
                    'size'    => $this->get('pagelimit'),
                    'count'   => $this->get('total'),
                    'phrase'  => _p('view_previous_comments'),
                    'icon'    => 'misc/comment.png',
                    'aParams' => array(
                        'comment_type_id'   => $this->get('comment_type_id'),
                        'item_id'           => $this->get('item_id'),
                        'append'            => true,
                        'pagelimit'         => $this->get('pagelimit'),
                        'total'             => $this->get('total'),
                        'feed_table_prefix' => $this->get('feed_table_prefix', '')
                    )
                )
            );

            $this->template()->getLayout('pager');

            $this->html('#js_feed_comment_pager_' . ($this->get('feed_id') ? $this->get('feed_id') : $this->get('item_id')),
                $this->getContent(false));
        } else {
            $this->hide('#js_feed_comment_view_more_link_' . ($this->get('feed_id') ? $this->get('feed_id') : $this->get('item_id')));
            $this->html('#js_feed_comment_view_more_' . ($this->get('feed_id') ? $this->get('feed_id') : $this->get('item_id')),
                $this->getContent(false));
        }

        $this->call('$Core.loadInit();');
    }

    public function getChildren()
    {
        $this->template()->assign(array(
                'bCanPostOnItem' => Phpfox::getUserParam(Phpfox::callback($this->get('type') . '.getAjaxCommentVar'))
            )
        );
        $this->_getChildren($this->get('comment_id'));

        $this->html('#js_comment_parent_view_' . $this->get('comment_id'),
            '<div style="margin-left:30px;">' . $this->getContent(false) . '</div>');
    }

    private function _getChildren($iId)
    {
        static $iCacheCnt = 0;

        $iCacheCnt++;

        list(, $aComments) = Phpfox::getService('comment')->get('cmt.*', array('cmt.parent_id = ' . $iId . ''),
            'cmt.time_stamp DESC');
        foreach ($aComments as $iKey => $aComment) {
            // Assign template vars for this comment.
            $this->template()->assign(array(
                    'aRow'           => $aComment,
                    'bCanPostOnItem' => ($iCacheCnt >= Phpfox::getParam('comment.total_child_comments') ? false : true)
                )
            );

            // Display the comment
            $this->template()->getTemplate('comment.block.entry');

            if ($aComment['child_total'] > 0) {
                echo '<div style="margin-left:30px;">' . "\n";
                $this->_getChildren($aComment['comment_id']);
                echo '</div>' . "\n";
            }
        }
    }
}