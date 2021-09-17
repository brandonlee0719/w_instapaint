if (typeof can_post_video_on_profile == 'undefined') {
  var can_post_video_on_profile = 0;
}
if (typeof can_post_video_on_page == 'undefined') {
  var can_post_video_on_page = 0;
}
if (typeof can_post_video_on_group == 'undefined') {
  var can_post_video_on_group = 0;
}
if (typeof v_phrases == 'undefined') {
  var v_phrases = {};
}

var videoUpload = function(e) {
  $('#pf_video_add_error').hide();
  var pf_v_video_url = $('.pf_v_video_url');
  $('.process-video-upload').
      addClass('button_not_active').
      attr('disabled', true).
      val(v_phrases.uploading);
  pf_v_video_url.hide();

  $('#pf_video_id_temp').remove();
  pf_v_video_url.prepend(
      '<div><input id="pf_video_id_temp" type="hidden" name="val[pf_video_id]" value=""></div>');

  $('.pf_select_video').hide();
  $('.pf_v_video_submit').hide();

  $('.pf_process_form').show();
  $Core.Video.toggleLocationPlace(false);

  $('.pf_select_video .extra_info').addClass('hide_it');

  var f = $('.pf_select_video').parents('form:first');
  f.find('.upload_message_danger').remove();
  f.find('.error_message').remove();
  $('#pf_select_video_no_ajax').find('.upload_message_danger').remove();

  var files = e.target.files || e.dataTransfer.files;
  if (files.length) {
    for (var i = 0, f; f = files[i]; i++) {
      var file = f;
      var data = new FormData();
      data.append('ajax_upload', file);
      $.ajax({
        type: 'POST',
        url: PF.url.make('/video/upload'),
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        xhr: function() {
          var xhr = $.ajaxSettings.xhr();
          xhr.upload.onprogress = function(e) {
            var percent = Math.floor(e.loaded / e.total * 100);
            if (percent < 98) {
              $('.pf_process_form > span').width(percent + '%');
            }
          };
          return xhr;
        },
        headers: {
          'X-File-Name': encodeURIComponent(file.name),
          'X-File-Size': file.size,
          'X-File-Type': file.type,
          'X-Post-Form': $(($('#video_page_upload').length
              ? '#video_page_upload'
              : '#js_activity_feed_form')).getForm(),
        },
        error: function(error) {
          var eJson = {};
          if (typeof(error.responseJSON) !== 'undefined') {
            eJson = error.responseJSON;
          }
          $('#pf_video_id_temp').remove();
          $('.pf_select_video').show();
          $('.pf_process_form').hide();
          $('.pf_upload_form').show();
          var f = $('.pf_select_video').parents('form:first');
          if (typeof(eJson.error) !== 'undefined') {
            f.prepend('<div class="alert alert-danger upload_message_danger">' +
                eJson.error + '</div>');
            $('#pf_select_video_no_ajax').
                prepend('<div class="alert alert-danger upload_message_danger">' +
                    eJson.error + '</div>');
          }
          $('.select-video').val('');
          $('.pf_process_form > span').width('2%');
          $('.pf_select_video .extra_info').removeClass('hide_it');
        },
        success: function(data) {
          $Core.Video.toggleLocationPlace(true);
          $Core.Video.addShareVideoBtnInFeed();

          // upload form
          $('.pf_v_video_submit').show();

          $('.select-video').val('');
          $('.pf_process_form > span').width('100%');
          $('#pf_video_id_temp').val(data.id);
          $('.pf_video_caption').show();
          $('.pf_video_caption > div.table').show();

          $('.pf_process_form').hide();
          $('.pf_select_video').hide();
          $('.pf_video_message').show();

          // activity form
          $('.process-video-upload').
              removeClass('button_not_active').
              attr('disabled', false).
              val(v_phrases.share);
        },
      });
    }
  }
  else {
    $('#pf_video_id_temp').remove();
    $('.pf_select_video').show();
    $('.pf_process_form').hide();
    $('.pf_upload_form').show();
    $('.select-video').val('');
    $('.pf_process_form > span').width('2%');
    $('.pf_select_video .extra_info').removeClass('hide_it');
  }
};

var core_videos_onchangeDeleteCategoryType = function(type) {
  if (type == 2) {
    $('#category_select').show();
  }
  else {
    $('#category_select').hide();
  }
};

var core_videos_load_videos = function() {
  $('#page_route_video #content').show();
  $('#page_route_v #content').show();
};

$Event(function() {
  if ($('#page_route_video').length || $('#page_route_v').length) {
    core_videos_load_videos();
  }
});

$Ready(function() {
  if ($('#page_route_video').length || $('#page_route_v').length) {
    core_videos_load_videos();
  }

  // Upload routine for videos
  var m = $(
      '#page_core_index-member .activity_feed_form_attach, #panel .activity_feed_form_attach'),
      p = $('#page_pages_view .activity_feed_form_attach'),
      g = $('#page_groups_view .activity_feed_form_attach'),
      up = $('#page_profile_index .activity_feed_form_attach'),
      v = $('.select-video-upload'),
      b = $('#pf_upload_form_input');

  if (m.length && !v.length && can_post_video_on_profile == 1) {
    var html = '<li><a href="#" class="select-video-upload" rel="custom"><span class="activity-feed-form-tab">' +
        v_phrases.video + '</span></a></li>';
    m.append(html);
  }

  if (p.length && !v.length && can_post_video_on_page == 1) {
    var html = '<li><a href="#" class="select-video-upload" rel="custom"><span class="activity-feed-form-tab">' +
        v_phrases.video + '</span></a></li>';
    p.append(html);
  }

  if (g.length && !v.length && can_post_video_on_group == 1) {
    var html = '<li><a href="#" class="select-video-upload" rel="custom"><span class="activity-feed-form-tab">' +
        v_phrases.video + '</span></a></li>';
    g.append(html);
  }

  if (up.length && !v.length && can_post_video_on_profile == 1) {
    var html = '<li><a href="#" class="select-video-upload" rel="custom"><span class="activity-feed-form-tab">' +
        v_phrases.video + '</span></a></li>';
    up.append(html);
  }

  $('.activity_feed_form_attach a:not(.select-video-upload)').click(function() {
    $('.process-video-upload').remove();
    $('.activity_feed_form .upload_message_danger').remove();
    $('.activity_feed_form .error_message').remove();
  });

  $('.select-video-upload').click(function() {
    $('.activity_feed_form_attach a.active').removeClass('active');
    $(this).addClass('active');
    $('.global_attachment_holder_section').hide().removeClass('active');
    $('.activity_feed_form_button').show();
    $('.activity_feed_form_button_position').show();
    $('#activity_feed_submit').hide();
    $('.error_message').hide();
    // hide tag friend
    $('#btn_display_with_friend').hide();

    var btn_display_check_in = $('#btn_display_check_in');
    if (typeof can_checkin_in_video === 'undefined' || !can_checkin_in_video) {
      btn_display_check_in.hide();
      btn_display_check_in.removeClass('is_active');
      $('#js_add_location, #js_location_input, #js_location_feedback').hide();
      $('#hdn_location_name, #val_location_name ,#val_location_latlng').val('');
    }
    else {
      $('#btn_display_check_in').show();
    }

    $('#activity_feed_textarea_status_info').
        attr('placeholder', $('<div />').html(v_phrases.say).text());

    var l = $('#global_attachment_videos');
    if (l.length == 0) {
      var m = $(
          '<div id="global_attachment_videos" class="global_attachment_holder_section" style="display:block;"><div style="text-align:center;"><i class="fa fa-spin fa-circle-o-notch"></i></div></div>');
      $('.activity_feed_form_holder').prepend(m);

      $.ajax({
        url: PF.url.make('/video/share'),
        contentType: 'application/json',
        data: 'is_ajax_browsing=1',
        success: function(e) {
          m.html(e.content);
          m.find('._block').remove();

          $('.process-video-upload').remove();
          $Core.loadInit();
        },
      });
    }
    else {
      l.show();
    }

    $Core.Video.toggleLocationPlace(false);

    return false;
  });

  $('.process-video-upload').click(function() {
    var t = $(this);
    if (t.hasClass('button_not_active')) {
      return false;
    }
    var f = $(this).parents('form:first');
    t.addClass('button_not_active');
    t.hide();
    t.before(
        '<span class="form-spin-it video_form_processing"><i class="fa fa-spin fa-circle-o-notch"></i></span>');
    f.find('.error_message').remove();
    f.find('.upload_message_danger').remove();
    $('#pf_video_add_error_link').hide();
    $('#pf_video_add_error_link').html('');
    var form_params = f.serializeArray();
    var params = {};
    for (var i = 0; i < form_params.length; i++) {
      params[form_params[i].name] = form_params[i].value;
    }
    params['is_ajax_post'] = 1;
    $Core.ajax('v.shareFeed', {
      type: 'POST',
      params: params,
      success: function(e) {
        if (e) {
          e = $.parseJSON(e);
        }
        if (typeof(e.error) == 'string') {
          f.prepend(e.error);
          t.show();
          t.parent().find('.form-spin-it').remove();
          return;
        }
        $('.form-spin-it').remove();
        eval(e.run);
      },
    });
    return false;
  });

  if (b.length && !b.hasClass('built')) {
    b.addClass('built');
    b.prepend(
        '<input id="divFileInput" type="file" class="select-video feed-attach-form-file">');

    $('#divFileInput.select-video')[0].addEventListener('change', videoUpload);
  }

  var url_changed = function() {
    $('#pf_video_add_error').hide();
    $('.pf_v_video_url .extra_info').removeClass('hide_it');
    $('.pf_select_video').slideUp();
    $Core.Video.toggleLocationPlace(true);
    $Core.Video.addShareVideoBtnInFeed();
  };

  $('#video_url').keyup(function() {
    if ($(this).val().length === 0) {
      $('.pf_v_url_cancel').show();
    }
  });

  $('#video_url').change(function() {
    var url = $(this).val();
    url = url.replace(/\\|\'|\(\)|\"|$|\#|%|<>/gi, '');
    $('.pf_v_url_cancel').hide();
    $('.pf_v_url_processing').show();
    $Core.ajax('v.validationUrl',
        {
          type: 'POST',
          params:
              {
                url: url,
              },
          success: function(sOutput) {
            $('.pf_v_url_cancel').show();
            $('.pf_v_url_processing').hide();
            var oOutput = $.parseJSON(sOutput);
            if (oOutput.status == 'SUCCESS') {
              // activity form
              $('.process-video-upload').removeClass('button_not_active');

              // upload form
              $('.pf_v_video_submit').show();
              $('#pf_video_add_error_link').hide();
              $('#pf_video_add_error_link').html('');

              if (oOutput.title != '') {
                $('#title').val(oOutput.title);
              }
              if (oOutput.description != '') {
                $('#text').val(oOutput.description);
                if (typeof(CKEDITOR) !== 'undefined') {
                  if (typeof(CKEDITOR.instances['text']) !== 'undefined') {
                    oOutput.description = oOutput.description.replace(/(?:\r\n|\r|\n)/g, '<br />');
                    CKEDITOR.instances['text'].setData(oOutput.description);
                  }
                }
              }
              if (oOutput.default_image != '') {
                $('#video_default_image').val(oOutput.default_image);
              }
              if (oOutput.embed_code != '') {
                $('#video_embed_code').val(oOutput.embed_code);
              }
            }
            else {
              $('.pf_v_video_submit').hide();
              $('#pf_video_add_error_link').html(oOutput.error_message);
              $('#pf_video_add_error_link').show();
            }
          },
        });
  });

  $('#video_url').focus(url_changed);

  $('.pf_v_url_cancel').click(function() {
    $(this).parent().addClass('hide_it');
    // upload form
    var pf_video_add_error_link = $('#pf_video_add_error_link');
    pf_video_add_error_link.hide();
    pf_video_add_error_link.html('');
    $('.pf_select_video').slideDown();
    $('.pf_v_video_url #video_url').val('');
    var f = $(this).parents('form:first');
    f.find('.error_message').remove();
    f.find('.upload_message_danger').remove();
    $('.process-video-upload').hide();
    $Core.Video.toggleLocationPlace(false);
    $('#title').val('');
    $('#text').val('');
    if (typeof(CKEDITOR) !== 'undefined') {
      if (typeof(CKEDITOR.instances['text']) !== 'undefined') {
        CKEDITOR.instances['text'].setData('');
      }
    }
    $('#video_default_image').val('');
    $('#video_embed_code').val('');

    return false;
  });

  $('.pf_v_upload_cancel').click(function() {
    $(this).parent().addClass('hide_it');
    $('.pf_v_video_url').slideDown();
    var f = $(this).parents('form:first');
    f.find('.upload_message_danger').remove();
    f.find('.error_message').remove();
    $('#pf_select_video_no_ajax').find('.upload_message_danger').remove();
    $Core.Video.toggleLocationPlace(false);
    $('#pf_video_id_temp').val('');

    return false;
  });

  $('.pf_v_message_cancel').click(function() {
    // reset dropzone
    if (typeof $Core.dropzone.instance.v !== 'undefined') {
      $Core.dropzone.instance.v.removeAllSuccessFiles();
    }
    // hide success message
    $('#pf_v_share_success_message').fadeOut('fast', function() {
      // show upload form
      $('#js_upload_form').fadeIn();
      // share video via url
      $('#video_url').fadeIn();
    });

    $('.pf_v_video_url').show();
    $('.pf_select_video').show();
    $('.pf_video_message').hide();
    $('.process-video-upload').remove();
    $('#pf_video_id_temp').remove();
    $('#title').val('');

    return false;
  });

  $('.pf_v_success_continue').click(function() {
    $('#pf_v_success_message').hide();
    $('.pf_upload_form').slideDown();
    $('#title').val('');
    $('#text').val('');
    if (typeof(CKEDITOR) !== 'undefined') {
      if (typeof(CKEDITOR.instances['text']) !== 'undefined') {
        CKEDITOR.instances['text'].setData('');
      }
    }
    $('#video_default_image').val('');
    $('#video_embed_code').val('');

    return false;
  });

  $('.pf_v_upload_success_cancel').click(function() {
    var pf_video_id = $('#pf_video_id_temp').val();
    $('.pf_video_message').hide();
    $('.pf_v_video_submit').hide();
    $('.pf_video_caption').hide();
    $('.process-video-upload').remove();
    $('#pf_video_id_temp').remove();
    $('.pf_process_form > span').width('2%');
    $('.pf_v_video_url').slideDown();
    $('.pf_select_video').slideDown();
    $Core.ajax('v.cancelUpload',
        {
          type: 'POST',
          params:
              {
                pf_video_id: pf_video_id,
              },
          success: function(sOutput) {
          },
        });
    return false;
  });
});

$Core.Video = {
  toggleLocationPlace: function(useLocationAtStatusInfo) {
    var statusInfo = $('.activity_feed_form_button_status_info');
    if (statusInfo.length === 0) {
      return;
    }

    if (typeof useLocationAtStatusInfo === 'boolean' && useLocationAtStatusInfo) {
      statusInfo.show();
      $('#js_location_feedback').addClass('hide');
    } else {
      statusInfo.hide();
      $('#js_location_feedback').removeClass('hide').show();
    }
  },

  processUploadSuccess: function(ele, file, response) {
    // show user status textarea
    $Core.Video.toggleLocationPlace(true);
    // show submit button
    $('.pf_v_video_submit').show();
    // append to form
    $('#core_js_video_form').
        append('<div><input id="pf_video_id_temp" type="hidden" name="val[pf_video_id]" value="' +
            response.id + '"></div>');
    // append to share in feed form
    $('#js_activity_feed_form').
        append('<div><input id="pf_video_id_temp" type="hidden" name="val[pf_video_id]" value="' +
            response.id + '"></div>');

    $('.pf_video_caption').show();
    $('.process-video-upload').
        removeClass('button_not_active').
        attr('disabled', false).
        val(v_phrases.share);

    // remove error message
    $('[data-dz-errormessage]').html('');

    // add share video button
    this.addShareVideoBtnInFeed(true);
  },

  processAddedFile: function(ele, file, response) {
    // hide share video via url
    $('#video_url').slideUp();
  },

  addShareVideoBtnInFeed: function(btnIsActive) {
    $('.process-video-upload').remove();
    $('#activity_feed_submit').
        before('<a href="#" class="btn btn-gradient btn-sm btn-primary ' +
            (typeof btnIsActive === 'boolean' && btnIsActive
                ? ''
                : 'button_not_active') + ' process-video-upload">' +
            v_phrases.share + '</a>');
    $Core.loadInit();
  },

  processAfterSharingVideoInFeed: function() {
    $('#pf_v_share_success_message').show();
    $('.pf_video_message').hide();
    $('.pf_v_video_info').hide();
    $('#activity_feed_textarea_status_info').val('');
    $Core.Video.toggleLocationPlace(false);
    $('#js_location_feedback').text('').hide();
    $('.pf_video_caption').hide();
    $('#js_upload_form').slideUp();
  },

  processRemoveButton: function() {
    $('.pf_video_caption').fadeOut('fast');
    $Core.Video.toggleLocationPlace(false);
    $('#video_url').fadeIn();
    $('#pf_video_id_temp').remove();
    $('.pf_v_video_submit').hide();
  },
  
  playVideo: function(player) {
    if (player.paused) {
      player.play();
      $(player).parent().addClass('video-playing');
    } else {
      player.pause();
      $(player).parent().removeClass('video-playing');
    }
  }
};

$(document).on('click', '.dz-upload-again', function() {
  $('#v-dropzone .dropzone-button-v').trigger('click');
});