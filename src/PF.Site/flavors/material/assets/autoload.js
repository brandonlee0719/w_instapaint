
$Ready(function() {
    //Header form search. clear search
	$.fn.clearSearch = function(options) {
		var settings = $.extend({
			'clearClass' : 'clear_input',
			'focusAfterClear' : true,
			'linkText' : '<span class="ico ico-close"></span>'
		}, options);
		return this.each(function() {
					var $this = $(this), btn,
						divClass = settings.clearClass + '_div';

					if (!$this.parent().hasClass(divClass)) {
						$this.wrap('<div style="position: relative;" class="' + divClass + '"></div>');
						$this.after('<a style="position: absolute; cursor: pointer;" class="'
							+ settings.clearClass + '">' + settings.linkText + '</a>');
					}
					btn = $this.next();

					function clearField() {
						$this.val('').change();
						triggerBtn();
						if (settings.focusAfterClear) {
							$this.focus();
						}
						if (typeof (settings.callback) === 'function') {
							settings.callback();
						}
					}

					function triggerBtn() {
						if (hasText()) {
							btn.show();
						} else {
							btn.hide();
						}
						update();
					}

					function hasText() {
						return $this.val().replace(/^\s+|\s+$/g, '').length > 0;
					}

					function update() {
					}

					if ($this.prop('autofocus')) {
						$this.focus();
					}

					btn.on('click', clearField);
					$this.on('keyup keydown change focus', triggerBtn);
					triggerBtn();
				});
	};
    
	$('#header_sub_menu_search_input').clearSearch({});
	//scale auto text area in block invite friend
	 var textarea = document.getElementById('personal_message');
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
    };
	//Navigation responsive
	$('.btn-nav-toggle').on("click", function(){
		$(".nav-mask-modal").addClass("in");
		$('body').addClass("overlap");
	});

	$('.nav-mask-modal').on("click touchend", function(){
		$(".main-navigation").removeClass("in");
		$('body').removeClass("overlap");
		$(this).removeClass("in");
	});

	$(".site-menu-small .ajax_link, .site-logo-link").on("click", function(){
		$(".nav-mask-modal").removeClass("in");
		$('body').removeClass("overlap");
	});
	
	//Sticky bar search on mobile
	$('.form-control-feedback').on("click", function(){
		$('.sticky-bar-inner').toggleClass('overlap');
	});

	//return search on mobile
	$('.btn-globalsearch-return').on("click", function(){
		$('.sticky-bar-inner').removeClass('overlap');
	});

	//Add class when input focus
	$('.form-control').focus( function() {
		var parent = $(this).parent('.input-group');
		if(parent){
			parent.addClass('focus');
		}
	});

	$('.form-control').blur( function() {
		$('.input-group').removeClass('focus');
	});

	// Just init custom scrollbar on desktop view.
	if(!(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )){
		//Init scrollbar
		$(".user-sticky-bar .panel-items, .friend-search-invite-container, #js_main_mail_thread_holder .mail_messages, .js_box_content .item-membership-container, #welcome_message .custom_flavor_content, .dropdown-menu-limit, .attachment-form-holder .js_attachment_list").mCustomScrollbar({
			theme: "minimal-dark",
		}).addClass('dont-unbind-children');
	
		$("#div_compare_wrapper").mCustomScrollbar({
			theme: "minimal-dark",
			axis:"x" // horizontal scrollbar
		}).addClass('dont-unbind-children');
	
		$(".attachment_holder_view").mCustomScrollbar({
			theme: "dark"
		}).addClass('dont-unbind-children');
	
		//Init scrollbar landingpage
		$('#js_block_border_user_register .content').mCustomScrollbar({
			theme: "dark-thin",
			scrollbarPosition: "inside",
		}).addClass('dont-unbind-children');

		PF.event.on('before_cache_current_body', function() {
      $('.mCustomScrollbar').mCustomScrollbar('destroy');
    });
	}


	//toggle for sign-up/sign-in form in landing page
	$(document).on('click', '.js-slide-visitor-form a.js-slide-btn', function(){
		$('.js-slide-visitor-form').toggle();
		var parent = $(this).closest('.js-slide-visitor-form');
		var block_title = parent.data('title');
		if (block_title && $('#js_block_border_user_register').length > 0) {
      $('#js_block_border_user_register').find('.title:first').html(block_title);
		}
	});

	if ($('#js-btn-collapse-main-nav:not(.built)').length > 0) {
    $Material.updateMainNav();
		$('#js-btn-collapse-main-nav').addClass('built');
	}

	//add class for category when collapse
  $(".core-block-categories ul.collapse").on('shown.bs.collapse', function(){
    $(this).closest('li.category').addClass('opened');
  });

  $(".core-block-categories ul.collapse").on('hidden.bs.collapse', function(){
    $(this).closest('li.category').removeClass('opened');
  });
});

//Scroll to top
$(window).scroll(function(){
	if($(window).scrollTop() >= 10) {
		$('.btn-scrolltop').fadeIn();
	} else {
		$('.btn-scrolltop').fadeOut();
	}
});

$(document).on('click', '[data-action="submit_search_form"]', function() {
  $(this).closest('form').submit();
});

$(document).on('click', '#hd-notification [data-dismiss="alert"]', function(evt) {
  evt.stopPropagation();
});



function page_scroll2top(){
	$('html,body').animate({
		scrollTop: 0
	}, 'fast');
}

$Core.updateCommentCounter = function(module_id, item_id, str) {
	var sId = '#js_feed_like_holder_' + module_id + '_' + item_id + ', #js_feed_mini_action_holder_' + module_id + '_' + item_id;
	if ($(sId).length && $(sId).find('.feed-comment-link .counter').length) {
		$(sId).each(function(){
			var count = $(this).find('.feed-comment-link .counter').first().text();
			if (!count) {
				count = 0;
			}
			if (str == '+') {
				count = parseInt(count) + 1;
			}
			else {
				count = parseInt(count) - 1;
			}
			count = count <= 0 ? '' : count;
			$(this).find('.feed-comment-link .counter').first().text(count);
        })
	}
};

var $Material = {
	updateMainNav: function() {
    var selectedMenu = $('.site_menu li a.menu_is_selected:first');
    var html = selectedMenu.length ? selectedMenu.html() : '';
    $('#js-btn-collapse-main-nav').html(html);
	}
}
PF.event.on('on_page_change_end', function() {
  $Material.updateMainNav();
});

$Core.FriendRequest = {
  panel: {
    accept: function(requestId, message) {
      var requestRow = $('#drop_down_' + requestId, '#request-panel-body');

      $('.info', requestRow).text(message);
      $('.panel-actions', requestRow).remove();
      requestRow.addClass('friend-request-accepted');

      // update counter
      $Core.FriendRequest.panel.descreaseCounter();

      setTimeout(function() {
        $('.panel-item-content', requestRow).slideUp(200, function() {
          requestRow.remove();
          $Core.FriendRequest.panel.checkAndClosePanel();
        });
      }, 2e3);
    },

    deny: function(requestId) {
      var requestRow = $('#drop_down_' + requestId, '#request-panel-body');

      // update counter
      $Core.FriendRequest.panel.descreaseCounter();

      $('.panel-item-content', requestRow).fadeOut(400, function() {
        requestRow.remove();
        $Core.FriendRequest.panel.checkAndClosePanel();
      });
    },

    descreaseCounter: function() {
      var friendRequestCounter = $('#js_total_friend_requests');
      if (friendRequestCounter.length === 0) {
        return;
      }

      var total = friendRequestCounter.text().match(/\(([0-9]*)\)/);
      if (typeof total === 'object' && typeof total[1] !== 'undefined') {
        total = total[1] - 1;
        if (total > 0) {
          friendRequestCounter.text('(' + total + ')');
          $('#request-view-all-count').text(total);
        } else {
          friendRequestCounter.remove();
        }
      }
    },

    checkAndClosePanel: function() {
      if ($('li', '#request-panel-body').length === 0) {
        $('#hd-request').trigger('click');
      }
    }
  },

  manageAll: {
    accept: function(requestId, message) {
      var requestRow = $('#request-' + requestId);

      $('.moderation_row', requestRow).remove();
      $('.item-info', requestRow).text(message);
      $('#drop_down_' + requestId, requestRow).remove();
      requestRow.addClass('friend-request-accepted');
      setTimeout(function() {
        requestRow.fadeOut(400, function() {
          $(this).remove();
          $Core.FriendRequest.manageAll.checkReload();
        });
      }, 2e3);
    },

    deny: function(requestId) {
      $('#request-' + requestId).slideUp(400, function() {
        $('#request-' + requestId).remove();
        $Core.FriendRequest.manageAll.checkReload();
      });
    },

    checkReload: function() {
      if ($('#collection-friends-incoming').children().length === 0) {
        window.location.reload();
      }
    }
  }
}