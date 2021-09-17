var $sFormAjaxRequest = null;
var $bButtonSubmitActive = true;
var $ActivityFeedCompleted = {};
var $sCurrentSectionDefaultPhrase = null;
var $sCssHeight = '40px';
var $sCustomPhrase = null;
var $sCurrentForm = null;
var $sStatusUpdateValue = null;
var $iReloadIteration = 0;
var $oLastFormSubmit = null;
var bCheckUrlCheck = false;
var bCheckUrlForceAdd = false;
var bAddingFeed = false;
var $sCacheFeedErrorMessage = [];

$Core.Like = {};
$Core.Like.Actions = {
  doLike: function(bIsCustom, sItemTypeId, iItemId, iParentId, oObj) {
    if ($(oObj).
            closest('.comment_mini_link_like').
            find('.like_action_unmarked').
            is(':visible')) {
      $(oObj).
          closest('.comment_mini_link_like').
          find('.like_action_marked').
          show();
      $(oObj).
          closest('.comment_mini_link_like').
          find('.like_action_unmarked').
          hide();
    }
    $(oObj).parent().find('.js_like_link_unlike:first').show();
    $(oObj).hide();
    $.ajaxCall('like.add', 'type_id=' + sItemTypeId + '&item_id=' + iItemId +
        '&parent_id=' + iParentId + '&custom_inline=' + bIsCustom, 'GET');
  },
};

$Core.isInView = function(elem, item) {
  if (!$Core.exists(elem)) {
    return false;
  }

  var docViewTop = $(window).scrollTop();
  var docViewBottom = docViewTop + $(window).height();

  var elemTop = $(elem).offset().top;
  var elemBottom = (elemTop + $(elem).height());
  if (item) {
    elemBottom = (elemBottom - parseInt(item));
  }

  return ((docViewTop < elemTop) && (docViewBottom > elemBottom));
};

$Core.resetActivityFeedForm = function() {
  if ($sCacheFeedErrorMessage.length > 0) {
    $('.activity_feed_form_share_process').hide();
    $('.activity_feed_form_button .button').removeClass('button_not_active');
    $('#activity_feed_upload_error').empty();
    $bButtonSubmitActive = true;
    $sCacheFeedErrorMessage.forEach(function(item, index) {
      $('#activity_feed_upload_error').
          append('<div class="error_message">' + item + '</div>').
          show();
    });

    $sCacheFeedErrorMessage = [];

    PF.event.trigger('on_show_cache_feed_error_message');
  }
  else {
    bAddingFeed = false;
    $('._load_is_feed').removeClass('active');
    $('#panel').hide();
    $('body').removeClass('panel_is_active');

    $('.activity_feed_form_attach li a').removeClass('active');
    $('.activity_feed_form_attach li:not(.share) a:first').addClass('active');
    $('.global_attachment_holder_section').hide();
    $('#global_attachment_status').show();
    $('.global_attachment_holder_section textarea').
        val('').
        css({height: $sCssHeight});

    $('.activity_feed_form_button_status_info').hide();
    $('.activity_feed_form_button_status_info textarea').val('');

    $Core.resetActivityFeedErrorMessage();

    $sFormAjaxRequest = $('.activity_feed_form_attach li a.active').
        find('.activity_feed_link_form_ajax').
        html();

    $Core.activityFeedProcess(false);

    $('.js_share_connection').val('0');
    $('.feed_share_on_item a').removeClass('active');

    $.each($ActivityFeedCompleted, function() {
      this(this);
    });

    $('#js_add_location, #js_location_input, .js_location_feedback').hide();

    if (typeof $Core.FeedTag !== 'undefined' &&
        typeof $Core.FeedTag.iFeedId !== 'undefined' &&
        $('#feed_input_tagged_' + $Core.FeedTag.iFeedId).length) {
      $('#feed_input_tagged_' + $Core.FeedTag.iFeedId).val('');
      $('.js_feed_tagged_items').html('');
      $('.js_tagged_review').html('').hide();
      $('.js_feed_compose_tagging').hide();
      $('.js_btn_display_with_friend').removeClass('is_active');
    }
    //remove e-gift after submit
    if ($('#js_core_egift_id').length) {
        $('#js_core_egift_id').val('');
    }
    $('.activity_feed_form_button_position').show();
    $('#hdn_location_name, #val_location_name ,#val_location_latlng').val('');
    $('#btn_display_check_in').removeClass('is_active');
  }

  // reset Share button
  $('#activity_feed_submit').removeClass('button_not_active').attr('disabled', false);
};

$Core.resetActivityFeedErrorMessage = function() {
  $('#activity_feed_upload_error').hide();
  $('#activity_feed_upload_error_message').html('');
};

$Core.resetActivityFeedError = function(sMsg) {
  $('.activity_feed_form_share_process').hide();
  $('.activity_feed_form_button .button').removeClass('button_not_active');
  $bButtonSubmitActive = true;
  $('#activity_feed_upload_error').
      html('<div class="alert alert-danger">' + sMsg + '</div>').
      show();
};

$Core.cacheActivityFeedError = function(sMsg) {
  $sCacheFeedErrorMessage.push(sMsg);
};

$Core.activityFeedProcess = function($bShow) {
  if ($bShow) {
    $bButtonSubmitActive = false;
    $('.activity_feed_form_share_process').show();
    $('.activity_feed_form_button .button').addClass('button_not_active');
  }
  else {
    $bButtonSubmitActive = true;
    $('.activity_feed_form_share_process').hide();
    $('.activity_feed_form_button .button').removeClass('button_not_active');
    $('.egift_wrapper').hide();
    if ($('.egift_wrapper').length > 0 && $('#egift_id')) {
      $('.egift_item').removeClass('eGiftHighlight');
      $('#egift_id').val('');
      $('.egift_wrapper').show();
    }
  }
};

$Core.addNewPollOption = function(iMaxAnswers, sLimitReached) {
  if (iMaxAnswers >= ($('#js_poll_feed_answer li').length + 1)) {
    $('.js_poll_feed_answer').
        append(
            '<li><input type="text" name="val[answer][][answer]" value="" size="30" class="js_feed_poll_answer v_middle" /></li>');
  }
  else {
    alert(oTranslations['you_have_reached_your_limit']);
  }

  return false;
};

$Core.forceLoadOnFeed = function() {
  if ($iReloadIteration > 0) {
    return;
  }

  if (!$Core.exists('#js_feed_pass_info')) {
    return;
  }

  $iReloadIteration++;
  $('#feed_view_more_loader').show();
  $('.global_view_more').remove();

  setTimeout(function() {$Core.loadMoreFeed($iReloadIteration);}, 1000);
};

$Core.loadMoreFeed = function(iReloadIteration) {
  var oLastFeed = $('.js_parent_feed_entry').last(),
      iLastFeedId = (oLastFeed) ? oLastFeed.attr('data-feed-id') : null,
      sForceFlavor = (oLastFeed && oLastFeed.attr('data-force-flavor')) ? '&force-flavor=' + oLastFeed.attr('data-force-flavor') : '';

  // ajax call to get new feeds
  $.ajaxCall('feed.viewMore', $('#js_feed_pass_info').html().replace(/&amp;/g, '&') + '&iteration="' + iReloadIteration + '"&last-feed-id=' + iLastFeedId + sForceFlavor, 'GET');
};

var postingFeedUrl = false;

$Core.handlePasteInFeed = function(oObj) {
  if ($(oObj).closest('#js_activity_feed_edit_form').length > 0) {
    return false;
  }

  if (postingFeedUrl) {
    return;
  }

  var value = $(oObj).val();
  var regrex = /(http[s]?:\/\/(www\.)?|ftp:\/\/(www\.)?|www\.){1}([0-9A-Za-z-\-\.@:%_\+~#=]+)+((\.[a-zA-Z])*)(\/([0-9A-Za-z-\-\.@:%_\+~#=\?])*)*/g;
  var match = value.match(regrex);
  if (!empty(match)) {
    setTimeout(function() {
      var newValue = $(oObj).val();
      var newMatch = newValue.match(regrex);
      if (empty(newMatch) || (match[0] != newMatch[0])) {
        return;
      }
      bCheckUrlCheck = true;
      postingFeedUrl = true;

      $('#activity_feed_submit').attr('disabled', 'disabled');

      $('.activity_feed_form_share_process').show();
      $(oObj).
          parent().
          append(
              '<div id="js_preview_link_attachment_custom_form_sub" class="js_preview_link_attachment_custom_form" style="margin-top:5px;"></div>');
      $Core.ajax('link.preview', {
        type: 'POST',
        params: {
          'no_page_update': '1',
          value: newMatch[0],
        },
        success: function($sOutput) {
          postingFeedUrl = false;
          $('.activity_feed_form_share_process').hide();
          if (substr($sOutput, 0, 1) == '{') {

          }
          else {
            $('#js_global_attach_value').val($(oObj).val());
            bCheckUrlForceAdd = true;
            /* bCheckUrlCheck = false; */
            $('#js_preview_link_attachment_custom_form_sub').html($sOutput);
          }
        },
      });
    }, 500);
  }
  else {
    $('#js_global_attach_value').val('');
    bCheckUrlForceAdd = false;
    /* bCheckUrlCheck = false; */
    $('#js_preview_link_attachment_custom_form_sub').remove();
  }
    $Core.resizeTextarea($(oObj));
};

/**
 * Editor on comments
 */
$Core.loadCommentButton = function() {
  $('.feed_comment_buttons_wrap div input.button_set_off').
      show().
      removeClass('button_set_off');
};

var __ = function(e) {
  $('.feed_stream[data-feed-url="' + e.url + '"]').replaceWith(e.content);
  $Core.loadInit();
};

$Core.resetFeedForm = function(f) {
  f.get()[0].reset();
  $('.feed_form_share').removeClass('.active');
  $('.feed_form_textarea textarea').removeClass('dont-unbind');
};

window.onerror = function(e) {
  var l = $('.feed_stream:not(.built)').length;
  if (l) {
    $('.feed_stream.built').each(function() {
      if ($(this).data('feed-url')) {
        //hide error
        //$(this).replaceWith('<div class="error_message">' + e + '</div>');
        // $Core.loadInit();
      }
    });
  }
};

var load_feed_entries = false;
var load_feed_action = function() {
  var total = $('.feed_stream:not(.built)').length, iteration = 0;
  $('.feed_stream:not(.built)').each(function() {
    var t = $(this);

    t.addClass('built');
    iteration++;
    if (iteration === 2) {
      return false;
    }

    var s = document.createElement('script');
    s.type = 'application/javascript';
    s.src = t.data('feed-url');
    document.head.appendChild(s);

    // return false;
  });
};

$Behavior.activityFeedProcess = function() {

  $('.comment-limit:not(.is_checked)').each(function() {
    var t = $(this);
    t.addClass('is_checked');
    var total = t.children('.js_mini_feed_comment').length;
    var limit = t.data('limit');
    var iteration = total;
    var totalHidden = 0;
    t.children('.js_mini_feed_comment').each(function() {
      var l = $(this);
      iteration--;
      if (iteration < limit) {
        return false;
      }

      totalHidden++;
      l.addClass('hidden');
    });

    if (totalHidden) {
      var cHolder = t.parent().find('.comment_pager_holder:first');
      cHolder.hide();
      var viewMore = $('<a href="#" class="load_more_comments dont-unbind">' +
          oTranslations['view_previous_comments'] + '</a>');
      cHolder.before(viewMore);
      viewMore.click(function() {
        t.find('.js_mini_feed_comment').removeClass('hidden');
        $(this).remove();
        cHolder.show();
        return false;
      });
    }
  });

  // $('.activity_feed_content_display:not(.is_built)').each(function() {
  // 	var t = $(this);
  //
  // 	t.addClass('is_built');
  // 	if (t.outerHeight() > 300) {
  // 		t.css('position', 'relative');
  // 		var onClick = "$(this).parent().css(\'max-height\', \'none\');
  // $(this).remove(); return false;"; if
  // (t.find('.js_view_more_parent').length > 0) { t.css('max-height',
  // '300px'); t.css('overflow', 'hidden'); t.find('.item_view_more').remove();
  // onClick =
  // "$(this).parents(\'.activity_feed_content_display:first\').css(\'max-height\',
  // \'none\').css(\'overflow\',
  // \'visible\');$(this).parents(\'.activity_feed_content_display:first\').find(\'.js_view_more_part\').hide();
  // $(this).parents(\'.activity_feed_content_display:first\').find(\'.js_view_more_full\').show();
  // $(this).remove(); return false;"; }  t.prepend('<a href="#"
  // class="feed_show_more_content no_ajax" onclick="' + onClick + '"> ' +
  // oTranslations['show_more'] + '</a>'); } });

  $('.comment_mini_link_like_empty').each(function() {
    var p = $(this).closest('.comment_mini_content_border');
  });

  if (!$Core.exists('#js_feed_content')) {
    // $iReloadIteration = 0;
    // return;
  }

  if ($Core.exists('.global_view_more') && ($(window).width() > 480)) {
    if ($Core.isInView('.global_view_more')) {
      $Core.forceLoadOnFeed();
    }

    $(window).scroll(function() {
      if ($Core.isInView('.global_view_more')) {
        $Core.forceLoadOnFeed();
      }
    });
  }

  $('.like_count_link').each(function() {
    var sHtml = $(this).parent().find('.like_count_link_holder:first').html();
    /*
    if (empty(sHtml)){
       $(this).parents('.activity_like_holder:first').hide();
    }
    */
  });

  $sFormAjaxRequest = $('.activity_feed_form_attach li a.active').
      find('.activity_feed_link_form_ajax').
      html();
  if (typeof Plugin_sFormAjaxRequest == 'function') {
    Plugin_sFormAjaxRequest();
  }

  if ($Core.exists('.profile_timeline_header')) {
    $(window).scroll(function() {
      if (isScrolledIntoView('.profile_timeline_header')) {
        $('.timeline_main_menu').removeClass('timeline_main_menu_fixed');
        $('#timeline_dates').removeClass('timeline_dates_fixed');
      }
      else {
        if (!$('.timeline_main_menu').hasClass('timeline_main_menu_fixed')) {
          $('.timeline_main_menu').addClass('timeline_main_menu_fixed');

          if ($('#content').height() > 600) {
            $('#timeline_dates').addClass('timeline_dates_fixed');
          }
        }
      }
    });
  }

  $('#global_attachment_status textarea').keyup(function() {
    $Core.handlePasteInFeed($(this));
  }).bind('paste', function() {
    var that = this;
    setTimeout(function() {
      $Core.handlePasteInFeed(that);
    }, 0);

  });

  $('#global_attachment_status textarea').keydown(function() {
    $Core.resizeTextarea($(this));
  });

  $('.activity_feed_form_button_status_info textarea').
      keydown(function() {$Core.resizeTextarea($(this));});

  $('#global_attachment_status textarea').focus(function() {
    var t = $(this);
    if (t.hasClass('_is_set')) {
      return;
    }
    // if ($(this).val() == $('#global_attachment_status_value').html())
    {
      t.addClass('_is_set');
      // $(this).val('');
      $('.activity_feed_form_button').show();
      $(this).addClass('focus');
      $('.activity_feed_form_button_status_info textarea').addClass('focus');
    }
  });

  $('.activity_feed_form_button_status_info textarea').focus(function() {
    var $sDefaultValue = $(this).val();
    var $bIsDefault = true;

    $('.activity_feed_extra_info').each(function() {
      if ($(this).html() == $sDefaultValue) {
        $bIsDefault = false;

        return false;
      }
    });

    if (($('#global_attachment_status textarea').val() ==
            $('#global_attachment_status_value').html() &&
            empty($sDefaultValue)) || !$bIsDefault) {
      // $(this).val('');
      $(this).css({height: '50px'});
      $(this).addClass('focus');
      $('#global_attachment_status textarea').addClass('focus');
    }
  });

  $('#js_activity_feed_form, #js_activity_feed_edit_form').submit(function() {
    if ($sCurrentForm == 'global_attachment_status') {
      var oStatusUpdateTextareaFilled = $('#global_attachment_status textarea');

      if ($sStatusUpdateValue == oStatusUpdateTextareaFilled.val()) {
        // oStatusUpdateTextareaFilled.val('');
      }
    }
    else {
      var oCustomTextareaFilled = $(
          '.activity_feed_form_button_status_info textarea');

      if ($sCustomPhrase == oCustomTextareaFilled.val()) {
        oCustomTextareaFilled.val('');
      }
    }

    if ($bButtonSubmitActive === false) {
      return false;
    }

    $Core.activityFeedProcess(true);
    if (typeof $sFormAjaxRequest === 'undefined' ||
        $sFormAjaxRequest === null) {
      return true;
    }

    $('.js_no_feed_to_show').remove();

    var sExtra = '';
    if (bCheckUrlForceAdd) {
      $('.activity_feed_form_button_status_info textarea').
          val($('#global_attachment_status textarea').val());
      $sFormAjaxRequest = 'link.addViaStatusUpdate';
      if ($('#js_activity_feed_edit_form').length > 0) {
        sExtra = 'force_form=1';
      }
    }
    bAddingFeed = true;

    var customAjax = $('#custom_ajax_form_submit');
    if (customAjax.length) {
      $(this).ajaxCall(customAjax.html(), sExtra);
    }
    else {
      $(this).ajaxCall($sFormAjaxRequest, sExtra);
    }

    if (bCheckUrlForceAdd) {
      $('#js_preview_link_attachment_custom_form_sub').remove();
      bCheckUrlForceAdd = false;
    }

    return false;
  });

  $('.activity_feed_form_attach li a').click(function() {
    $sCurrentForm = $(this).attr('rel');
    var toggleWithFriend = function(hide){
        if (hide) {
          $('.js_feed_compose_extra').hide();
          $('.js_tagged_review').hide();
          $('.js_btn_display_with_friend').hide().removeClass('is_active');
        } else {
          if ($('.js_tagged_review').html() != "") {
            $('.js_tagged_review').show();
            $('.js_feed_compose_extra').show();
            $('.js_btn_display_with_friend').addClass('is_active');
          }
          $('.js_btn_display_with_friend').show();
        }
    };
    if ($sCurrentForm == 'custom') {
      toggleWithFriend(true);
      $('.core-egift-wrapper').hide();
      return false;
    }

    if ($sCurrentForm == 'view_more_link') {
      $('.core-egift-wrapper').hide();
      $('.view_more_drop').toggle();

      return false;
    }
    else {
      $('.view_more_drop').hide();
    }

    if ($sCurrentForm == 'global_attachment_photo') {
      $('.js_location_feedback', '.activity_feed_form_holder').addClass('hide');
    }
    else {
      $('.js_location_feedback', '.activity_feed_form_holder').removeClass('hide');
    }

    $('.activity_feed_form_button_position').show();
    if ($sCurrentForm == 'global_attachment_status') {
      $('#btn_display_check_in').show();
    }
    else if (typeof $(this).data('allow-checkin') === 'undefined') {
      $('#btn_display_check_in').hide();
      $('#js_add_location, #js_location_input, .js_location_feedback').hide();
      $('#hdn_location_name, #val_location_name ,#val_location_latlng').val('');
      $('#btn_display_check_in').removeClass('is_active');
    }

    $('#js_preview_link_attachment_custom_form_sub').remove();
    $('#activity_feed_upload_error').hide();

    $('.global_attachment_holder_section').hide();
    $('.activity_feed_form_attach li a').removeClass('active');
    $(this).addClass('active');

    if ($(this).find('.activity_feed_link_form').length > 0) {
      $('#js_activity_feed_form').
          attr('action', $(this).find('.activity_feed_link_form').html()).
          attr('target', 'js_activity_feed_iframe_loader');
      $sFormAjaxRequest = null;
      if (empty($('.activity_feed_form_iframe').html())) {
        $('.activity_feed_form_iframe').
            html(
                '<iframe id="js_activity_feed_iframe_loader" name="js_activity_feed_iframe_loader" height="200" width="500" frameborder="1" style="display:none;"></iframe>');
      }
    }
    else {
      $sFormAjaxRequest = $(this).find('.activity_feed_link_form_ajax').html();
    }

    $('#' + $(this).attr('rel')).show();
    $('.activity_feed_form_holder_attach').show();
    $('.activity_feed_form_button').show();

    var $oStatusUpdateTextarea = $('#global_attachment_status textarea');
    var $sStatusUpdateTextarea = $oStatusUpdateTextarea.val();
    $sStatusUpdateValue = $('#global_attachment_status_value').html();

    var $oCustomTextarea = $('.activity_feed_form_button_status_info textarea');
    var $sCustomTextarea = $oCustomTextarea.val();

    $sCustomPhrase = $(this).find('.activity_feed_extra_info').html();

    var $bHasDefaultValue = false;
    $('.activity_feed_extra_info').each(function() {
      if ($(this).html() == $sCustomTextarea) {
        $bHasDefaultValue = true;

        return false;
      }
    });

    if ($sCurrentForm != 'global_attachment_status') {
      $('.activity_feed_form_button_status_info').show();
      toggleWithFriend(true);
      $('.core-egift-wrapper').hide();
      if ((empty($sCustomTextarea) &&
              ($sStatusUpdateTextarea == $sStatusUpdateValue
                  || empty($sStatusUpdateTextarea)))
          ||
          ($sStatusUpdateTextarea == $sStatusUpdateValue && $bHasDefaultValue)
          || (!$bButtonSubmitActive && $bHasDefaultValue)
      ) {
        $oCustomTextarea.css({height: $sCssHeight}).
            val('').
            prop({placeholder: $sCustomPhrase});
      }
      else if ($sStatusUpdateTextarea != $sStatusUpdateValue &&
          $bButtonSubmitActive && !empty($sStatusUpdateTextarea)) {
        $oCustomTextarea.val($sStatusUpdateTextarea);
      }

      $('.activity_feed_form_button .button').addClass('button_not_active');
      $bButtonSubmitActive = false;
    }
    else {
      // hide `with` button in pages/groups
      if ($('body#page_pages_view').length === 0 && $('body#page_groups_view').length === 0 && $('body#page_event_view').length === 0) {
          toggleWithFriend(false);
      } else {
          toggleWithFriend(false);
      }
      $('.core-egift-wrapper').show();
      $('.activity_feed_form_button_status_info').hide();
      $('.activity_feed_form_button .button').removeClass('button_not_active');

      if (!$bHasDefaultValue && !empty($sCustomTextarea)) {
        $oStatusUpdateTextarea.val($sCustomTextarea);
      }
      else if ($bHasDefaultValue && empty($sStatusUpdateTextarea)) {
        $oStatusUpdateTextarea.val($sStatusUpdateValue).
            css({height: $sCssHeight});
      }

      $bButtonSubmitActive = true;
    }

    if ($(this).hasClass('no_text_input')) {
      $('.activity_feed_form_button_status_info').hide();
    }

    $('.activity_feed_form_button .button').show();
    $('#js_piccup_upload').hide();

    return false;
  });
};

$Behavior.activityFeedLoader = function() {
  if (empty($('.view_more_drop').html())) {
    $('.timeline_view_more').parent().hide();
  }

  /**
   * Click on adding a new comment link.
   */
  $('.js_feed_entry_add_comment').click(function() {
    $('.js_comment_feed_textarea').each(function() {
      if ($(this).val() == $('.js_comment_feed_value').html()) {
        $(this).removeClass('js_comment_feed_textarea_focus');
        $(this).val($('.js_comment_feed_value').html());
      }

      $(this).
          parents('.comment_mini').
          find('.feed_comment_buttons_wrap').
          hide();
    });

    $(this).
        parents('.js_parent_feed_entry:first').
        find('.comment_mini_content_holder').
        show();
    $(this).
        parents('.js_parent_feed_entry:first').
        find('.feed_comment_buttons_wrap').
        show();

    if ($(this).
            parents('.js_parent_feed_entry:first').
            find('.js_comment_feed_textarea').
            val() == $('.js_comment_feed_value').html()) {
      $(this).
          parents('.js_parent_feed_entry:first').
          find('.js_comment_feed_textarea').
          val('');
    }
    $(this).
        parents('.js_parent_feed_entry:first').
        find('.js_comment_feed_textarea').
        focus().
        addClass('js_comment_feed_textarea_focus');
    $(this).
        parents('.js_parent_feed_entry:first').
        find('.comment_mini_textarea_holder').
        addClass('comment_mini_content');

    var iTotalComments = 0;
    $(this).
        parents('.js_parent_feed_entry:first').
        find('.js_mini_feed_comment').
        each(function() {
          iTotalComments++;
        });

    if (iTotalComments > 2) {
      $.scrollTo($(this).
          parents('.js_parent_feed_entry:first').
          find('.js_comment_feed_textarea_browse:first'), 340);
    }

    return false;
  });

  /**
   * Comment textarea on focus.
   */
  $('.js_comment_feed_textarea').focus(function() {
    $Core.commentFeedTextareaClick(this);
  });

  $('#js_captcha_load_for_check_submit').submit(function() {

    if (function_exists('' + Editor.sEditor + '_wysiwyg_feed_comment_form')) {
      eval('' + Editor.sEditor + '_wysiwyg_feed_comment_form(this);');
    }

    $oLastFormSubmit.parent().
        parent().
        find('.js_feed_comment_process_form:first').
        show();
    $(this).ajaxCall('comment.add', $oLastFormSubmit.getForm());
    isAddingComment = false;
    return false;
  });

  $('.js_comment_feed_form').unbind().submit(function() {
    var t = $(this);

    t.addClass('in_process');

    if ($Core.exists('#js_captcha_load_for_check')) {

      $('div#js_captcha_load_for_check').removeClass('built');

      $('#js_captcha_load_for_check').addClass('built').css({
        top: t.offset().top,
        left: '50%',
        'margin-left': '-' +
        (($('#js_captcha_load_for_check').width() / 2) + 12) + 'px',
        display: 'block',
      }).detach().appendTo('body').find('.captcha').attr('id', 'js_captcha_image').css({opacity: 0.0});

      $('#js_captcha_load_for_check.built').find('')

      $('#js_captcha_image').
          ajaxCall('captcha.reload',
              'sId=js_captcha_image&sInput=image_verification');
      $oLastFormSubmit = $(this);

      $('div#js_captcha_load_for_check:not(.built)').remove();

      return false;
    }

    if (function_exists('' + Editor.sEditor + '_wysiwyg_feed_comment_form')) {
      eval('' + Editor.sEditor + '_wysiwyg_feed_comment_form(this);');
    }

    $(this).
        parent().
        parent().
        find('.js_feed_comment_process_form:first').
        show();
    $(this).ajaxCall('comment.add', null, null, null, function(e, self) {
      $(self).find('textarea').blur();
      isAddingComment = false;
      $('.js_feed_comment_process_form').fadeOut();
    });

    $(this).find('.error_message').remove();
    $(this).find('textarea:first').removeClass('dont-unbind');

    return false;
  });

  $('.js_comment_feed_new_reply').click(function() {

    var oParent = $(this).
        parents('.js_mini_feed_comment:first').
        children('.js_comment_form_holder:first');
    var oGrand = oParent.parent();
    oParent.detach().appendTo(oGrand);

    if ((Editor.sEditor == 'tiny_mce' || Editor.sEditor == 'tinymce') &&
        isset(tinyMCE) && isset(tinyMCE.activeEditor)) {
      $('.js_comment_feed_form').
          find('.js_feed_comment_parent_id:first').
          val($(this).attr('rel'));
      tinyMCE.activeEditor.focus();
      if (typeof($.scrollTo) == 'function') {
        $.scrollTo('.js_comment_feed_form', 800);
      }
      return false;
    }

    var sCommentForm = $(this).
        parents('.js_feed_comment_border:first').
        find('.js_feed_comment_form:first').
        html();
    oParent.html(sCommentForm);
    oParent.find('.js_feed_comment_parent_id:first').val($(this).attr('rel'));

    oParent.find('.js_comment_feed_textarea:first').focus();
    oParent.find('.js_comment_feed_textarea:first').
        attr('placeholder', oTranslations['write_a_reply']);
    $Core.commentFeedTextareaClick(
        oParent.find('.js_comment_feed_textarea:first'));

    $('.js_feed_add_comment_button .error_message').remove();

    oParent.find('.button_set_off:first').show().removeClass('button_set_off');
    oParent.closest('.js_mini_feed_comment').addClass('has-replies');

    $Core.loadInit();
    /*$Behavior.activityFeedLoader();*/

    return false;
  });
};

var isAddingComment = false;
$Core.commentFeedTextareaClick = function($oObj) {
  $($oObj).addClass('dont-unbind');
  $($oObj).blur(function() {
    $(this).removeClass('dont-unbind');
  });
  $($oObj).keydown(function(e) {
    if (isAddingComment) {
      return false;
    }

    if (e.which == 13) {
      setTimeout(function() {
        $('.chooseFriend').remove();
      }, 100);
      if(e.ctrlKey || e.metaKey){
          var val = this.value;
          var start = this.selectionStart;
          this.value = val.slice(0, start) + "\n" + val.slice(this.selectionEnd);
          this.selectionStart = this.selectionEnd = start + 1;
      }else{
          e.preventDefault();
          $($oObj).parents('form:first').trigger('submit');
          $($oObj).removeClass('dont-unbind');
          if (window.grecaptcha) {
              grecaptcha.reset();
          }
          // $($oObj).unbind();
          $Core.loadInit();
          isAddingComment = true;
          return false;
      }
    }
  }).keyup(function(){
      $Core.resizeTextarea($(this));
  }).on('paste',function(){
      $Core.resizeTextarea($(this));
  });

  $($oObj).addClass('js_comment_feed_textarea_focus').addClass('is_focus');
  $($oObj).
      parents('.comment_mini').
      find('.feed_comment_buttons_wrap:first').
      show();

  $($oObj).
      parent().
      parent().
      find('.comment_mini_textarea_holder:first').
      addClass('comment_mini_content');
  /*p($($oObj).parent().parent().html());*/
};

$Behavior.activityFeedAttachLink = function() {
  $('#js_global_attach_link').click(function() {
    $Core.activityFeedProcess(true);

    $Core.ajax('link.preview',
        {
          params:
              {
                'no_page_update': '1',
                value: $('#js_global_attach_value').val(),
              },
          type: 'POST',
          success: function($sOutput) {
            $('#js_global_attachment_link_cancel').show();

            if (substr($sOutput, 0, 1) == '{') {
              var $oOutput = $.parseJSON($sOutput);
              $Core.resetActivityFeedError($oOutput['error']);
              $bButtonSubmitActive = false;
              $('.activity_feed_form_button .button').
                  addClass('button_not_active');
            }
            else {
              $Core.activityFeedProcess(false);

              $('#js_preview_link_attachment').html($sOutput);
              $('#global_attachment_link_holder').hide();
            }
          },
        });
  });

  $('#js_global_attachment_link_cancel').click(function() {
    $('#js_global_attachment_link_cancel').hide();
  });
};

$ActivityFeedCompleted.link = function() {
  $bButtonSubmitActive = true;

  $('#global_attachment_link_holder').show();
  $('.activity_feed_form_button .button').removeClass('button_not_active');
  $('#js_preview_link_attachment').html('');
  $('#js_global_attach_value').val('http://');
};

$ActivityFeedCompleted.photo = function() {
  $bButtonSubmitActive = true;

  $('#global_attachment_photo_file_input').val('');
  $('#btn_display_check_in').show();
};

var sToReplace = '', buildingCache = false;

function attachFunctionTagger(sSelector) {
  if ($(sSelector).length && !buildingCache &&
      (typeof $Cache == 'undefined' || typeof $Cache.friends == 'undefined')) {
    buildingCache = true;
    $.ajaxCall('friend.buildCache', '', 'GET');
  }

  var customSelector = function() {
    return '_' + Math.random().toString(36).substr(2, 9);
  };
  var increment = 0;
  $(sSelector).each(function() {
    increment++;
    var t = $(this), selector = '_custom_' + customSelector() + '_' + increment;
    if (t.data('selector')) {
      t.removeClass(t.data('selector').replace('.', ''));
    }
    t.addClass(selector);
    t.data('selector', '.' + selector);
  });

  $(sSelector).keyup(function() {
    var t = $(this);
    var sInput = t.val();
    var iInputLength = sInput.length;
    var iAtSymbol = sInput.lastIndexOf('@');
    if (sInput == '@' || empty(sInput) || iAtSymbol < 0 ||
        iAtSymbol == (iInputLength - 1)) {
      $($(this).data('selector')).
          siblings('.chooseFriend').
          hide(function() {$(this).remove();});
      return;
    }

    var sNameToFind = sInput.substring(iAtSymbol + 1, iInputLength);

    /* loop through friends */
    var aFoundFriends = [], sOut = '';
    for (var i in $Cache.friends) {
      if ($Cache.friends[i]['full_name'].toLowerCase().
              indexOf(sNameToFind.toLowerCase()) >= 0) {
        var sNewInput = sInput.substr(0, iAtSymbol).
            replace(/\'/g, '&#39;').
            replace(/\"/g, '&#34;');
        sToReplace = sNewInput;
        aFoundFriends.push({
          user_id: $Cache.friends[i]['user_id'],
          full_name: $Cache.friends[i]['full_name'],
          user_image: $Cache.friends[i]['user_image'],
        });
        if ($Cache.friends[i]['has_image'] &&
            ($Cache.friends[i]['user_image'].indexOf('<img src') === -1)) {
          PF.event.trigger('urer_image_url', $Cache.friends[i]);

          // p($Cache.friends[i]['user_image']);

          $Cache.friends[i]['user_image'] = '<img src="' +
              $Cache.friends[i]['user_image'] +
              '" class="_image_32 image_deferred">';
        }

        sOut += '<div class="tagFriendChooser" onclick="$(\'' +
            $(this).data('selector') +
            '\').val(sToReplace + \'\' + (false ? \'@' +
            $Cache.friends[i]['user_name'] + '\' : \'[user=' +
            $Cache.friends[i]['user_id'] + ']' +
            $Cache.friends[i]['full_name'].replace(/\&#039;/g, '\\\'') +
            '[/user]\') + \' \').putCursorAtEnd();$(\'' +
            $(this).data('selector') +
            '\').siblings(\'.chooseFriend\').remove();"><div class="tagFriendChooserImage">' +
            $Cache.friends[i]['user_image'] + '</div><span>' +
            (($Cache.friends[i]['full_name'].length > 25)
                ? ($Cache.friends[i]['full_name'].substr(0, 25) + '...')
                : $Cache.friends[i]['full_name']) + '</span></div>';
        /* just delete the fancy choose your friend and recreate it */
        sOut = sOut.replace('\n', '').replace('\r', '');
      }
    }

    $($(this).data('selector')).siblings('.chooseFriend').remove();
    if (!empty(sOut)) {
      $($(this).data('selector')).
          after('<div class="chooseFriend" style="width: ' +
              $(this).parent().width() + 'px;">' + sOut + '</div>');
      $('.chooseFriend').mCustomScrollbar({
        theme: "minimal-dark",
      }).addClass('dont-unbind-children');
    }
  });
}

$Core.attachFunctionTagger = function(selector) {
  attachFunctionTagger(selector);
};

$Behavior.tagger = function() {
  var selectors = '#js_activity_feed_form > .activity_feed_form_holder > #global_attachment_status > textarea, .js_comment_feed_textarea, .js_comment_feed_textarea_focus, #js_activity_feed_form .activity_feed_form_button_status_info > textarea, #js_activity_feed_edit_form textarea';
  attachFunctionTagger(selectors);
  /*
  for (var i in aSelectors)
  {
    if ( $(aSelectors[i]).length >= 1)
    {
      var bChanged = false;
      $.each($(aSelectors[i]), function(key, value)
      {

        if ($(value).attr('id') != undefined)
        {
          aSelectors.push('#' + $(value).attr('id'));
          bChanged = true;
        }
      });
      if (bChanged)
      {
        aSelectors.splice(i,1);
      }
    }
  }

  for (var i in aSelectors)
  {
    var sSelector = aSelectors[i];

    if (sSelector == '#pageFeedTextarea' || sSelector == '#eventFeedTextarea'  || sSelector == '#profileFeedTextarea')
    {
      continue;
    }


    if ($(sSelector).length > 1)
    {
      $.each($(sSelector), function(key, value)
      {
        attachFunctionTagger($(this));
      });
    }
    attachFunctionTagger(sSelector);
  }
  */
};

$Core.checkNewFeedAfter = function(aFeedIds) {
  var iNewCounter = 0;
  for (var i = 0; i < aFeedIds.length; ++i) {
    if ($('#js_item_feed_' + aFeedIds[i]).length) {
      continue;
    }
    iNewCounter++;
  }
  if (!iNewCounter) {
    return;
  }

  // update number
  var btn = $('#feed_check_new_count_link');
  btn.text(btn.text().replace(/\d+/, iNewCounter));

  $('#feed_check_new_count').removeClass('hide');
};

$Core.updateShareFeedCount = function(module_id, item_id, str, number) {
  var holder = $('#js_feed_like_holder_' + module_id + '_' + item_id +
      ', #js_feed_mini_action_holder_' + module_id + '_' + item_id);
  if (holder.length > 0) {
    holder.each(function() {
      var counter = $(this).find('.feed-comment-share-holder .counter:first');
      if (counter.length > 0) {
        var count = counter.text();
        if (!count) {
          count = 0;
        }
        if (str == '+') {
          count = parseInt(count) + number;
        }
        else {
          count = parseInt(count) - number;
        }
        count = count <= 0 ? '' : count;
        counter.text(count);
      }
    });
  }
};

$Behavior.checkForNewFeed = function() {

  if (typeof window.isRegisteredCheckForNewFeed != 'undefined' ||
      typeof window.$iCheckForNewFeedsTime === 'undefined') {
    return;
  }

  window.isRegisteredCheckForNewFeed = true;
  var iCheckForNewFeedsTime = parseInt(window.$iCheckForNewFeedsTime) * 1000;

  function _isHomePage() {
    return !!$('body#page_core_index-member #js_feed_content').length
        && !$('#sHashTagValue').length;
  }

  function _getLastFeedUpdate() {
    //jquery .data() may cached in some case, so we can't use it.
    var val = $('#js_feed_content .row_feed_loop:not(".sponsor"):first').
        attr('data-feed-update');
    return val ? val : 0;
  }

  function _checkForNewFeed() {

    var $ele = $('#js_feed_content');

    if (bAddingFeed == true) {
      return;
    }
    if (!_isHomePage()) {
      return;
    }

    if ($ele.data('loading')) {
      return;
    }

    $ele.data('loading', true);
    $.ajaxCall('feed.checkNew', 'iLastFeedUpdate=' + _getLastFeedUpdate()).
        always(function() {
          $ele.data('loading', false);
        }).
        done(function() {

        });
  }

  window.setInterval(_checkForNewFeed, iCheckForNewFeedsTime);

  window.loadNewFeeds = function() {
    $('#js_new_feed_update').html('');
    $.ajaxCall('feed.loadNew', 'iLastFeedUpdate=' + _getLastFeedUpdate());
  };
};

$Ready(function() {
  $('.activity_feed_form_attach a:not(.select-video-upload)').click(function() {
    $('.process-video-upload').remove();
    $('.activity_feed_form .upload_message_danger').remove();
    $('.activity_feed_form .error_message').remove();
  });
});