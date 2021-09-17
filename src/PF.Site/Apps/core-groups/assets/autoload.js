$Core.Groups = {
  searching: false,
  searchAction: null,
  cmds: {
    add_new_group: function(ele, evt) {
      tb_show(oTranslations['add_new_group'],
          $.ajaxBox('groups.addGroup', 'height=400&width=550&type_id=' +
              ele.data('type-id')));
      return false;
    },

    select_category: function(ele, evt) {
      $('[class^=select-category-]').hide();
      $('.select-category-' + ele.val()).show();
      $('#select_sub_category_id').val(0);
    },

    add_group_process: function(ele, evt) {
      evt.preventDefault();
      ele.ajaxCall('groups.add');
      // disable submit button
      var submit = $('input[type="submit"]', ele);
      submit.prop('disabled', true).addClass('submitted');
    },

    widget_add_form: function(ele, evt) {
      if (ele.val() === '1') {
        $('#js_groups_widget_block').slideUp('fast');
      } else {
        $('#js_groups_widget_block').slideDown('fast');
      }
    },

    init_drag: function(ele) {
      Core_drag.init({table: ele.data('table'), ajax: ele.data('ajax')});
    },

    search_member: function(ele, evt) {
      if ($Core.Groups.searching === true) {
        return;
      }

      clearTimeout($Core.Groups.searchAction);
      $Core.Groups.searchAction = setTimeout(function() {
        // process searching
        $Core.Groups.searching = true;

        var parentBlock = $('.groups-block-members'),
            activeTab = $('li.active a', parentBlock),
            container = $(ele.data('container')),
            resultContainer = $(ele.data('result-container')),
            spinner = $('.groups-searching', parentBlock);

        if (ele.val()) {
          container.addClass('hide');
          spinner.removeClass('hide');
          $.ajaxCall('groups.getMembers', 'tab=' + activeTab.data('tab') + '&container=' + ele.data('result-container') + '&group_id=' + ele.data('group-id') + '&search=' + ele.val());
        } else {
          container.removeClass('hide');
          resultContainer.addClass('hide');
          $Core.Groups.searchingDone();
        }
      }, 500);
    },

    change_tab: function(ele, evt) {
      evt.preventDefault();
      var container = $(ele.data('container')),
          resultCotainer = $(ele.data('result-container'));

      // hide search result div, show container div
      container.hasClass('hide') && container.removeClass('hide');
      !resultCotainer.hasClass('hide') && resultCotainer.addClass('hide');

      // only show moderation in `all members` tab
      if (ele.data('tab') === 'all') {
        $('.moderation_placeholder').removeClass('hide');
      } else {
        $('.moderation_placeholder').addClass('hide');
      }

      // ajax call to get tab members
      $.ajaxCall('groups.getMembers', 'tab=' + ele.data('tab') + '&container=' + ele.data('container') + '&group_id=' + ele.data('group-id'));
    },

    remove_member: function(ele, evt) {
      $Core.jsConfirm({
        message: ele.data('message')
      }, function() {
        $.ajaxCall('groups.removeMember', 'group_id=' + ele.data('group-id') + '&user_id=' + ele.data('user-id'))
      }, function() {});
    },

    remove_pending: function(ele, evt) {
      $Core.jsConfirm({
        message: ele.data('message')
      }, function() {
        $.ajaxCall('groups.removePendingRequest', 'sign_up=' + ele.data('signup-id') + '&user_id=' + ele.data('user-id'));
      }, function() {});
    },

    remove_admin: function(ele, evt) {
      $Core.jsConfirm({
        message: ele.data('message')
      }, function() {
        $.ajaxCall('groups.removeAdmin', 'group_id=' + ele.data('group-id') + '&user_id=' + ele.data('user-id'))
      }, function() {});
    },

    disable_submit: function(form) {
      $('input[type="submit"]', form).prop('disabled', true).addClass('submitted');
    },

    join_group: function(ele, evt) {
      ele.fadeOut('fast', function() {
        if (ele.data('is-closed') == 1) {
          ele.prev().fadeIn('fast');
          $.ajaxCall('groups.signup', 'page_id=' + ele.data('id')); return false;
        } else {
          ele.prev().prev().fadeIn('fast');
          $.ajaxCall('like.add', 'type_id=groups&item_id=' + ele.data('id')); return false;
        }
      });
    },
  },

  setAsCover: function(iPageId, iPhotoId) {
    $.ajaxCall('groups.setCoverPhoto', 'page_id=' + iPageId +
        '&photo_id=' + iPhotoId);
  },

  removeCover: function(iPageId) {
    $Core.jsConfirm({}, function() {
      $.ajaxCall('groups.removeCoverPhoto', 'page_id=' + iPageId);
    }, function() {});
  },

  resetSubmit: function() {
    $('input[type="submit"].submitted').each(function() {
      $(this).prop('disabled', false).removeClass('submitted');
    });
  },

  searchingDone: function(searchingDone) {
    $Core.Groups.searching = false;
    $('.groups-searching').addClass('hide');

    if (typeof searchingDone === 'boolean' && searchingDone) {
      $('.search-member-result').removeClass('hide');
    }
  },
  
  updateCounter: function(selector) {
    var ele = $(selector),
        counter = ele.html().substr(1, ele.html().length - 2);

    ele.html('('+ (parseInt(counter) - 1) +')');
  }
};

$(document).on('click', '[data-app="core_groups"]', function(evt) {
  var action = $(this).data('action'),
      type = $(this).data('action-type');
  if (type === 'click' && $Core.Groups.cmds.hasOwnProperty(action) &&
      typeof $Core.Groups.cmds[action] === 'function') {
    $Core.Groups.cmds[action]($(this), evt);
  }
});

$(document).on('change', '[data-app="core_groups"]', function(evt) {
  var action = $(this).data('action'),
      type = $(this).data('action-type');
  if (type === 'change' && $Core.Groups.cmds.hasOwnProperty(action) &&
      typeof $Core.Groups.cmds[action] === 'function') {
    $Core.Groups.cmds[action]($(this), evt);
  }
});

$(document).on('submit', '[data-app="core_groups"]', function(evt) {
  var action = $(this).data('action'),
      type = $(this).data('action-type');
  if (type === 'submit' && $Core.Groups.cmds.hasOwnProperty(action) &&
      typeof $Core.Groups.cmds[action] === 'function') {
    $Core.Groups.cmds[action]($(this), evt);
  }
});

$(document).on('keyup', '[data-app="core_groups"]', function(evt) {
  var action = $(this).data('action'),
      type = $(this).data('action-type');
  if (type === 'keyup' && $Core.Groups.cmds.hasOwnProperty(action) &&
      typeof $Core.Groups.cmds[action] === 'function') {
    $Core.Groups.cmds[action]($(this), evt);
  }
});

$(document).on('click', '#js_groups_add_change_photo', function() {
  $('#js_event_current_image').hide();
  $('#js_event_upload_image').fadeIn();
});

$Behavior.groupsInitElements = function() {
  $('[data-app="core_groups"][data-action-type="init"]').each(function() {
    var t = $(this);
    if (t.data('action-type') === 'init' &&
        $Core.Groups.cmds.hasOwnProperty(t.data('action')) &&
        typeof $Core.Groups.cmds[t.data('action')] === 'function') {
      $Core.Groups.cmds[t.data('action')](t);
    }
  });
};

$Behavior.contentHeight = function() {
  $('#content').height($('.main_timeline').height());
};

$Behavior.fixSizeTinymce = function() {
  //The magic code to add show/hide custom event triggers
  (function($) {
    $.each(['show', 'hide'], function(i, ev) {
      var el = $.fn[ev];
      $.fn[ev] = function() {
        this.trigger(ev);
        return el.apply(this, arguments);
      };
    });
  })(jQuery);

  $('#js_groups_block_info').on('show', function() {
    $('.mceIframeContainer.mceFirst.mceLast iframe').height('275px');
  });
};

// Creating/Editing groups
if ($Core.exists('#js_groups_add_holder')) {
  $(document).on('change', '.groups_add_category select', function() {
    var detailBlock = $('#js_groups_block_detail');
    $('.js_groups_add_sub_category', detailBlock).hide();
    console.log($('.js_groups_add_sub_category', detailBlock));
    $('#js_groups_add_sub_category_' + $(this).val(), detailBlock).show();
    $('#js_category_groups_add_holder').
        val($('#js_groups_add_sub_category_' + $(this).val() +' option:first', detailBlock).val());
  });

  $(document).on('change', '.js_groups_add_sub_category select', function() {
    $('#js_category_groups_add_holder').val($(this).val());
  });
}