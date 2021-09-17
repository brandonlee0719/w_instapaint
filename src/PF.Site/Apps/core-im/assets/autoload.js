var $Core_IM = {
  sound_path: '',
  host_failed: false,
  socket_built: false,
  thread_cnt: 0,
  thread_total: 0,
  thread_show: 0,
  users: '',
  load_first_time: true,
  scrollBottom: 0,
  im_debug_mode: false, // remember to turn it off
  is_mobile: false,
  deleted_users: [],
  chat_form_min_height: 0,
  chat_form_max_height: 150,
  file_preview: '[' + oTranslations['im_file'] + ']',
  host: '',
  searching: null,

  cmds: {
    'back_to_conversations': function(t, evt) {
      $('.pf-im-panel[data-thread-id="' + t.data('thread-id') + '"]').
          trigger('click');
    },
  },

  search_message_tmpl:
  '<div class="pf-im-search">' +
  '<div class="pf-im-search-top">' +
  '<div class="pf-im-search-title">' +
  '<i class="fa fa-search" aria-hidden="true"></i>&nbsp;${Title}' +
  '</div>' +
  '<div class="pf-im-search-action">' +
  '<span class="pf-im-search-close" title="Close search box"><i class="fa fa-times" aria-hidden="true"></i></span>' +
  '</div>' +
  '</div>' +
  '<div class="pf-im-search-main">' +
  '<input type="text" placeholder="${SearchPlaceholder}" id="pf-im-search-input" class="form-control">' +
  '<div class="pf-im-search-result"></div>' +
  '</div>' +
  '</div>',

  core_tmpl:
  '<span id="pf-im-total-messages">0</span>' +
  '<div id="pf-open-im"><i class="fa fa-comments"></i></div>' +
  '<div id="pf-im-wrapper"></div>' +
  '<div id="pf-im">' +
  '<i class="fa fa-spin fa-circle-o-notch"></i>' +
  '<div class="pf-im-title">' +
  '<i class="fa fa-comments"></i>${Title}' +
  '<span class="close-im-window" title="${CloseChatBox}"><i class="fa fa-times" aria-hidden="true"></i></span>' +
  '<span class="popup-im-window" title="${OpenNewTab}"><i class="fa fa-external-link" aria-hidden="true"></i></span>' +
  '</div>' +
  '<div class="_pf_im_friend_search">' +
  '<i class="fa fa-spinner fa-pulse" style="display: none;"></i><i class="fa fa-search"></i><input type="text" name="user" autocomplete="off" placeholder="${SearchFriendPlaceholder}" readonly="true">' +
  '</div>' +
  '<div class="pf-im-main"></div>' +
  '<div class="pf-im-search-user" style="display: none;"></div>' +
  '<audio id="pf-im-notification-sound" src="${SoundPath}" autostart="false" ></audio>' +
  '</div>',

  chat_load_more_tmpl:
      '<div class="pf-chat-row-loading"><i class="fa fa-spin fa-circle-o-notch"></i>&nbsp;${LoadingMessage}</div>',

  panel_tmpl:
  '<div class="pf-im-panel" data-friend-id="${UserId}" data-thread-id="${ThreadId}">' +
  '<div class="pf-im-panel-image">{{html PhotoLink}}</div>' +
  '<div class="pf-im-panel-content">' +
  '<span class="__thread-name" data-users="">${Name}</span>' +
  '<div class="pf-im-panel-preview"></div>' +
  '</div>' +
  '</div>',

  chat_link_tmpl:
  '<div><div class="pf-im-chat-link clearfix">' +
  '<div class="pf-im-chat-image">{{html LinkPreview}}</div>' +
  '<div class="pf-im-chat-content">' +
  '<a href="${Link}" target="_blank">${Title}</a>' +
  '<div class="pf-im-chat-description">${Description}</div>' +
  '</div>' +
  '</div></div>',

  chat_message_tmpl:
  '<div class="pf-chat-message ${OwnerClass}" ${StyleDisplayNone} data-user-id="${UserId}" id="${MessageTimestamp}">' +
  '<div class="pf-chat-image">{{html UserPhoto}}</div>' +
  '<div class="pf-chat-body clearfix">{{html ChatMessage}}' +
  '<time class="set-moment" data-time="${MessageTimestamp}"></time>' +
  '</div>' +
  '</div>',

  chat_action_tmpl:
  '<div class="chat-row-title">' +
  '<span class="chat-row-back" data-cmd="core-im" data-action="back_to_conversations" data-thread-id="${ThreadId}">' +
  '<i class="fa fa-chevron-left"></i>' +
  '</span>' +
  '<span class="chat-row-users"></span>' +
  '<span class="chat-row-close">' +
  '<i class="fa fa-bell" aria-hidden="true" id="chat-action-noti" title="${ThreadNotification}"></i>&nbsp;&nbsp;' +
  '<i class="fa fa-search" aria-hidden="true" id="chat-action-search" title="${SearchThread}"></i>&nbsp;&nbsp;' +
  '<i class="fa fa-user-times" id="chat-action-delete" title="${HideThread}"></i>' +
  '</span>' +
  '</div>' +
  '<form action="{{html AttachmentUrl}}" method="post" class="dropzone" enctype="multipart/form-data" style="display: none;">' +
  '<div class="fallback">' +
  '<input name="file" id="im_attachment"/>' +
  '</div>' +
  '</form>' +
  '<div class="chat-row"></div>' +
  '<div class="chat-form-actions" style="display:none"></div>' +
  '<div class="chat-form-actions-arrow"></div>' +
  '<div class="chat-form">' +
  '<div>' +
  '<textarea name="chat" id="im_chat_box"></textarea>' +
  '<span class="chat-row-action">' +
  '{{html Attachment}}' +
  '{{html Twemoji}}' +
  '<span class="chat-attachment-preview">' +
  '<span class="chat-attachment-preview-uploading"><i class="fa fa-spin fa-circle-o-notch"></i>&nbsp;&nbsp;${UploadingMessage}</span>' +
  '&nbsp;&nbsp;<span id="chat-attachment-file-name"></span>&nbsp;&nbsp;<span id="chat-attachment-result"></span>&nbsp;&nbsp;<i class="fa fa-close" id="chat-attachment-remove"></i>' +
  '</span>' +
  '</span>' +
  '</div>' +
  '<button class="btn btn-primary" id="im_send_btn" autofocus="false" title="${Send}"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>' +
  '</div>',

  chat_action_deleted_user_tmpl:
  '<div class="chat-row-title">' +
  '<span class="chat-row-back" data-cmd="core-im" data-action="back_to_conversations"  data-thread-id="${ThreadId}">' +
  '<i class="fa fa-chevron-left"></i>' +
  '</span>' +
  '<span class="chat-row-users"></span>' +
  '<span class="chat-row-close">' +
  '<i class="fa fa-search" aria-hidden="true" id="chat-action-search" title="${SearchThread}"></i>&nbsp;&nbsp;' +
  '<i class="fa fa-user-times" id="chat-action-delete" title="${HideThread}"></i>' +
  '</span>' +
  '</div>' +
  '<div class="chat-row"></div>' +
  '<div class="chat-form-actions" style="display:none"></div>' +
  '<div class="chat-form">' +
  '<p>${CannotReply}</p>' +
  '</div>',

  thread_tmpl:
  '<div class="pf-im-panel ${NewHidden}" data-thread-id="${ThreadId}" style="display:none;">' +
  '<div class="pf-im-panel-image"><span class="__thread-image" data-users="${Users}"></span></div>' +
  '<div class="pf-im-panel-content">' +
  '<span class="__thread-name" data-users="${Users}"></span>' +
  '<div class="pf-im-panel-preview">{{html MessagePreview}}</div>' +
  '</div>' +
  '<div class="pf-im-panel-info">' +
  '<span class="badge"></span>' +
  '</div>' +
  '</div>',

  loading_conversation_tmpl:
      '<div class="pf-chat-window-loading"><i class="fa fa-spin fa-circle-o-notch"></i>&nbsp;${LoadingConversation}</div>',

  deleted_user_tmpl:
      '<span><a class="no_ajax_link" role="button" target="_blank"><span class="no_image_user  _size__120 _gender_ _first_du" title="${UserName}"><span class="js_hover_info hidden">${UserName}</span><span>${ShortName}</span></span></a></span>',

  invalid_user_tmpl:
      '<span><a class="no_ajax_link" target="_blank"><span class="no_image_user  _size__120 _gender_ _first_du" title="${UserName}"><span class="js_hover_info hidden">${UserName}</span><span>${ShortName}</span></span></a></span>',

  init: function() {
    $Core_IM.im_debug_mode && console.log('init()');
    var u = $('#auth-user'),
        im = $('#pf-im');

    if ($('#admincp_base').length || pf_im_node_server === '' || !u.length) {
      return;
    }

    $Core_IM.thread_cnt = 0;
    $Core_IM.thread_total = 0;
    $Core_IM.users = '';

    $(document).off('click', '[data-cmd="core-im"]');
    $(document).on('click', '[data-cmd="core-im"]', function(evt) {
      var t = $(this),
          action = t.data('action');
      if ($Core_IM.cmds.hasOwnProperty(action) &&
          typeof $Core_IM.cmds[action] === 'function') {
        $Core_IM.cmds[action](t, evt);
      }
    });

    $('.pf_chat_delete_message').click(function() {
      var t = $(this),
          thread_id = t.closest('.chat-row').data('thread-id');
      t.hide();
      $('.pf-im-panel[data-thread-id="' + thread_id + '"]').
          find('.pf-im-panel-preview').
          text(oTranslations['this_message_has_been_deleted']);
      // remove attachment
      t.siblings('.im_attachment').remove();
      $Core_IM.socket.emit('chat_delete', thread_id, t.data('key'));
      return false;
    });

    if (typeof(twemoji_selectors) !== 'undefined') {
      twemoji_selectors += ', .pf-chat-body, .pf-im-panel-preview';
    }

    $Core_IM.sound_path = (typeof(pf_im_custom_sound) !== 'undefined')
        ? pf_im_custom_sound
        : PF.url.make('/PF.Site/Apps/core-im/assets/sounds/noti.wav').
            replace('/index.php/', '/');
    $Core_IM.sound_path = $Core_IM.sound_path.indexOf('http') === -1
        ? PF.url.make($Core_IM.sound_path).replace('/index.php/', '/')
        : $Core_IM.sound_path;

    // Hide emoji panel when click on below elements
    $(document).
        on('click',
            '.chat-row, .chat-row-title, .pf-im-main, ._pf_im_friend_search, #im_chat_box',
            function() {
              $Core_IM.emoji.hide();
            });

    if (typeof pf_im_using_host !== 'undefined') {
      $Core_IM.host = window.location.hostname + '@';
    }

    // remove attachment
    $(document).on('click', '#chat-attachment-remove', function() {
      var textarea = $('.chat-form textarea'),
          attachment_id = textarea.data('attachment-id');
      $(this).parent('.chat-attachment-preview').hide();
      if (typeof attachment_id !== 'undefined' && attachment_id > 0) {
        $Core_IM.im_debug_mode &&
        console.log('Remove attachment ' + attachment_id);
        // remove attachment in server
        $.ajaxCall('attachment.delete', 'id=' + attachment_id);
        // remove attachment id in textarea
        textarea.removeData('attachment-id');
      }
    });

    $('#chat-action-delete').click(function() {
      var t = $('.chat-row').data('thread-id');
      $('#pf-chat-window').hide();
      $('.pf-im-panel.active').removeClass('active');
      $('#pf-chat-window-active').hide();
      $('.pf-im-panel[data-thread-id="' + t + '"]').remove();
      $Core_IM.socket.emit('hideThread', {
        id: t,
        user_id: $Core_IM.getUser().id,
      });
    });

    $('#chat-action-noti').click(function() {
      var bell = $('#chat-action-noti');
      bell.attr('class', 'fa ' +
          (bell.hasClass('fa-bell') ? 'fa-bell-slash' : 'fa-bell'));
      $Core_IM.socket.emit('toggleNoti', {
        id: $('.chat-row').data('thread-id'),
        noti: bell.hasClass('fa-bell'),
        userId: $Core_IM.getUser().id,
      });
    });

    $('#chat-action-search').click(function() {
      if ($('#pf-im').is(':visible')) {
        $('#pf-im-search-input').val('');
        $('.pf-im-search-result').empty();
        $('.pf-im-search').show();
      }
    });

    $('.pf-im-search-close').click(function() {
      $('.pf-im-search').hide();
    });

    $('#pf-im-search-input').unbind().keyup(function(e) {
      var pis = $('#pf-im-search-input'),
          stext = pis.val(),
          valid = $Core_IM.checkKeyUp(e.keyCode);

      // search when input from 3 chars
      if (valid && stext.length > 2) {
        $('.pf-im-search-result').empty();
        $Core_IM.socket.emit('search_message', $('.chat-row').data('thread-id'),
            stext, 0);
      }
    });

    $('.pf-im-search-result').scroll(function() {
      if ($(this).children().length > 0 &&
          $(this).scrollTop() + $(this).innerHeight() ===
          $(this)[0].scrollHeight) {
        var pis = $('#pf-im-search-input'),
            stext = pis.val();
        // search when input from 3 chars
        if (stext.length > 2) {
          $Core_IM.socket.emit('search_message',
              $('.chat-row').data('thread-id'), stext,
              $('.pf-im-search-result').find('.pf-chat-message').length);
        }
      }
    });

    $('.chat-row, .chat-form').mousedown(function() {
      $('#pf-chat-window').css('opacity', 1);
    });

    var hd_mess = $('#hd-message a'),
        span_new = $('span#js_total_new_messages');

    if (hd_mess.length || (!hd_mess.length && span_new.length)) {
      var obj = (hd_mess.length ? hd_mess : span_new.parents('a:first'));

      obj.each(function() {
        var m = $(this);
        m.addClass('built');
        m.addClass('no_ajax');
        m.removeClass('_panel');
        m.removeAttr('data-toggle').removeAttr('data-panel');

        m.click(function() {
          $Core_IM.im_debug_mode && console.log('click!');
          $('#pf-open-im').trigger('click');
          return false;
        });
      });
    }

    $(document).
        on('click', '.pf-im-title .close-im-window, #pf-im-wrapper',
            function() {
              var body = $('body');
              body.removeClass('im-is-active');
              body.css('overflow-y', 'scroll');
              body.css('position', 'initial');
              $('#pf-im, #pf-chat-window, #pf-chat-window-active, .pf-im-search, #pf-im-wrapper').
                  hide();
              deleteCookie('pf_im_active');
            });

    $('.popup-im-window').click(function() {
      var newwindow = window.open(PF.url.make('/im/popup'), 'name');
      if (window.focus) {
        newwindow.focus();
      }
      $('#pf-im-wrapper').trigger('click');
    });

    $('._pf_im_friend_search input').keyup(function(e) {
      var t = $(this),
          im_main = $('.pf-im-main'),
          im_search = $('.pf-im-search-user'),
          valid = $Core_IM.checkKeyUp(e.keyCode);

      if (!valid) {
        return;
      }

      im_main.hide();
      im_search.empty().show();
      if (t.val() === '') {
        im_search.hide();
        im_main.show();
        return;
      }

      $('.fa-search', '._pf_im_friend_search').hide();
      $('.fa-pulse', '._pf_im_friend_search').show();
      clearTimeout($Core_IM.searching);
      $Core_IM.searching = setTimeout(function() {
        $.ajax({
          url: PF.url.make('/im/search-friends'),
          data: 'search=' + t.val(),
          contentType: 'application/json',
          success: function(e) {
            // im_main.hide();
            $('.fa-pulse', '._pf_im_friend_search').hide();
            $('.fa-search', '._pf_im_friend_search').show();
            im_search.empty();
            if (e !== '') {
              im_search.append(e);

              // update search preview
              $('.pf-im-search-user').find('.pf-im-panel').each(function() {
                $Core_IM.socket.emit('loadSearchPreview', {
                  'friend_id': $(this).data('friend-id'),
                  'user_id': $Core_IM.getUser().id,
                });
              });
            }
            else {
              im_search.append('<p>' + oTranslations['no_friends_found'] + '</p>');
            }
            $Core.loadInit();
          },
        });
      }, 1e3);
    });

    $('#im_action_emotion').unbind().click(function() {
      if ($('.chat-form-actions').is(':visible')) {
        $Core_IM.emoji.hide();
      }
      else {
        $Core_IM.emoji.show($(this).data('action'));
      }
    });

    $('#pf-open-im').click(function() {
      $Core_IM.im_debug_mode && console.log('#pf-open-im click');
      if ($Core_IM.host_failed !== false) {
        window.parent.sCustomMessageString = $Core_IM.host_failed;
        tb_show(oTranslations['error'],
            $.ajaxBox('core.message', 'height=150&width=300'));
        return;
      }
      var b = $(this),
          body = $('body');

      // lock scroll on ios
      if ($Core_IM.isIos()) {
        $('body').css('position', 'fixed');
      }

      $('#pf-im').show();
      $('#pf-im-wrapper').show();
      body.css('overflow', 'hidden');

      if (!b.data('fake-click') ||
          (b.data('fake-click') && b.data('fake-click') == '0')) {
        body.addClass('im-is-active');
      }

      setCookie('pf_im_active', 1);

      $('.pf-im-panel.active').removeClass('active');
      // $('span#js_total_new_messages').html('0').hide();
    });

    if (u.length && !im.length) {
      $.tmpl($Core_IM.core_tmpl, {
        'Title': oTranslations['messenger'],
        'CloseChatBox': oTranslations['close_chat_box'],
        'OpenNewTab': oTranslations['open_in_new_tab'],
        'SearchFriendPlaceholder': oTranslations['search_friends_dot_dot_dot'],
        'SoundPath': $Core_IM.sound_path,
      }).prependTo('body');
      $.tmpl($Core_IM.search_message_tmpl, {
        'Title': oTranslations['search_message'],
        'SearchPlaceholder': oTranslations['enter_search_text'],
      }).prependTo('body');

      $Core.loadInit();
    }

    // IM draggable init
    if (im.hasClass('ui-draggable')) {
      im.draggable('destroy');
    }
    im.draggable({
      handle: '.pf-im-title',
    });
    // IM resizable init
    if (im.hasClass('ui-resizable')) {
      im.resizable('destroy');
    }
    im.resizable();
    // Search message draggable init
    var im_search = $('.pf-im-search');
    if (im_search.hasClass('ui-draggable')) {
      im_search.draggable('destroy');
    }
    im_search.draggable({
      handle: '.pf-im-search-top',
    });

    $Core_IM.chatWithUser();

    // Load more messages when scroll up
    var chat_row = $('.chat-row');
    chat_row.scroll(function() {
      if (chat_row.scrollTop() === 0) {
        $.tmpl($Core_IM.chat_load_more_tmpl, {
          'LoadingMessage': oTranslations['loading_messages'],
        }).prependTo('.chat-row');
        $Core_IM.socket.emit('loadMore', chat_row.data('thread-id'),
            $('.pf-chat-message').length);
      }
    });

    // $Core_IM.load_first_time = false;
  },

  isIos: function() {
    var iDevices = [
      'iPad Simulator',
      'iPhone Simulator',
      'iPod Simulator',
      'iPad',
      'iPhone',
      'iPod',
    ];
    while (iDevices.length) {
      if (navigator.platform === iDevices.pop()) { return true; }
    }
    return false;
  },

  // click send message on user profile
  composeMessage: function(param) {
    if ($Core_IM.host_failed !== false) {
      window.parent.sCustomMessageString = $Core_IM.host_failed;
      tb_show('error', $.ajaxBox('core.message', 'height=150&width=300'));
      return;
    }

    $Core_IM.socket.emit('showThread', $Core_IM.getUser().id + ':' +
        param.user_id, $Core_IM.getUser().id);

    // open IM
    $('#pf-open-im').trigger('click');

    // clear search
    var search_user = $('._pf_im_friend_search input');
    search_user.val('');
    $('.pf-im-main').show();
    $('.pf-im-search-user').hide();

    var thread = $('.pf-im-panel[data-thread-id="' + $Core_IM.getUser().id +
        ':' + param.user_id + '"]');
    if (thread.length > 0) {
      thread.trigger('click');
      return false;
    }

    thread = $('.pf-im-panel[data-thread-id="' + param.user_id + ':' +
        $Core_IM.getUser().id + '"]');
    if (thread.length > 0) {
      thread.trigger('click');
      return false;
    }

    var f = $('.pf-im-menu a[data-type="2"]');
    if (f.hasClass('active')) {
      f.removeClass('active');
      $('.pf-im-menu a[data-type="1"]').addClass('active');
    }

    var is_listing = (typeof(param.listing_id) === 'number');
    $.ajax({
      url: PF.url.make('/im/conversation') + '?user_id=' + param.user_id +
      '&listing_id=' + (is_listing ? param.listing_id : '0'),
      contentType: 'application/json',
      success: function(resp) {
        if (typeof(resp.error) === 'string') {
          $Core_IM.imFailed();
          return;
        }

        var e = resp.user,
            thread_ids = [$Core_IM.getUser().id, e.id],
            thread_id = thread_ids.sort($Core_IM.sortNumber).join(':'),
            m = $('.pf-im-panel[data-thread-id="' + thread_id + '"]');

        if (!m.length) {
          $.tmpl($Core_IM.panel_tmpl, {
            'UserId': e.id,
            'ThreadId': thread_id,
            'PhotoLink': e.photo_link,
            'Name': e.name,
          }).prependTo('.pf-im-main');
        }

        $Core_IM.socket.emit('loadSearchPreview', {
          'friend_id': e.id,
          'user_id': $Core_IM.getUser().id,
        });

        $Core.loadInit();

        m = $('.pf-im-panel[data-thread-id="' + thread_id + '"]');
        m.removeClass('active');
        if (is_listing) {
          m.data('listing-id', param.listing_id);
        }
        m.trigger('click');
      },
    });

    return false;
  },

  imAttachFile: function() {
    $('#im_attachment').trigger('click');
  },

  imFailed: function() {
    $('#chat-action-delete').trigger('click');
    var popup = $('<a class="popup" href="' + PF.url.make('/im/failed') +
        '"></a>');
    tb_show('', PF.url.make('/im/failed'), popup);
    $('#pf-im').hide();
    $('body').css('position', 'initial')
    $('#pf-im-wrapper').hide();
  },

  addTargetBlank: function(photo_link) {
    var user_image = $($.parseHTML(photo_link));
    user_image.attr('target', '_blank');
    user_image.addClass('no_ajax_link');

    return user_image.prop('outerHTML');
  },

  getUser: function() {
    var u = $('#auth-user');

    return {
      id: u.data('id'),
      name: u.data('name'),
      photo_link: $Core_IM.addTargetBlank(u.data('image')),
    };
  },

  filterWords: function(text) {
    for (var m in window.ban_filters) {
      if (text.indexOf(m) !== -1) {
        if (window.ban_users.indexOf(m) !== -1) {
          // ban user
          $.ajax({
            url: PF.url.make('/im/ban-user'),
            contentType: 'application/json',
            'success': function() {
              window.location.reload();
            },
          });
        }
        text = text.replace(m, window.ban_filters[m]);
      }
    }

    return text;
  },

  preventXss: function(text) {
    var regScript = /<script/ig,
        regEndScript = /<\/script/ig,
        replaceScript = regScript.exec(text),
        replaceEndScript = regEndScript.exec(text);
    if (replaceScript !== null && replaceScript.length > 0) {
      text = text.replace(replaceScript[0],
          replaceScript[0].split('').join(' '));
    }
    if (replaceEndScript !== null && replaceEndScript.length > 0) {
      text = text.replace(replaceEndScript[0],
          replaceEndScript[0].split('').join(' '));
    }

    return text;
  },

  fixChatMessage: function(text, bReplaceLink) {
    if (text === null) {
      return '';
    }

    var map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      '\'': '&#039;',
      '\r': '<br />',
      '\n': '<br />',
      '\n\r': '<br />',
    };

    if (bReplaceLink) {
      text = text.replace(/[&<>"'\r\n]/g, function(m) { return map[m]; });
      var patt = /(https?):\/\/[-A-Z0-9+&@#/%?=~_|!:,.;]*[-A-Z0-9+&@#/%=~_|]/ig,
          match = patt.exec(text);
      if (match !== null) {
        text = text.replace(match[0], '<a href="' + match[0] + '">' + match[0] +
            '</a>');
      }
    }

    return text;
  },

  getPartnerId: function(thread_id) {
    var users = thread_id.split(':');
    for (var i in users) {
      if (users[i] != $Core_IM.getUser().id) {
        return users[i];
      }
    }
    return false;
  },

  buildMessage: function(message, do_scroll, force, no_trash) {
    if (typeof(message) === 'string') {
      message = JSON.parse(message);
    }
    if (!message.deleted) {
      var patt = /(https?):\/\/[-A-Z0-9+&@#/%?=~_|!:,.;]*[-A-Z0-9+&@#/%=~_|]/ig,
          match = patt.exec(message.text);
      if (match !== null) {
        // parse link for preview
        $.ajax({
          url: PF.url.make('/im/link'),
          data: 'url=' + match[0] + '&time_stamp=' + message.time_stamp,
          contentType: 'application/json',
          'success': function(e) {
            var message = $('#' + e.time_stamp),
                link_preview = '';
            if (message.length === 1) {
              if (e.link.has_embed > 0) {
                link_preview = '<a href="' + e.link.link +
                    '" target="_blank" class="play_link no_ajax_link" onclick="$Core.box(\'link.play\', 700, \'id=' +
                    e.link.link_id +
                    '&popup=true\', \'GET\'); return false;"><span class="play_link_img">' +
                    oTranslations['play'] + '</span><img src="' + e.link.image +
                    '"></a>';
              }
              else {
                link_preview = e.link.image
                    ? '<a href="' + e.link.link +
                    '" target="_blank"><img src="' + e.link.image + '"></a>'
                    : '';
              }

              // check if link already have preview
              if (message.find('.pf-im-chat-link').length === 0) {
                message.find('.pf-chat-body').
                    prepend($.tmpl($Core_IM.chat_link_tmpl, {
                      'LinkPreview': link_preview,
                      'Link': e.link.link,
                      'Title': e.link.title,
                      'Description': e.link.description,
                    }));
              }

              message.find('.fa-circle-o-notch').parent().remove();
            }
          },
        });
      }

      var attachment = '';
      if (typeof(message.attachment_id) === 'number') {
        attachment = '<div id="im_attachment_' + message.attachment_id +
            '" class="im_attachment"></div>';
        $.ajax({
          url: PF.url.make('/im/get-attachment'),
          data: 'id=' + message.attachment_id,
          contentType: 'application/json',
          'success': function(e) {
            if (e.is_image) {
              attachment =
                  '<a href="' + e.path + '" class="thickbox">' +
                  '<img src="' + e.thumb + '" data-src="' + e.thumb + '">' +
                  '</a>';
            }
            else {
              attachment =
                  '<a href="' + e.path +
                  '" class="attachment_row_link no_ajax_link">' + e.file_name +
                  '</a>';
            }
            $('#im_attachment_' + e.id).html(attachment);
            $Core.loadInit();
          },
        });
      }
    }

    var icon = '',
        time_stamp_ms = message.time_stamp,
        user_image = '';
    // support old data
    if (time_stamp_ms < 1000000000000) {
      time_stamp_ms *= 1000;
    }

    if ($.inArray(message.user.id.toString(), $Core_IM.deleted_users) !== -1) {
      user_image = $('<div />').append($.tmpl($Core_IM.deleted_user_tmpl, {
        'UserName': oTranslations['deleted_user'],
        'ShortName': 'DU',
      })).html();
    } else if (typeof pf_im_blocked_users !== 'undefined' && pf_im_blocked_users.indexOf(message.user.id) !== -1) {
      user_image = $('<div />').append($.tmpl($Core_IM.invalid_user_tmpl, {
        'UserName': oTranslations['invalid_user'],
        'ShortName': 'IU',
      })).html();
    } else {
      user_image = force ? message.user.photo_link : '';
    }

    if (message.user.id === $Core_IM.getUser().id &&
        typeof(no_trash) === 'undefined' &&
        (typeof(window.pf_time_to_delete_message) === 'undefined' ||
            Date.now() - time_stamp_ms <= window.pf_time_to_delete_message)) {
      icon = '<a href="#" class="pf_chat_delete_message" data-key="' +
          message.time_stamp + '"><i class="fa fa-trash"></i></a>';
    }
    if (do_scroll === true) {
      var c = $('.chat-row');
      c.scrollTop(c[0].scrollHeight);
    }

    return $.tmpl($Core_IM.chat_message_tmpl, {
      'OwnerClass': message.user.id === $Core_IM.getUser().id
          ? ' pf-chat-owner'
          : '',
      'StyleDisplayNone': force === true ? '' : 'style="display:none;"',
      'UserId': message.user.id,
      'MessageTimestamp': message.time_stamp,
      'UserPhoto': user_image,
      'ChatMessage': (message.deleted
          ? '<i>' + oTranslations['this_message_has_been_deleted'] + '</i>'
          : '<span class="pf-im-chat-text">' + (match !== null
          ? '<div><i class="fa fa-circle-o-notch fa-spin"></i></div>'
          : '') + $Core_IM.fixChatMessage(message.text, true) + '</span>' +
          attachment + icon),
    });
  },

  updateChatPreview: function(thread_id, text) {
    var old_thread = $('.pf-im-panel[data-thread-id="' + thread_id + '"]'),
        thread = old_thread.clone();
    if (text === '') {
      text = $Core_IM.file_preview;
    }
    thread.find('.pf-im-panel-preview').removeClass('twa_built').text(text);
    old_thread.remove();
    $('.pf-im-main').prepend(thread);
  },

  chatTextArea: function() {
    $('.chat-form textarea').unbind().focus(function() {
      if ($Core_IM.is_mobile) {
        $('body > *:not(#pf-im):visible').addClass('im_temp_hide').hide();
      }
    }).blur(function() {
      if ($Core_IM.is_mobile) {
        $('.im_temp_hide').show().removeClass('im_temp_hide');
      }
    }).keydown(function(e) {
      if (e.which === 13 && !e.shiftKey && !e.ctrlKey) {
        e.preventDefault();
        $Core_IM.submitChat();
      }
    });
    $('#im_send_btn').unbind().click(function(e) {
      e.preventDefault();
      $Core_IM.submitChat();
    });
  },

  chatWithUser: function() {
    var chat_form = $('.chat-form'),
        chat_row = $('.chat-row'),
        chat_form_old_height = chat_form.height(),
        im_chat_box = $('#im_chat_box'),
        im_before = im_chat_box.height(),
        im_chat_row_before = chat_row.height();
    if (chat_form.hasClass('ui-resizable')) {
      chat_form.resizable('destroy');
    }
    chat_form.resizable({
      handles: 'n',
      alsoResize: '#im_chat_box',
      minHeight: $Core_IM.chat_form_min_height,
      maxHeight: $Core_IM.chat_form_max_height,
      start: function() {
        chat_form_old_height = chat_form.height();
        im_before = im_chat_box.height();
        im_chat_row_before = chat_row.height();
      },
      resize: function() {
        $(this).css('top', 'auto');
        var changed = chat_form.height() - chat_form_old_height;
        im_chat_box.height(im_before + changed);
        chat_row.height(im_chat_row_before - changed);
      },
    });

    $('.pf-im-panel').click(function() {
      $Core_IM.load_first_time = true;
      var isImPopup = window.location.href.search('/im/popup'),
          c = $('#pf-chat-window'),
          t = $(this),
          html = '',
          total_new_messages = $('#js_total_new_messages');
      // remove count when view conversation
      t.removeClass('count');
      if (t.data('user-deleted') || t.data('user-banned') || t.data('user-blocked')) {
        html = $.tmpl($Core_IM.chat_action_deleted_user_tmpl, {
          'SearchThread': oTranslations['search_thread'],
          'HideThread': oTranslations['hide_thread'],
          'CannotReply': oTranslations['you_cannot_reply_this_conversation'],
          'ThreadId': t.data('thread-id'),
        });
      }
      else {
        html = $.tmpl($Core_IM.chat_action_tmpl, {
          'ThreadNotification': oTranslations['noti_thread'],
          'SearchThread': oTranslations['search_thread'],
          'HideThread': oTranslations['hide_thread'],
          'AttachmentUrl': PF.url.make('/im/attachment'),
          'Send': oTranslations['send'],
          'ThreadId': t.data('thread-id'),
          'Attachment': (typeof pf_im_attachment_enable !== 'undefined')
              ? '<i class="fa fa-paperclip" onclick="$Core_IM.imAttachFile()" title="' +
              oTranslations['add_attachment'] + ' (' + pf_im_attachment_types +
              ')"></i>'
              : '',
          'Twemoji': (typeof pf_im_twemoji_enable !== 'undefined')
              ? '<i class="fa fa-smile-o" id="im_action_emotion" data-action="' +
              PF.url.make('/emojis?id=im_chat_box') + '"></i>'
              : '',
          'UploadingMessage': oTranslations['uploading'] + '...',
        });
      }
      html = $('<div />').append(html).html();
      if (isImPopup !== -1) {
        document.title = window.pf_im_site_title;
      }

      c.css('opacity', '1');
      if (t.hasClass('new')) {
        t.removeClass('new');
        // update notification counter
        var message_counter = parseInt(total_new_messages.text());
        message_counter--;
        if (message_counter === 0) {
          total_new_messages.text(0).hide();
        }
        else {
          total_new_messages.text(message_counter);
        }
      }
      t.removeClass('is_hidden');

      if (t.hasClass('active')) {
        t.removeClass('active');
        c.hide();
        $('#pf-chat-window-active').hide();

        return false;
      }

      $('.pf-im-panel.active').removeClass('active');
      t.addClass('active');

      if (!t.data('thread-id')) {
        function get_thread_id(numArray) {
          numArray = numArray.sort(function(a, b) {
            return a - b;
          });

          return numArray.join(':');
        }

        t.data('thread-id',
            get_thread_id([t.data('user-id'), $Core_IM.getUser().id]));
      }

      $Core_IM.socket.emit('loadConversation', {
        user_id: $Core_IM.getUser().id,
        partner_id: $Core_IM.getPartnerId(t.data('thread-id')),
        thread_id: t.data('thread-id'),
      });

      if (c.length) {
        c.html(html).show();
      }
      else {
        $('#pf-im').
            prepend('<span id="pf-chat-window-active"></span><div id="pf-chat-window">' +
                html + '</div>');
      }

      $('#pf-chat-window-active').
          css('top', ((t.offset().top - $(window).scrollTop()) +
              (t.height() / 2)) - 5).
          show();
      $('.chat-row').attr('data-thread-id', t.data('thread-id'));

      if (t.data('listing-id')) {
        $('.chat-form input').
            before('<div><input type="hidden" name="listing_id" id="pf_im_listing_id" value="' +
                t.data('listing-id') + '">');
      }

      var l = $('#pf-chat-window .fa-external-link');
      l.data('action', l.data('action') + '?thread_id=' + t.data('thread-id'));

      $Core_IM.chatTextArea();
      $Core.loadInit();
      if (!$Core_IM.is_mobile) {
        $('.chat-form input').focus();
      }
      if (typeof(myDropzone) === 'undefined' &&
          typeof pf_im_attachment_enable !== 'undefined') {
        $Core_IM.initDropzone();
      }

      if ($Core_IM.chat_form_min_height === 0) {
        $Core_IM.chat_form_min_height = $('.chat-form').height();
      }

      // update new message counter
      t.find('.badge').text('0');
    });

    $Core_IM.chatTextArea();
  },

  updateChatTime: function() {
    $('.set-moment:not(.built)').each(function() {
      var t = $(this),
          time = 0,
          start = new Date();
      t.addClass('built');
      start.setHours(0, 0, 0, 0);
      if (t.data('time') > 1000000000000) {
        if (t.data('time') < start.getTime()) {
          var date = new Date(t.data('time')),
              df = new DateFormatter();
          t.html(df.formatDate(date, window.global_update_time));
          return;
        }
        else {
          time = t.data('time') / 1000;
        }
      }
      else {
        // support old version
        if (t.data('time') * 1000 < start.getTime()) {
          var date = new Date(t.data('time') * 1000),
              df = new DateFormatter();
          t.html(df.formatDate(date, window.global_update_time));
          return;
        }
        else {
          time = t.data('time');
        }
      }
      // support old timestamp in second, new timestamp in milisecond
      t.html($Core_IM.convertTime(time));
    });
  },

  build_thread: function(message, users) {
    var is_new = '', is_hidden = '';
    if (typeof(message.is_new) === 'string' && message.is_new === '1' &&
        (message.user != undefined && message.user.id !==
            $Core_IM.getUser().id)) {
      is_new = ' new';
    }

    if (typeof(message.is_hidden) === 'string' && message.is_hidden == '1') {
      is_hidden = ' is_hidden';
    }

    if (message.preview === '') {
      message.preview = $Core_IM.file_preview;
    }

    return $('<div />').append($.tmpl($Core_IM.thread_tmpl, {
      'NewHidden': is_new + is_hidden,
      'ThreadId': message.thread_id,
      'Users': users,
      'MessagePreview': (typeof(message.is_deleted) !== 'undefined' &&
      message.is_deleted
          ? '<span class="pf-im-chat-preview-text">' +
          oTranslations['this_message_has_been_deleted'] + '</span>'
          : $Core_IM.fixChatMessage(message.preview, false)),
    })).html();
  },

  start_im: function() {
    if ($('#admincp_base').length || !$('#auth-user').length ||
        pf_im_node_server === '' ||
        (pf_im_token === '' && $('#pf-im-host').length === 0)) {
      return;
    }

    if ($Core_IM.socket_built === false && typeof io !== 'undefined') {
      $Core_IM.socket_built = true;
      $.ajaxSetup({cache: true});

      if (typeof(pf_im_token) === 'undefined' || pf_im_token == '') {
        return;
      }

      // connect to chat server
      $Core_IM.socket = io(pf_im_node_server, {
        query: 'token=' + pf_im_token,
      });

      $Core_IM.socket.on('host_failed', function(message) {
        $Core_IM.im_debug_mode && console.log('On host_failed');
        // destroy socket
        $Core_IM.socket.disconnect();
        // set failed message
        $Core_IM.host_failed = message;
        // hide im panel
        var im = $('#pf-im');
        if (im.length && im.is(':visible')) {
          im.hide();
          $('#pf-im-wrapper').hide();
        }
      });

      $Core_IM.socket.on('retry', function(data) {
        $Core_IM.im_debug_mode && console.log('On retry');
        if (data.retry) {
          // can retry
          $Core_IM.im_debug_mode && console.log('RETRY TO CONNECT:', 'get token');
          $.ajax({
            url: PF.url.make('/im/get-token'),
            data: 'timestamp=' + data.im_timestamp,
            success: function(token) {
              $Core_IM.im_debug_mode && console.log('RETRY TO CONNECT:', 're-verify');
              $Core_IM.socket = io(pf_im_node_server, {
                query: 'token=' + token + '&retry=1',
                forceNew: true
              });
              $Core_IM.initSocketEvents();
            },
            error: function() {
              $Core_IM.retryFailed('Unable to connect to the IM server.');
            }
          });
        } else {
          // cannot retry
          $Core_IM.im_debug_mode && console.log('CANNOT RETRY');
          $Core_IM.retryFailed(data.message);
        }
      });

      $Core_IM.initSocketEvents();

      $.ajaxSetup({cache: false});
      $Core.loadInit();
    }
  },

  reloadImages: function() {
    $('.image_deferred:not(.built)').each(function() {
      var t = $(this),
          src = t.data('src'),
          i = new Image();

      t.addClass('built');
      if (!src) {
        t.addClass('no_image');
        return;
      }

      t.addClass('has_image');
      i.onerror = function(e, u) {
        t.replaceWith('');
      };
      i.onload = function(e) {
        t.attr('src', src);
      };
      i.src = src;
    });
  },

  insertAtCaret: function(areaId, text) {
    var txtarea = document.getElementById(areaId);
    if (!txtarea) { return; }

    var scrollPos = txtarea.scrollTop;
    var strPos = 0;
    var br = ((txtarea.selectionStart || txtarea.selectionStart == '0')
        ? 'ff'
        : (document.selection ? 'ie' : false ) );
    if (br == 'ie') {
      txtarea.focus();
      var range = document.selection.createRange();
      range.moveStart('character', -txtarea.value.length);
      strPos = range.text.length;
    }
    else if (br == 'ff') {
      strPos = txtarea.selectionStart;
    }

    var front = (txtarea.value).substring(0, strPos);
    var back = (txtarea.value).substring(strPos, txtarea.value.length);
    txtarea.value = front + text + back;
    strPos = strPos + text.length;
    if (br == 'ie') {
      txtarea.focus();
      var ieRange = document.selection.createRange();
      ieRange.moveStart('character', -txtarea.value.length);
      ieRange.moveStart('character', strPos);
      ieRange.moveEnd('character', 0);
      ieRange.select();
    }
    else if (br == 'ff') {
      txtarea.selectionStart = strPos;
      txtarea.selectionEnd = strPos;
      txtarea.focus();
    }

    txtarea.scrollTop = scrollPos;
  },

  initDropzone: function() {
    if ($('#im_attachment').length !== 0) {
      var myDropzone = new Dropzone('div.chat-row', {
        maxFiles: 1,
        url: PF.url.make('/im/attachment'),
        clickable: '#im_attachment',
        addedfile: function(file) {
          $('.chat-attachment-preview').css('display', 'inline-block');
          $('#chat-attachment-file-name').text(file.name);
          $('.chat-attachment-preview-uploading').show();
        },
        success: function(file, res) {
          if (res === '') {
            // upload failed
            $('#chat-attachment-result').text(oTranslations['im_failed']);
          }
          else {
            $('.chat-form textarea').data('attachment-id', res.id);
            $('#chat-attachment-result').empty();
          }
          $('.chat-attachment-preview-uploading').hide();
          $('#chat-attachment-file-name').show();
          this.removeAllFiles();
        },
      });
    }
  },

  sortNumber: function(a, b) {
    return a - b;
  },

  notificationIsEnabled: function(notification) {
    notification = notification.split(':');
    return (notification.indexOf($Core_IM.getUser().id.toString()) !== -1);
  },

  checkKeyUp: function(keycode) {
    return (keycode > 47 && keycode < 58) || // number keys
        keycode === 32 || keycode === 13 || // spacebar & return key(s) (if you
        // want to allow carriage returns)
        (keycode > 64 && keycode < 91) || // letter keys
        (keycode > 95 && keycode < 112) || // numpad keys
        (keycode > 185 && keycode < 193) || // ;=,-./` (in order)
        (keycode > 218 && keycode < 223) || // [\]' (in order)
        keycode === 8;                      // backspace
  },

  convertTime: function(timestamp) {
    if (timestamp === 0) {
      return false;
    }
    var n = new Date(),
        now = Math.round(n.getTime() / 1000),
        iSeconds = Math.round(now - timestamp),
        iMinutes = Math.round(iSeconds / 60),
        hour = Math.round(parseFloat(iMinutes) / 60.0);
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
  },

  submitChat: function() {
    var t = $('#im_chat_box'),
        c = $('.chat-row'),
        timeNow = Math.floor(Date.now()),
        l = $('#pf_im_listing_id'),
        attachment_id = (typeof t.data('attachment-id') === 'undefined')
            ? 0
            : parseInt(t.data('attachment-id')) || 0,
        text = $Core_IM.preventXss($Core_IM.filterWords(trim(t.val())));
    if (text.length <= 0 && attachment_id === 0) {
      return;
    }

    $Core_IM.im_debug_mode && console.log('Submit...');
    c.append($Core_IM.buildMessage({
      text: text,
      time_stamp: timeNow,
      attachment_id: t.data('attachment-id'),
      user: {
        photo_link: $Core_IM.getUser().photo_link,
        id: $Core_IM.getUser().id,
      },
    }, false, true));

    $Core_IM.updateChatTime();
    c.scrollTop(c[0].scrollHeight);

    var receiver = $('.pf-im-panel.active a');
    $Core_IM.socket.emit('chat', {
      text: text,
      user: $Core_IM.getUser(),
      receiver: {
        'id': $Core_IM.getPartnerId(c.data('thread-id')),
        'name': receiver.attr('title'),
        'photo_link': receiver.clone().wrap('<div/>').parent().html(),
      },
      time_stamp: timeNow,
      thread_id: c.data('thread-id'),
      attachment_id: t.data('attachment-id'),
      listing_id: (l.length ? l.val() : 0),
      deleted: false,
    });
    $Core_IM.updateChatPreview(c.data('thread-id'), text);

    t.data('attachment-id', '');
    t.val('').focus();
    // hide attachment preview
    $('.chat-attachment-preview').hide();
    $Core.loadInit();
    $Core_IM.init();
  },

  threadMessageCounter: function(thread_id) {
    var thread = $('.pf-im-panel[data-thread-id="' + thread_id + '"]'),
        badge = thread.find('.badge'),
        message_counter = parseInt(badge.text()) || 0;

    !thread.hasClass('new') && thread.addClass('new');
    badge.text(message_counter + 1);
  },

  emoji: {
    init: function() {
      var im = $('#pf-im'),
          emoji_list = im.find('.emoji-list li');
      im.find('.emoji-list li').unbind('click').click(function() {
        var emoji = $(this).find('span').text();
        $Core_IM.insertAtCaret('im_chat_box', emoji);
        $('.chat-form-actions').hide();
      });
      if (!emoji_list.hasClass('dont-unbind')) {
        emoji_list.addClass('dont-unbind');
      }
    },
    hide: function() {
      $('.chat-form-actions').hide();
      $('.chat-form-actions-arrow').hide();
    },
    show: function(url) {
      var c = $('.chat-form-actions');
      c.show();
      c.html('<i class="fa fa-spin fa-circle-o-notch"></i>').
          css('bottom', '0px').
          show().
          animate({
            bottom: '35px',
          });
      $('.chat-form-actions-arrow').show();
      $.ajax({
        url: url,
        type: 'GET',
        contentType: 'application/json',
        success: function(e) {
          lastEmojiObject = $('.chat-form input.form-control');
          c.html(e.content);
          $Core_IM.emoji.init();
        },
      });
    },
  },

  retryFailed: function(message) {
    $Core_IM.im_debug_mode && console.log('RETRY TO CONNECT:', 'connect failed');

    var im = $('#pf-im');
    if (typeof message !== 'undefined') {
      $Core_IM.host_failed = message;
    }
    // hide im panel
    if (im.length && im.is(':visible')) {
      im.hide();
      $('#pf-im-wrapper').hide();
      // show alert
      window.parent.sCustomMessageString = $Core_IM.host_failed;
      tb_show(oTranslations['error'],
        $.ajaxBox('core.message', 'height=150&width=300'));
    }
  },

  initSocketEvents: function() {
    $Core_IM.socket.on('connect_successfully', function() {
      $Core_IM.im_debug_mode && console.log('connect_successfully');
      // clear threads
      $('.pf-im-main').empty();
      // load threads
      $Core_IM.socket.emit('loadThreads', $Core_IM.getUser().id,
        pf_total_conversations);
      // unlock search field
      var input = $('._pf_im_friend_search').find('input');
      input.length && input.attr('readonly', false);
      // reset error message
      $Core_IM.host_failed = false;
    });

    $Core_IM.socket.on('loadThreads', function(thread) {
      $Core_IM.im_debug_mode && console.log('On loadThreads');

      var threadUsers = '';
      $Core_IM.thread_cnt++;

      thread = $.parseJSON(thread);

      if (isset(thread.is_hidden) && thread.is_hidden === '1') {
        $Core_IM.im_debug_mode && console.log('no new count...');
      }
      else {
        $Core_IM.thread_show++;
      }

      // in case thread to show
      if (pf_total_conversations !== '0' && pf_total_conversations !== '' &&
        $Core_IM.thread_show > pf_total_conversations) {
        thread.is_hidden = '1';
      }

      var users = $Core_IM.users.split(',');
      for (var i in thread.users) {
        (users.indexOf(thread.users[i]) === -1) &&
        users.push(thread.users[i]);
      }
      $Core_IM.users = users.join();
      $('.pf-im-main').
        append($Core_IM.build_thread(thread, thread.users.join()));

      $Core_IM.socket.emit('update_new',
        $Core_IM.getPartnerId(thread.thread_id), thread.thread_id);
    });

    $Core_IM.socket.on('lastThread', function(thread) {
      $Core_IM.im_debug_mode && console.log('On lastThread');
      var get_friends = -1;

      // get avatar of users
      $.ajax({
        url: PF.url.make('/im/panel'),
        data: 'users=' + $Core_IM.users,
        contentType: 'application/json',
        'success': function(e) {
          $('.pf-im-panel').each(function() {
            var t = $(this),
              names = t.data('thread-id').split(':');
            for (var i in names) {
              var n = names[i];
              if (!n) {
                continue;
              }
              if (typeof(e[n]) === 'object') {
                var u = e[n];
                if (u.id == $Core_IM.getUser().id) {
                  continue;
                }
                // check for blocked users
                if (typeof pf_im_blocked_users !== 'undefined' && pf_im_blocked_users.indexOf(u.id) !== -1) {
                  t.find('.pf-im-panel-image').
                    html($.tmpl($Core_IM.invalid_user_tmpl, {
                      'UserName': oTranslations['invalid_user'],
                      'ShortName': 'IU',
                    }));
                  t.find('.__thread-name').html(oTranslations['invalid_user']);
                  t.attr('data-user-blocked', '1');
                } else {
                  t.find('.pf-im-panel-image').
                    html($Core_IM.addTargetBlank(u.photo_link));
                  t.find('.__thread-name').html(u.name);
                }

                // banned user
                if (typeof e[n].is_banned !== 'undefined' && e[n].is_banned) {
                  t.attr('data-user-banned', '1');
                }
              }
              else if (e[n] === false) {
                $Core_IM.deleted_users.push(n);
                t.find('.pf-im-panel-image').
                  html($.tmpl($Core_IM.deleted_user_tmpl, {
                    'UserName': oTranslations['deleted_user'],
                    'ShortName': 'DU',
                  }));
                t.find('.__thread-name').html(oTranslations['deleted_user']);
                t.attr('data-user-deleted', '1');
              }
              else {
                $Core_IM.socket.emit('deleteUser', n);
              }
            }

            t.show();
          });

          $('#pf-im > i').remove();
          $Core_IM.updateChatTime();
          $Core.loadInit();
        },
      });

      // get friends
      if (pf_total_conversations === '0' || pf_total_conversations === '') {
        get_friends = 0;
      }
      else {
        get_friends = (pf_total_conversations - $Core_IM.thread_show > 0)
          ? pf_total_conversations - $Core_IM.thread_show
          : -1;
      }
      if (get_friends >= 0) {
        $.ajax({
          url: PF.url.make('/im/friends'),
          data: 'limit=' + (get_friends > 0 ? get_friends : 0) + '&threads=' +
          $Core_IM.users,
          contentType: 'application/json',
          'success': function(e) {
            $('.pf-im-main').append(e);
            $Core.loadInit();
            $('#pf-im .fa-spin').hide();
          },
        });
      }
      else {
        $('#pf-im .fa-spin').hide();
      }

      // update message counter
      if (typeof thread !== 'undefined') {
        $Core_IM.socket.emit('update_new',
          $Core_IM.getPartnerId(thread.thread_id), thread.thread_id, true);
      }
    });

    $Core_IM.socket.on('hiddenThread', function(thread_id) {
      $Core_IM.users = $Core_IM.users.split(',').
        concat(thread_id.split(':')).
        join();
    });

    $Core_IM.socket.on('failed', function(data) {
      $('#chat-action-delete').trigger('click');
      $('.pf-im-panel[data-thread-id="' + data.thread + '"]').remove();
      var popup = $('<a class="popup" href="' + PF.url.make('/im/failed') +
        '"></a>');
      tb_show('', PF.url.make('/im/failed'), popup);
      $('#pf-im').hide();
      $('body').css('position', 'initial')
    });

    $Core_IM.socket.on('loadNewConversation', function(thread) {
      $Core_IM.im_debug_mode && console.log('On loadNewConversation');

      // add image of users on conversation
      var users = thread.thread_id.split(':').filter(function(v) {
        return v !== '';
      });
      if (users.length < 2) {
        return;
      }

      $.ajax({
        url: PF.url.make('/im/panel'),
        data: 'users=' + users.join(),
        contentType: 'application/json',
        'success': function(e) {
          for (var i in e) {
            var u = e[i];
            if (u === false) {
              $.tmpl($Core_IM.deleted_user_tmpl, {
                'UserName': oTranslations['deleted_user'],
                'ShortName': 'DU',
              }).prependTo('.chat-row-users');
            }
            else {
              $('.chat-row-users').
                prepend('<span>' + $Core_IM.addTargetBlank(u.photo_link) +
                  '</span>');
            }
          }
          $('.pf-chat-window-loading').remove();

          $Core_IM.reloadImages();
          $Core.loadInit();
        },
      });
    });

    $Core_IM.socket.on('loadSearchPreview', function(message) {
      $Core_IM.im_debug_mode && console.log('On loadSearchPreview');
      // update search preview text
      var users = message.thread_id.split(':');
      for (var i = 0; i < users.length; i++) {
        if (users[i] == $Core_IM.getUser().id) {
          continue;
        }
        if (message.deleted) {
          message.text = oTranslations['this_message_has_been_deleted'];
        }
        if (message.text === '') {
          message.text = $Core_IM.file_preview;
        }
        var preview = $('.pf-im-panel[data-friend-id="' + users[i] + '"]').
          find('.pf-im-panel-preview');
        preview.removeClass('twa_built');
        preview.text(message.text);
        $Core.loadInit();
      }
    });

    $Core_IM.socket.on('loadConversation', function(threads) {
      $Core_IM.im_debug_mode && console.log('loadConversation');
      var u = '',
        c = $('.chat-row'),
        cache = {},
        iteration = false;

      // This case is newly chat or load more but have no messages
      if (threads.length === 0) {
        $('.pf-chat-row-loading').remove();
        c.unbind('scroll');
        return;
      }

      if ($Core_IM.load_first_time) {
        $.tmpl($Core_IM.loading_conversation_tmpl, {
          'LoadingConversation': oTranslations['loading_conversation'],
        }).prependTo('#pf-chat-window');
        c.hide();
      }

      threads.reverse();
      $Core_IM.scrollBottom = c[0].scrollHeight - c[0].scrollTop;
      for (var i in threads) {
        var thread = $.parseJSON(threads[i]);

        if (!iteration) {
          iteration = true;
          var k = thread.thread_id.split(':');
          for (var i2 in k) {
            if (typeof(cache[k[i2]]) !== 'string') {
              cache[k[i2]] = '1';
              u += k[i2] + ',';
            }
          }
        }

        c.prepend($Core_IM.buildMessage(thread));
      }
      $Core.loadInit();

      $.ajax({
        url: PF.url.make('/im/panel'),
        data: 'users=' + u,
        contentType: 'application/json',
        'success': function(e) {
          $('.pf-chat-message').each(function() {
            var t = $(this), id = t.data('user-id'), u = e[id];
            if (typeof u !== 'undefined' && typeof u.photo_link !== 'undefined' &&
              (typeof pf_im_blocked_users === 'undefined' ||
                pf_im_blocked_users.indexOf(id) === -1)) {
              t.find('.pf-chat-image').
                html($Core_IM.addTargetBlank(u.photo_link));
            }
            $Core_IM.updateChatTime();

            t.show();
          });

          var c = $('.chat-row'),
            chat_row_users = $('.chat-row-users');
          c.show();
          if ($Core_IM.load_first_time && chat_row_users.html() === '') {
            for (var i in e) {
              var u = e[i];
              if (u === false) {
                $.tmpl($Core_IM.deleted_user_tmpl, {
                  'UserName': oTranslations['deleted_user'],
                  'ShortName': 'DU',
                }).prependTo('.chat-row-users');
              } else if (typeof pf_im_blocked_users !== 'undefined' && pf_im_blocked_users.indexOf(u.id) !== -1) {
                $.tmpl($Core_IM.invalid_user_tmpl, {
                  'UserName': oTranslations['invalid_user'],
                  'ShortName': 'IU',
                }).prependTo('.chat-row-users');
              }
              else if (u.photo_link !== 'undefined') {
                chat_row_users.prepend('<span>' + $Core_IM.addTargetBlank(u.photo_link) + '</span>');
              }
            }
            c.scrollTop(c[0].scrollHeight);
            $('.pf-chat-window-loading').remove();
            $Core_IM.load_first_time = false;
          }
          else {
            c.scrollTop(c[0].scrollHeight - $Core_IM.scrollBottom);
            $('.pf-chat-row-loading').remove();
          }

          $Core_IM.reloadImages();
          $Core.loadInit();
        },
      });
    });

    $Core_IM.socket.on('loadNotification', function(notification_enable) {
      if (notification_enable === false) {
        $('#chat-action-noti').attr('class', 'fa fa-bell-slash');
      }
    });

    $Core_IM.socket.on('chat_delete', function(key, id) {
      var message = $('#' + key);
      if (message.length === 1) {
        message.find('.pf-im-chat-text').
          html('<i>' + oTranslations['this_message_has_been_deleted']+
            '</i>');
        message.find('.pf-im-chat-link').parent().remove();
        message.find('.im_attachment').remove();
      }
      $('.pf-im-panel[data-thread-id="' + id + '"]').
        find('.pf-im-panel-preview').
        text(oTranslations['this_message_has_been_deleted']);
    });

    $Core_IM.socket.on($Core_IM.host + 'chat', function(chat) {
      var sameUser = chat.user.id === $Core_IM.getUser().id,
        isImPopup = window.location.href.search('/im/popup'),
        c = $('.chat-row'),
        total_new = 0;
      if (chat.user.id !== $Core_IM.getUser().id &&
        chat.thread_id.indexOf($Core_IM.getUser().id) !== -1 &&
        $Core_IM.notificationIsEnabled(chat.notification)) {
        var sound = $('#pf-im-notification-sound').get(0);
        sound.volume = 0.5;
        sound.play();
      }
      if (chat.user.id !== $Core_IM.getUser().id &&
        chat.thread_id.indexOf($Core_IM.getUser().id) !== -1 &&
        (isImPopup !== -1)) {
        document.title = '(' + chat.new + ') ' + chat.user.name + ' ' +
          oTranslations['messaged_you'];
      }

      var users = chat.thread_id.split(':'), total_friends = 0;
      for (var i in users) {
        if ($Core_IM.getUser().id == users[i]) {
          total_friends++;
        }
      }

      if (!total_friends) {
        $Core_IM.im_debug_mode &&
        console.log('Unable to chat with this user.');
        return;
      }

      if (!$('#pf-im').is(':visible')) {
        $Core_IM.im_debug_mode && console.log('not visible...');
      }

      // newly chat
      var chat_row = $('.chat-row[data-thread-id="' + chat.thread_id + '"]');
      if ((!chat_row.length) ||
        (chat_row.length && !chat_row.is(':visible'))) {
        $Core_IM.im_debug_mode &&
        console.log('thread does not exist: ' + chat.thread_id);
        if (chat.text === '') {
          chat.text = $Core_IM.file_preview;
        }
        if (!$('.pf-im-panel[data-thread-id="' + chat.thread_id +
            '"]').length) {
          $Core_IM.im_debug_mode &&
          console.log('does not exist in panel either: ' + chat.thread_id);
          var html = '';
          if (sameUser) {
            html = '<div class="pf-im-panel" data-user-id="' +
              chat.receiver.id + '" data-thread-id="' + chat.thread_id +
              '">' +
              '<div class="pf-im-panel-image">' + chat.receiver.photo_link +
              '</div>' +
              '<div class="pf-im-panel-content">' + chat.receiver.name +
              '<div class="pf-im-panel-preview">' +
              ((typeof(chat.deleted) !== 'undefined' && chat.deleted)
                ? '<i>' + oTranslations['this_message_has_been_deleted']+
                '</i>'
                : $Core_IM.fixChatMessage(chat.text, false)) +
              '</div></div>' +
              '<div class="pf-im-panel-info"><span class="badge"></span></div>' +
              '</div>';
          }
          else {
            html = '<div class="pf-im-panel new" data-user-id="' +
              chat.user.id + '" data-thread-id="' + chat.thread_id + '">' +
              '<div class="pf-im-panel-image">' + chat.user.photo_link +
              '</div>' +
              '<div class="pf-im-panel-content">' + chat.user.name +
              '<div class="pf-im-panel-preview">' +
              ((typeof(chat.deleted) !== 'undefined' && chat.deleted)
                ? '<i>' + oTranslations['this_message_has_been_deleted'] +
                '</i>'
                : $Core_IM.fixChatMessage(chat.text, false)) +
              '</div></div>' +
              '<div class="pf-im-panel-info"><span class="badge"></span></div>' +
              '</div>';
          }
          $('.pf-im-main').prepend(html);
          $Core_IM.updateChatTime();
        }

        var panel = $('.pf-im-panel[data-thread-id="' + chat.thread_id +
          '"]'),
          t = panel.clone();
        panel.remove();

        t.prependTo('.pf-im-main');
        if (!sameUser) {
          // update counter
          $Core_IM.threadMessageCounter(chat.thread_id);
          if ($Core_IM.notificationIsEnabled(chat.notification)) {
            t.addClass('count');
          }
        }
        if (typeof(chat.deleted) !== 'undefined' && !chat.deleted) {
          // update preview message on left side chat
          var preview = t.find('.pf-im-panel-preview');
          preview.html($Core_IM.fixChatMessage(chat.text, false));
          preview.removeClass('twa_built');
        }
        $Core.loadInit();

        total_new = $('.pf-im-panel.count').length;
        if (!sameUser && total_new &&
          $Core_IM.notificationIsEnabled(chat.notification)) {
          $('span#js_total_new_messages').html(total_new).show();
        }

        return;
      }

      var pre = $('.pf-im-panel[data-thread-id="' + chat.thread_id + '"]').
        find('.pf-im-panel-preview');
      if (typeof(chat.deleted) !== 'undefined' && !chat.deleted) {
        pre.removeClass('twa_built');
        pre.html($Core_IM.fixChatMessage(chat.text, false));
      }
      else {
        pre.html(oTranslations['this_message_has_been_deleted']);
      }

      c.append($Core_IM.buildMessage(chat, false, true));
      $Core_IM.updateChatTime();
      c.scrollTop(c[0].scrollHeight);
      $Core.loadInit();
    });

    $Core_IM.socket.on('search_message', function(result, index) {
      var pisr = $('.pf-im-search-result');
      (index == 0) && pisr.empty();
      for (var i = 0; i < result.length; i++) {
        var message = JSON.parse(result[i]),
          html = $Core_IM.buildMessage(message, false, true, true);
        pisr.append(html);
      }
      if (pisr.is(':empty')) {
        pisr.append('<span class="pf-im-no-message">' +
          oTranslations['no_message'] + '</span>');
      }
      $Core_IM.updateChatTime();
      $Core.loadInit();
    });

    $Core_IM.socket.on('update_new', function(thread, total, is_last) {
      $Core_IM.im_debug_mode && console.log('On update_new');
      thread = JSON.parse(thread);
      var thread_id = thread.thread_id,
        notification;
      if (typeof thread.notification === 'undefined') {
        notification = thread.thread_id.split(':');
        $Core_IM.socket.emit('add_notification', thread.thread_id);
      }
      else {
        notification = thread.notification.split(':');
      }
      if ($('.chat-row[data-thread-id="' + thread_id + '"]').length === 0) {
        var p = $('.pf-im-panel[data-thread-id="' + thread_id + '"]');
        if (parseInt(total) > 0) {
          p.find('.badge').text(total);
          if (!p.hasClass('new')) {
            p.addClass('new');
            if (notification.indexOf($Core_IM.getUser().id.toString()) !==
              -1) {
              p.addClass('count');
              // update message counter last
              if (typeof is_last !== 'undefined') {
                $('span#js_total_new_messages').
                  html($('.pf-im-panel.count').length).
                  show();
              }
            }
            if ((parseInt(total) > 0 || 0) > 0) {
              p.find('.badge').text(total);
            }
          }
        }
        else {
          p.removeClass('new');
        }
      }
    });

    $Core_IM.socket.on($Core_IM.host + 'resetCounterAndTitle',
      function(user_id, thread_id) {
        if (user_id == $Core_IM.getUser().id) {
          // reset counter
          var panel = $('.pf-im-panel[data-thread-id="' + thread_id + '"]'),
            badge = panel.find('.badge'),
            isImPopup = window.location.href.search('/im/popup');

          badge.text('0');
          panel.removeClass('new');
          // reset title
          if (isImPopup !== -1) {
            document.title = window.pf_im_site_title;
          }
        }
      });
  }
};

// Override main compose function
if (pf_im_node_server !== '') {
  $Core.composeMessage = $Core_IM.composeMessage;
}

if (/iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
        navigator.userAgent)) {
  $Core_IM.is_mobile = true;
}

$Ready(function() {
  $Core_IM.init();
  if (typeof pf_im_using_host !== 'undefined' && pf_im_using_host &&
      ($('.pf_im_is_hosted').length === 0)) {
    var html = '<div class="pf_im_is_hosted"><span>Active Hosting: Starter at $5 / month</span></div>';
    $('input[name="val[value][pf_im_node_server]"]').val(pf_im_node_server).
        attr('disabled', true).
        after(html);
    $('.app_grouping').show();
  }
});