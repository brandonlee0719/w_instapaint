var _debug = false;
var PF = {
    events: {},
    tools: {
        versionCompare: function (v1, v2, operator) {
            this.php_js = this.php_js || {};
            this.php_js.ENV = this.php_js.ENV || {};

            var i = 0,
                x = 0,
                compare = 0,
                vm = {
                    'dev': -6,
                    'alpha': -5,
                    'a': -5,
                    'beta': -4,
                    'b': -4,
                    'RC': -3,
                    'rc': -3,
                    '#': -2,
                    'p': 1,
                    'pl': 1
                },
                prepVersion = function (v) {
                    v = ('' + v)
                        .replace(/[_\-+]/g, '.');
                    v = v.replace(/([^.\d]+)/g, '.$1.')
                        .replace(/\.{2,}/g, '.');
                    return (!v.length ? [-8] : v.split('.'));
                };

            numVersion = function (v) {
                return !v ? 0 : (isNaN(v) ? vm[v] || -7 : parseInt(v, 10));
            };
            v1 = prepVersion(v1);
            v2 = prepVersion(v2);
            x = Math.max(v1.length, v2.length);
            for (i = 0; i < x; i++) {
                if (v1[i] == v2[i]) {
                    continue;
                }
                v1[i] = numVersion(v1[i]);
                v2[i] = numVersion(v2[i]);
                if (v1[i] < v2[i]) {
                    compare = -1;
                    break;
                } else if (v1[i] > v2[i]) {
                    compare = 1;
                    break;
                }
            }
            if (!operator) {
                return compare;
            }

            switch (operator) {
                case '>':
                case 'gt':
                    return (compare > 0);
                case '>=':
                case 'ge':
                    return (compare >= 0);
                case '<=':
                case 'le':
                    return (compare <= 0);
                case '==':
                case '=':
                case 'eq':
                    return (compare === 0);
                case '<>':
                case '!=':
                case 'ne':
                    return (compare !== 0);
                case '':
                case '<':
                case 'lt':
                    return (compare < 0);
                default:
                    return null;
            }
        }
    },

    url: {
        make: function (url, params) {
            url = getParam('sBaseURL') + trim(url, '/');
            if (params) {
                url += '?';
                var iteration = 0;
                for (var i in params) {
                    iteration++;
                    if (iteration != 1) {
                        url += '&';
                    }
                    url += i + '=' + params[i];
                }
            }

            return url;
        },

        send: function (url, relocation) {
            url = (url.substr(0, 7) == 'http://' ? url : PF.url.make(url));
            if (relocation) {
                window.location.href = url;
                return;
            }

            history.pushState(null, null, url);
        }
    },

    event: {
        trigger: function (name, param) {
            if (typeof(PF.events[name]) != 'object') {
                return;
            }

            $.each(PF.events[name], function (name, callbacks) {
                this(this, param);
            });
        },

        on: function (name, callback) {
            if (typeof(PF.events[name]) != 'object') {
                PF.events[name] = new Array();
            }
            PF.events[name].push(callback);
        }
    },

    popup: function (url) {
        url = this.url.make(url);
        var popup = $('<a href="' + url + '" class="popup"></a>');
        popup.prependTo('body');
        $Core.loadInit();
        popup.trigger('click');
    },
    clicks: {},
    add: function (cmd, cb) {
        this.clicks[cmd] = cb;
        return this;
    },
    waitUntil: function (when, cb) {
        if (when()) {
            cb();
        } else {
            window.setTimeout(PF.waitUntil, 1e3, when, cb);
        }
    },
    _cmds: {},
    cmd: function (name, cb) {
        this._cmds[name] = cb;
        return this;
    }
};

(function ($) {

    $(document).on('click','.js_item_active_link',function()
    {
        var aParams = $.getParams(this.href),
            sParams = '',
            sVar = '',
            ctn  =  $(this).closest('td,th');

        for (sVar in aParams)
        {
            sParams += '&' + sVar + '=' + aParams[sVar] + '';
        }
        sParams = sParams.substr(1, sParams.length);

        if ($(this).hasClass('js_remove_default'))
        {
            $('.js_remove_default').each(function()
            {
                $('.js_item_is_active:first',ctn).addClass('hide').hide();
                $('.js_item_is_not_active:first',ctn).removeClass('hide').show();
            });
        }

        if (aParams['active'] == '1')
        {
            $('.js_item_is_not_active:first',ctn).addClass('hide').hide();
            $('.js_item_is_active:first',ctn).removeClass('hide').show();
        }
        else
        {
            $('.js_item_is_active:first',ctn).addClass('hide').hide();
            $('.js_item_is_not_active:first',ctn).removeClass('hide').show();
        }

        $Core.ajaxMessage();
        $.ajaxCall(aParams['call'], sParams + '&global_ajax_message=true');

        return false;
    });

    $(document).on('click', '[data-cmd]', function (evt) {
        var name = $(this).data('cmd') || 'empty';
        if (PF._cmds.hasOwnProperty(name)) {
            return PF._cmds[name]($(this), evt);
        }
    }).on('core.moderation_changed', function () {
        var number = $('.js_global_item_moderate:checked').length;
        if (!number) {
            $('#moderation_drop_down').addClass('hide');
        } else {
            $('#moderation_badge').html(number);
            $('#moderation_drop_down').removeClass('hide');
        }
    }).on('change', '.js_global_item_moderate', function () {
      $(document).trigger('core.moderation_changed');

      var t = $(this),
          parentModerationRow = t.closest('.moderation_row').parent();

      if (t.prop('checked')) {
        parentModerationRow.addClass('active');
      } else {
        parentModerationRow.removeClass('active');
      }
    });

    PF.cmd('core.moderation_action', function (btn, evt) {
      var rel = btn.attr('rel'),
          action = btn.attr('href').replace('#', ''),
          holder = $('#js_global_multi_form_ids');
      holder.html('');

      $('.js_global_item_moderate:checked').each(function(a, b) {
        $('<input type="hidden" name="item_moderate[]" class="js_global_item_moderate"/>').
            val($(b).val()).
            appendTo(holder);
      });

      if (btn.attr('rel') == 'mail.mailThreadAction' &&
          btn.attr('href').replace('#', '') == 'forward') {
        var sGlobalModeration = '';
        $('.js_global_item_moderate').each(function() {
          sGlobalModeration += ',' + parseInt(btn.val());
        });
        $Core.box('mail.compose', 500, 'forward_thread_id=' +
            $('#js_forward_thread_id').val() + '&forwards=' +
            sGlobalModeration);
        $Core.moderationLinkClear();
      }
      else if (btn.attr('rel') == 'mail.archive' &&
          btn.attr('href').replace('#', '') == 'export') {
        btn.parents('form:first').submit();
        $Core.moderationLinkClear();
      }
      else if (btn.attr('rel') == 'mail.moderation' &&
          btn.attr('href').replace('#', '') == 'move') {
        $Core.box('mail.listFolders', 400);
      }
      else {
          var processAction = function() {
              $('.moderation_process').show();
              var params = 'action=' + btn.attr('href').replace('#', '');
              if (btn.data('extra')) {
                  params += btn.data('extra');
              }
              $('#js_global_multi_form_holder').ajaxCall(btn.attr('rel'), params);
              $Core.moderationLinkClear();
              holder.html('');
          };
          if (btn.attr('href') == '#delete') {
              $Core.jsConfirm({message: oTranslations['are_you_sure']}, function () {
                  processAction();
              }, function () {
                  holder.html('');
                  return false;
              });
          }
          else {
              processAction();
          }
      }
      evt.preventDefault();
      return false;
    }).cmd('core.tab_item',function(btn,evt){
        var ul  = btn.closest('ul'),
            pane = $(btn.attr('href'));

        $('li',ul).removeClass('active');
        btn.closest('li').addClass('active');

        $('.tab-pane',pane.closest('.tab-content')).removeClass('active');
        pane.addClass('active');

        evt.preventDefault();
        return false;

    }).cmd('core.toggle_check_all', function (btn) {
        if (btn.data('checked_all')) {
            btn.data('checked_all', false).html(btn.data('txt1'));
            btn.removeClass('active');
            $('.js_global_item_moderate').each(function() {
              var t = $(this);
              t.prop('checked', false);
              t.closest('.moderation_row').parent().removeClass('active');
            });
        } else {
            btn.data('checked_all', true).html(btn.data('txt2'));
            btn.addClass('active');
            $('.js_global_item_moderate').each(function() {
              var t = $(this);
              t.prop('checked', true);
              t.closest('.moderation_row').parent().addClass('active');
            });
        }
        $(document).trigger('core.moderation_changed');
    }).cmd('core.toggle_placeholder', function (btn) {
        btn.closest('.collapse-placeholder').toggleClass('open');
    }).cmd('core.toggle_sub_menu', function (btn, evt) {
        btn.closest('li').find('ul').toggleClass('in');
        evt.preventDefault();
        return false;
    }).cmd('core.table_sort', function (btn) {

        var arr = btn.data('table_sort').split('|'),
            val = arr[1];

        if(btn.hasClass('asc')){
            val = arr[2];
        }else if(arr[0] == 'desc' ){
            val = arr[2];
        }
        window.location.href = window.location.pathname + $.query.set(arr[3], val) + "";
    }).cmd('profile.reposition_cover_save', function () {
        $('.pages_header_cover img, .profiles_banner_bg .cover img').draggable('destroy');
        $('.profiles_banner').removeClass('editing');
        $('#save_reposition_cover').remove();
        $.ajaxCall(pf_reposition.module + '.repositionCoverPhoto', 'id=' + pf_reposition.id + '&position=' + pf_reposition.top);
    });
})(jQuery);

function getParameterByName(name) {
    var match = RegExp('[?&]' + name + '=([^&]*)').exec(decodeURIComponent(window.location.search));
    return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
}


var Admin_Demo_Message = function (message) {
    $('#admin_demo_message').remove();
    var t = $('<div id="admin_demo_message">' + message + '</div>');
    $('body').prepend(t);
    t.animate({
        'margin-bottom': 0
    });

    setTimeout(function () {
        t.animate({
            'margin-bottom': '-70px'
        });
    }, 3000);
};


var $Cache = {};
var $oEventHistory = {};
var $oStaticHistory = {};
var $bDocumentIsLoaded = false;
var $bIsSample = false;

if (typeof window.console == 'undefined') {
    window.console = {
        log: function (sTxt) {
        }
    };
}
if (typeof window.console.log == 'undefined') {
    window.console.log = function (sTxt) {
    };
}

$.fn.message = function (sMessage, sType) {
    switch (sType) {
        case 'valid':
            sClass = 'valid_message';
            break;
        case 'error':
            sClass = 'error_message';
            break;
        case 'public':
            sClass = 'public_message';
            break;
    }

    this.html(this.html() + '<div class="' + sClass + '">' + sMessage + '</\div>');

    return this;
};

$.getParams = function (sUrl) {
    var aArgs = sUrl.split('#');
    var aArgsFinal = aArgs[1].split('?');
    var aFinal = aArgsFinal[1].split('&');

    var aUrlParams = Array();

    for (count = 0; count < aFinal.length; count++) {
        var aArg = aFinal[count].split('=');

        aUrlParams[aArg[0]] = aArg[1];
    }

    return aUrlParams;
};

$.ajaxProcess = function (sMessage, sSize) {
    sMessage = (sMessage ? sMessage : getPhrase('processing'));

    if (empty(sSize)) {
        sSize = 'small';
    }

    return '<span style="margin-left:4px; margin-right:4px; font-size:9pt; font-weight:normal;"><img src="' + eval('oJsImages.ajax_' + sSize + '') + '" class="v_middle" /> ' + (sMessage === 'no_message' ? '' : sMessage + '...') + '</span>';
};

$Behavior.buildMenu = function() {
  $('[data-component="menu"]:not(\'.built\')').each(function() {
    var th = $(this),
        firstMenuItem = $('li:first', th),
        lastMenuItem = $('li:not(.explorer):last', th);

    if (typeof firstMenuItem.offset() === 'undefined' || typeof lastMenuItem.offset()  === 'undefined') {
      return;
    } else {
      var checkOffsetTop = firstMenuItem.offset().top + 20, // 20 for threshold
          lastItemOffsetTop = lastMenuItem.offset().top;
    }

    if (checkOffsetTop > lastItemOffsetTop) {
      $('>div', th).hide();
      th.addClass('built');
      th.css('overflow', 'visible');

      return;
    }

    var explorerItem = $('>li.explorer', th).removeClass('hide'),
        itemSize = $('>li:not(.explorer)', th).length,
        explorerMenu = $('ul', explorerItem);

    function shouldMoveMenuItem() {
      th.find('>li:not(.explorer):last').prependTo(explorerMenu);
      return checkOffsetTop < explorerItem.offset().top;
    }

    for (var i = 0; i < itemSize; i++) {
      if (!shouldMoveMenuItem()) {
        $('>div', th).fadeOut();
        th.addClass('built');
        th.css('overflow', 'visible');

        return;
      }
    }
  });
};

$Behavior.moderationChanged = function () {
    $(document).trigger('core.moderation_changed');
}

$Behavior.imageHoverHolder = function () {

    $('#panels .block .title').click(function () {
        var t = $(this).parent();
        if (t.find('.content').length) {
            t.find('.content').first().toggle();
        }
    });

    $('#show-side-panel').click(function () {
        var b = $('body');
        if (b.hasClass('show-side-panel-mode')) {
            b.removeClass('show-side-panel-mode');
            return;
        }

        b.addClass('show-side-panel-mode');
    });

    $('body').click(function () {
        $('.image_hover_menu_link').each(function () {
            if ($(this).hasClass('image_hover_active')) {
                $(this).removeClass('image_hover_active');
                $(this).parent().find('.image_hover_menu:first').hide();
                $(this).hide();
            }
        });
    });

    $('.image_hover_holder').mouseover(function () {
        if (!empty($(this).find('.image_hover_menu:first').html())) {
            $(this).addClass('image_hover_holder_hover').find('.image_hover_menu_link:first').show();
        }
    });

    $('.image_hover_holder').mouseout(function () {
        if (!$(this).find('.image_hover_menu_link').hasClass('image_hover_active')) {
            $(this).removeClass('image_hover_holder_hover').find('.image_hover_menu_link:first').hide();
        }
    });

    $('.image_hover_menu_link').click(function () {

        var oMenu = $(this).parent().find('.image_hover_menu:first');

        if ($(this).hasClass('image_hover_active')) {
            $(this).removeClass('image_hover_active');

            oMenu.hide();

            return false;
        }

        $('.image_hover_menu_link').each(function () {
            if ($(this).hasClass('image_hover_active')) {
                $(this).removeClass('image_hover_active');
                $(this).parent().find('.image_hover_menu:first').hide();
                $(this).hide();
            }
        });

        $(this).addClass('image_hover_active');

        oMenu.show();

        return false;
    });
};

$Behavior.targetBlank = function () {
    $('.targetBlank').click(function () {
        window.open($(this).get(0).href);
        return false;
    });
};

var bCacheIsHover = false;
$Behavior.dropDown = function () {
    $('.dropContent').mouseover(function () {
        bCacheIsHover = true;
    });

    $('.dropContent').mouseout(function () {
        bCacheIsHover = false;
    });

    $('body').click(function () {
        if (!bCacheIsHover) {
            $('.dropContent').hide();
            $('.sub_menu_bar li a').each(function () {
                if ($(this).hasClass('is_already_open')) {
                    $(this).removeClass('is_already_open');
                }
            });
        }
    });
};

/**
 * Drop down auto jump
 */
$Behavior.goJump = function () {
    $('.goJump').change(function () {
        // Empty value, do nothing
        if ($(this).get(0).value == "") {
            return false;
        }

        // Is this a delete link? If it is make sure they confirm they want to delete the item
        if ($(this).get(0).value.search(/delete/i) != -1 && !confirm(getPhrase('are_you_sure'))) {
            return false;
        }

        // All set lets send them to the new page
        window.location.href = $(this).get(0).value;
    });
};

$Behavior.inlinePopup = function () {
    $('.inlinePopup').click(function () {
        var $aParams = $.getParams($(this).get(0).href);
        var sParams = '&tb=true';
        for (sVar in $aParams) {
            sParams += '&' + sVar + '=' + $aParams[sVar] + '';
        }
        sParams = sParams.substr(1, sParams.length);

        tb_show($(this).get(0).title, $.ajaxBox($aParams['call'], sParams));

        return false;
    });
};

$Behavior.blockClick = function () {
    $('.block .menu ul li a').click(function () {
        $(this).parents('.block:first').find('li').removeClass('active');
        $(this).parent().addClass('active');

        if (this.href.match(/#/)) {
            var aParts = explode('#', this.href);
            var aParams = explode('?', aParts[1]);
            var aParamParts = explode('&', aParams[1]);
            var aRequest = [];
            for (count in aParamParts) {
                var aPart = explode('=', aParamParts[count]);

                aRequest[aPart[0]] = aPart[1];
            }

            $('.js_block_click_lis_cache').remove();
            // $(this).parents('.menu:first').find('ul').append('<li class="js_block_click_lis_cache" style="margin-top:2px;">' + $.ajaxProcess('no_message') + '</li>');
            $.ajaxCall(aParams[0], aParams[1] + '&js_block_click_lis_cache=true', 'GET');
        }

        return false;
    });
};

$Behavior.deleteLink = function () {
    $('.delete_link').click(function () {
        var obj = $(this);
        $Core.jsConfirm({}, function () {
            $aParams = $.getParams(obj.get(0).href);
            var sParams = '';
            for (sVar in $aParams) {
                sParams += '&' + sVar + '=' + $aParams[sVar] + '';
            }
            sParams = sParams.substr(1, sParams.length);

            $.ajaxCall($aParams['call'], sParams);
        }, function () {
        });

        return false;
    });
};

$Behavior.globalToolTip = function () {
    if ($('#js_global_tooltip').length <= 0) {
        $('body').prepend('<div id="js_global_tooltip" style="display:none;"></div>');
    }

    $('.js_hover_title').mouseover(function () {
        var offset = $(this).offset();
        var sContent = '';
        if ($(this).find('.js_hover_info').length && $(this).find('.js_hover_info').html() !== null && $(this).find('.js_hover_info').html().length < 1) {

        }
        else {
            $('#js_global_tooltip').css('display', 'block');

            if ($(this).find('.js_hover_info').length > 0) {
                sContent = $(this).find('.js_hover_info').html();
            }
            else {
                var oParent = $(this).parent();

                if (!empty(oParent.attr('title'))) {
                    oParent.data('title', oParent.attr('title')).removeAttr('title');
                }

                sContent = oParent.data('title');
            }

            $('#js_global_tooltip').html('<div id="js_global_tooltip_display">' + sContent + '</div>');
            $('#js_global_tooltip').css('top', (offset.top - ($('#js_global_tooltip_display').height() + 10)) + 'px');

            var pos = ($(window).width() - (offset.left + $('#js_global_tooltip').width()));
            if (pos < 10) {
                offset.left = (offset.left - $('#js_global_tooltip').width()) + 20;
            }

            $('#js_global_tooltip').css('left', (offset.left - 10) + 'px');
        }
    });

    $('.js_hover_title').mouseout(function () {
        $('#js_global_tooltip').hide()
            .html('')
            .css('top', '0px')
            .css('left', '0px');
    });
};

$Behavior.clearTextareaValue = function () {
    $('.js_comment_text_area #text').focus(function () {
        if ($(this).val() == $('#js_comment_write_phrase').html()) {
            $(this).val('');
        }
    });
};

$Behavior.loadEditor = function () {
    if ((!getParam('bWysiwyg') || typeof(bForceDefaultEditor) != 'undefined') && typeof(Editor) == 'object') {
        Editor.getEditors();
    }
};

$Ready(function () {
    $('.audio_player:not(.built)').each(function () {
        var t = $(this),
            onPlay = t.data('onplay');
        t.addClass('built');
        var audio = document.createElement('audio');
        audio.setAttribute('src', t.data('src'));
        audio.setAttribute('controls', 'controls');
        audio.setAttribute('preload', 'none');

        t.get(0).appendChild(audio);

        if (onPlay) {
            audio.addEventListener('play', function () {
                $.ajax({
                    url: onPlay
                });
            });
        }
    });

  $('[data-component="dropzone"]').each(function() {

    if (typeof $(this).data('dropzone-id') != 'undefined') {
        $Core.dropzone.dropzone_id = $(this).data('dropzone-id');
    }
    else {
        $Core.dropzone.dropzone_id = 'dropzone';
    }

    if (!$(this).prop('built')) {
        if (typeof $Core.dropzone.instance[$Core.dropzone.dropzone_id] != 'undefined' && $Core.dropzone.instance[$Core.dropzone.dropzone_id] !== null) {
            $Core.dropzone.instance[$Core.dropzone.dropzone_id].destroy();
        }
        $Core.dropzone.init($(this));
    }
  });

  PF.event.on('on_page_column_init_end', function() {
    $('[data-component="breadcrumb"]').not('.built').each(function() {
      $(this).addClass('built').asBreadcrumbs({
          onReady: function() {
              $(this.element).css({
                'overflow': 'visible',
                'max-width': '100%',
              });
          }
      });
    });
  });

  $(document).on('click touchend', '.dropzone-component .dz-preview', function () {
    var t = $(this),
        error = t.find('.dz-error-message'),
        img = t.find('img');

    if (t.find('.dz-error-message span').html() === '') {
        return;
    }
    error.css('opacity', 1);
    t.addClass('active');
    img.css('filter', 'blur(8px)');
    setTimeout(function () {
        error.css('opacity', 0);
        img.css('filter', 'none');
        t.removeClass('active');
    }, 3000);
  });
});

$Core.player = {
    load: function (params) {
        var t = $('#' + params.id),
            html = '';
        if (t.hasClass('played')) {
            return '';
        }
        t.addClass('played');
        if (params.type != 'music') {
            return '';
        }

        /*
        html = '<audio controls>' +
            '<source src="' + params.play + '" type="audio/mpeg">' +
            '</audio>';
        */

        var audio = document.createElement('audio');
        audio.setAttribute('src', params.play);
        audio.setAttribute('controls', 'controls');
        audio.setAttribute('preload', 'none');

        // audio.setAttribute("autoplay","autoplay");

        t.get(0).appendChild(audio);

        if (typeof(params.on_start) == 'function') {
            audio.addEventListener('play', params.on_start);
        }

        // t.html(audio);
        // audio.prependTo(t);
    }
};

$Core.dropzone = {
  instance: {},
  dropzone_id: '',
  init: function (t) {
    var autoProcessQueue = false,
        uploadMultiple = true;

    if (typeof t.data('auto-process-queue') !== 'undefined') {
      autoProcessQueue = t.data('auto-process-queue');
    }

    if (typeof t.data('upload-multiple') !== 'undefined') {
      uploadMultiple = t.data('upload-multiple');
    }

    if (typeof t.data('submit-button') !== 'undefined') {
      $(document).on('click', t.data('submit-button'), function() {
        if (typeof $Core.dropzone.instance[$Core.dropzone.dropzone_id] !== 'object') {
          return;
        }
        $Core.dropzone.instance[$Core.dropzone.dropzone_id].processQueue();
      });
    }
    //prevent init again
    t.prop('built',true).addClass('dont-unbind-children');

    $Core.dropzone.instance[$Core.dropzone.dropzone_id] = new Dropzone('#' + t.prop('id'), {
      url: t.data('url'),
      paramName: t.data('param-name') ? t.data('param-name') : 'file',
      maxFiles: (t.data('max-files') !== null) ? t.data('max-files') : null,
      uploadStyle: (t.data('upload-style') !== null) ? t.data('upload-style') : null,
      errorMessageOutside: (t.data('error-message-outside') !== null) ? t.data('error-message-outside') == true : null,
      maxFilesize: t.data('max-size') ? t.data('max-size') : 256, // MB
      maxThumbnailFilesize: t.data('max-size') ? t.data('max-size') : 256,
      acceptedFiles: t.data('accepted-files'),
      clickable: t.data('clickable') ? t.data('clickable') : true,
      autoProcessQueue: autoProcessQueue,
      parallelUploads: 1000,
      uploadMultiple: uploadMultiple,
      previewTemplate: t.data('preview-template') ? $(t.data('preview-template')).html() : Dropzone.prototype.defaultOptions.previewTemplate,
      dictDefaultMessage: (typeof oTranslations['dz_default_message'] !== 'undefined') ? oTranslations['dz_default_message'] : Dropzone.prototype.defaultOptions.dictDefaultMessage,
      dictFallbackMessage: (typeof oTranslations['dz_fallback_message'] !== 'undefined') ? oTranslations['dz_fallback_message'] : Dropzone.prototype.defaultOptions.dictFallbackMessage,
      dictFallbackText: (typeof oTranslations['dz_fallback_text'] !== 'undefined') ? oTranslations['dz_fallback_text'] : Dropzone.prototype.defaultOptions.dictFallbackText,
      dictFileTooBig: (typeof oTranslations['dz_file_too_big'] !== 'undefined') ? oTranslations['dz_file_too_big'] : Dropzone.prototype.defaultOptions.dictFileTooBig,
      dictInvalidFileType: (typeof oTranslations['dz_invalid_file_type'] !== 'undefined') ? oTranslations['dz_invalid_file_type'] : Dropzone.prototype.defaultOptions.dictInvalidFileType,
      dictResponseError: (typeof oTranslations['dz_response_error'] !== 'undefined') ? oTranslations['dz_response_error'] : Dropzone.prototype.defaultOptions.dictResponseError,
      dictCancelUpload: (typeof oTranslations['dz_cancel_upload'] !== 'undefined') ? oTranslations['dz_cancel_upload'] : Dropzone.prototype.defaultOptions.dictCancelUpload,
      dictCancelUploadConfirmation: (typeof oTranslations['dz_cancel_upload_confirmation'] !== 'undefined') ? oTranslations['dz_cancel_upload_confirmation'] : Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation,
      dictRemoveFile: (typeof oTranslations['dz_remove_file'] !== 'undefined') ? oTranslations['dz_remove_file'] : Dropzone.prototype.defaultOptions.dictRemoveFile,
      dictRemoveFileConfirmation: (typeof oTranslations['dz_remove_file_confirmation'] !== 'undefined') ? oTranslations['dz_remove_file_confirmation'] : Dropzone.prototype.defaultOptions.dictRemoveFileConfirmation,
      dictMaxFilesExceeded: (typeof oTranslations['dz_max_files_exceeded'] !== 'undefined') ? oTranslations['dz_max_files_exceeded'] : Dropzone.prototype.defaultOptions.dictMaxFilesExceeded,
      dropzoneId: $Core.dropzone.dropzone_id,
      init: function() {
        // on sending: append data
        this.on('sending', function(data, xhr, formData) {
          if (t.data('on-sending')) {
            $Core.executeFunctionByName(t.data('on-sending'), window, data, xhr,
                formData);
          }
        });

        // on error method
        this.on('error', function(file, message) {
          if (t.data('on-error')) {
            $Core.executeFunctionByName(t.data('on-error'), window, t, file, message);
          }
        });

        // on success method
        this.on('success', function(file, response) {
          if (t.data('on-success')) {
            $Core.executeFunctionByName(t.data('on-success'), window, t, file, response);
          }
        });

        this.on('removedfile', function(file) {
          if (typeof file.item_id !== 'undefined' && file.item_id !== 0 && t.data('on-remove') !== 'undefined') {
            $.ajaxCall(t.data('on-remove'), 'id=' + file.item_id);
          }

          if (t.data('on-removedfile')) {
            $Core.executeFunctionByName(t.data('on-removedfile'), window, t, file);
          }
          if (this.options.dropzoneId == 'photo_feed' && this.files.length == 0) {
             $bButtonSubmitActive = false;
             $('.activity_feed_form_button .button').addClass('button_not_active');
          }
        });

        // on added file
        this.on('addedfile', function(file) {
          var th = this;
          if (t.data('on-addedfile')) {
            $Core.executeFunctionByName(t.data('on-addedfile'), window, t, file);
          }
          if ((t.data('max-files') == 1 && t.data('upload-style') == 'mini') || t.data('single-mode') == true) {
            t.removeClass('dz-error').parent().children('.dz-error-message').remove();
            th.removeAllFilesExceptLast();
          }
          if (this.options.dropzoneId == 'photo_feed' && !$Core.Photo.iTotalError) {
              $bButtonSubmitActive = true;
              $('.activity_feed_form_button .button').removeClass('button_not_active');
          }
          // Create the remove button
          var removeButton = Dropzone.createElement(
              '<div class="dz-remove-file">' +
              ((typeof t.data('not-show-remove-icon') !== 'undefined') ? '<span>' : '<i class="ico ico-close"></i><span style="display:none">') +
              this.options.dictRemoveFile + '</span></div>');

          // Capture the Dropzone instance as closure.
          var _this = this;

          if (typeof t.data('not-remove-file') !== 'undefined') {
            return;
          }
          // Listen to the click event
          removeButton.addEventListener('click', function(e) {
            // Make sure the button click doesn't submit the form:
            e.preventDefault();
            e.stopPropagation();

            if (t.data('remove-button-action')) {
              $Core.executeFunctionByName(t.data('remove-button-action'), window, t);
            }
            if ((t.data('max-files') == 1 && t.data('upload-style') == 'mini') || t.data('single-mode') == true) {
              t.removeClass('dz-error').parent().find('.dz-error-message').remove();
            }

            _this.removeFile(file);

          });

          // Add the button to the file preview element.
          if ($('[data-dz-remove-file]', file.previewElement).length) {
            $('[data-dz-remove-file]', file.previewElement).append(removeButton);
          } else {
            file.previewElement.appendChild(removeButton);
          }
        });

        this.on('queuecomplete', function () {
            if (t.data('on-queuecomplete')) {
                $Core.executeFunctionByName(t.data('on-queuecomplete'), window, t);
            }
        });
        // init function
        if (t.data('on-init')) {
          $Core.executeFunctionByName(t.data('on-init'), window, t);
        }
      },
    });
  },
  destroy: function (id) {
    if (typeof id === 'undefined') {
      id = 'dropzone';
    }
    var destroyInstance = this.instance[id];
    if (typeof destroyInstance === 'object' && typeof destroyInstance.destroy === 'function') {
      // process customize destroy function
      var element = $(destroyInstance.element);
      if (typeof element !== 'undefined' && typeof element.data('on-destroy') !== 'undefined') {
        $Core.executeFunctionByName(element.data('on-destroy'), window, destroyInstance);
      }

      destroyInstance.destroy();
    }
  },
  setFileError: function(id, file, message) {
    var dropzone =  this.instance[id];
    dropzone._errorProcessing([file], message, null)
  }
};

$Core.executeFunctionByName = function(functionName, context /*, args */) {
  var args = [].slice.call(arguments).splice(2),
      namespaces = functionName.split('.'),
      func = namespaces.pop();

  for (var i = 0; i < namespaces.length; i++) {
    context = context[namespaces[i]];
  }

  return context[func].apply(context, args);
};

$Core.getCurrentFeedIds = function () {
    var sMoreFeedIds = '';
    $('.js_parent_feed_entry').each(function () {
        sMoreFeedIds += $(this).attr('id').replace('js_item_feed_', '') + ',';
    });

    return sMoreFeedIds;
};

$Core.processForm = function (sSelector, bReset) {
    if (bReset === true) {
        $(sSelector).find('.button:first').removeClass('button_off').attr('disabled', false);
        $(sSelector).find('.table_clear_ajax').hide();
    }
    else {
        $(sSelector).find('.button:first').addClass('button_off').attr('disabled', true);
        $(sSelector).find('.table_clear_ajax').show();
    }
};

$Core.exists = function (sSelector) {
    return ($(sSelector).length > 0 ? true : false);
};

$Core.searchFriends = function ($aParams) {
    if (typeof($Core.searchFriendsInput) == 'undefined') {
        return;
    }
    $Core.searchFriendsInput.init($aParams);
};

$Core.loadStaticFile = function ($aFiles) {
    $Core.loadStaticFiles($aFiles);
};

var sCustomHistoryUrl = '';
$Core.loadStaticFiles = function ($aFiles) {
    if (typeof($aFiles) == 'string') {
        $aFiles = new Array($aFiles);
    }
    if (typeof($Core.core_bundle_files) == 'undefined') {
        $Core.core_bundle_files = [];
    }

    if (!$bDocumentIsLoaded) {
        if (!isset($Cache['post_static_files'])) {
            $Cache['post_static_files'] = new Array();
        }

        $Cache['post_static_files'].push($aFiles);

        return;
    }

    /* $Core.loadInit is triggered before this function finishes loading all the JS files we use this counter to control loadInit and make it wait for all JS files*/
    $Core.dynamic_js_files = 0;

    if(typeof $aFiles == 'undefined')
        return ;

    var unique = $aFiles.filter(function (itm, i) {
        return i == $aFiles.indexOf(itm);
    });

    $aFiles = unique;

    $($aFiles).each(function ($sKey, $sFile) {
        if (substr($sFile, -3) == '.js' && !isset($oStaticHistory[$sFile]) && ($Core.core_bundle_files.indexOf($sFile) == -1)) {
            $Core.dynamic_js_files++;
        }
    });

    $($aFiles).each(function ($sKey, $sFile) {
        if (!isset($oStaticHistory[$sFile]) && ($Core.core_bundle_files.indexOf($sFile) == -1)) {
            $oStaticHistory[$sFile] = true;
            if (substr($sFile, -3) == '.js') {
                $.ajax($sFile + '?v=' + getParam('sStaticVersion') + '').always(function () {
                    $Core.dynamic_js_files--;
                });
            }
            else if (substr($sFile, -4) == '.css') {
                var sCustomId = '';
                if (substr($sFile, -10) == 'custom.css') {
                    sCustomHistoryUrl = $sFile;
                    sCustomId = 'js_custom_css_loader';
                }
                $('head').prepend('<link ' + sCustomId + ' rel="stylesheet" type="text/css" href="' + $sFile + '?v=' + getParam('sStaticVersion') + '" />');
            }
            else if(/^(https:|http:)?\/\//.test($sFile.trim())){
                $('head').prepend('<script src="'+$sFile+'" type="text/javascript" /></script>');
                $Core.dynamic_js_files--;
            }
            else {
                // console.log($sFile);
                eval($sFile);
            }
        }
        else {
            if (substr($sFile, -10) == 'custom.css') {
                sCustomHistoryUrl = $sFile;
            }
        }
    });

    if (!empty(sCustomHistoryUrl)) {
        $('#js_custom_css_loader').remove();
        $('head').append('<link id="js_custom_css_loader" rel="stylesheet" type="text/css" href="' + sCustomHistoryUrl + '?v=' + getParam('sStaticVersion') + '" />');
    }
};

var lastClassName;
$Core.openPanel = function (obj) {
    // $('body').addClass('panel_is_active');
    $('#header_search_form').hide();
    $('._search').show();
    if (lastClassName) {
        $('#panel').removeClass(lastClassName).attr('style', '');
        lastClassName = null;
    }

    PF.event.trigger('openPanel', obj);

    if (obj instanceof jQuery) {
        if (obj.find('span').length) {
            obj.find('span').html('').hide();
        }

        if (obj.hasClass('active')) {
            obj.removeClass('active');
            $('body').removeClass('panel_is_active');

            return;
        }

        if (obj.data('class')) {
            lastClassName = obj.data('class');
            var panel = $('#panel').addClass(obj.data('class'));
            panel.css({
                top: (obj.offset().top - $(window).scrollTop())
            });
        }

        $('.notifications a.active').removeClass('active');
        obj.addClass('active');
        $('body').addClass('panel_is_active').removeClass('user_block_is_active');

        $('#panel').html('<i class="fa fa-spin fa-circle-o-notch"></i>');

        $.ajax({
            url: obj.data('open'),
            contentType: 'application/json',
            success: function (e) {
                $('#panel').html(e.content);
                $('#panel').find('._block').remove();
                $Core.loadInit();
            }
        })
    }
};

$Core.ajaxLoadMorePaging = function(t, url, sendData) {
  if (t.find('.next_page').length) {
    t.find('.next_page').addClass('focus');
    $.ajax({
      url: url,
      contentType: 'application/json',
      data: sendData,
      success: function (e) {
        if (typeof(e.content) == 'string') {
          var position = $(window).scrollTop();
          if (t.data('pagination')) {
            var pager = t.parents('.pagination');
            pager.replaceWith(e.content);
          } else {
            //remove duplication content
            t.parent().find('.mail_duplication_content').remove();
            t.before(e.content);
            t.remove();
          }
          $(window).scrollTop(position);
          $Core.loadInit();
        }
        else {
          t.remove();
        }
      }
    });
  }
};

$Behavior.globalInit = function () {
    if (!oParams.hasOwnProperty('bIsAdminCP') || (oParams.hasOwnProperty('bIsAdminCP') && !oParams.bIsAdminCP)) {
        $('input[type="text"]:not(.form-control)').addClass('form-control');
    }

    $('.js_pager_view_more_link:not(.built)').each(function () {
        var t = $(this),
            isInView = false,
            lastItem = t.parent().find('.pf_video_last_item_paging').val(),
            nextPage = t.find('.next_page'),
            sPagingVar = nextPage.data('paging') ? nextPage.data('paging') : ''
        url = nextPage.attr('href');
        if (typeof lastItem == 'undefined') {
            lastItem = 0;
        } else {
            t.parent().find('.pf_video_last_item_paging').remove();
        }
        t.addClass('built');
        var sendData = 'core[ajax]=true' + (t.data('pagination') ? '&pagination=1' : '') + '&last-item=' + lastItem + '&sPagingVar=' + sPagingVar;

        if ($(window).width() > 480) {
          $(window).scroll(function () {
              if ($Core.isInView(t, 100) && !isInView) {
                $Core.ajaxLoadMorePaging(t, url, sendData);
                isInView = true;
              }
          });
        }
        else {
          nextPage.addClass('btn btn-primary btn-round btn-gradient global_view_more no_ajax_link mobile_load_more').on('click', function(event) {
            $Core.ajaxLoadMorePaging(t, url, sendData);
            event.preventDefault();
            $(this).removeClass('btn btn-primary btn-round btn-gradient global_view_more mobile_load_more');
            isInView = true;
            return false;
          });
        }
    });

    if ($('.set_to_fixed').length) {
        $('.set_to_fixed:not(.built)').addClass('dont-unbind').each(function () {
            var t = $(this),
                o = t.offset(),
                isFixed = false;

            t.addClass('built');
            $(window).scroll(function () {
                var total;
                if ($('body').hasClass(t.data('class'))) {
                    total = (o.top - ($(this).scrollTop() + t.height()));
                } else {
                    total = (o.top - $(this).scrollTop());
                }
                if (total <= 1 && !isFixed) {
                    isFixed = true;
                    $('body').addClass(t.data('class'));
                }

                if (isFixed && total >= 2) {
                    isFixed = false;
                    $('body').removeClass(t.data('class'));
                }
            });
        });
    }

    $('.user_block_toggle').click(function () {
        $('body').toggleClass('user_block_is_active');
    });

    $('.mobile_menu').click(function () {
        $('body').toggleClass('show_mobile_menu');
        $('body').removeClass('panel_is_active');
    });

    $('.feed_form_toggle').unbind().click(function () {
        $('.feed_form_menu').slideToggle('fast');
    });

    $('.feed_form_share:not(.active)').click(function () {
        var t = $(this),
            f = t.parents('form:first');

        t.addClass('feed_form_share');
        $.ajax({
            url: f.attr('action'),
            type: 'POST',
            data: f.serialize(),
            complete: function (e) {
                $Core.resetFeedForm(f);
                eval(e.responseText);
            }
        });

        return false;
    });

    $('.cancel_post').click(function () {
        $('._load_is_feed').removeClass('active');
        $('body').removeClass('panel_is_active');
        $('#panel').hide();
    });
    $('.feed_form_textarea textarea').keydown(function () {
        $Core.resizeTextarea($(this));
    });

    $('.feed_form_textarea textarea:not(.dont-unbind)').click(function () {
        var t = $(this);

        t.addClass('dont-unbind');
        t.parents('form:first').addClass('active');
    });

    $('._panel').click(function () {
        $Core.openPanel($(this));

        return false;
    });

    if ($('.mosaicflow_load:not(.built_flow)').length) {
        var mLoad = setInterval(function () {
            if (typeof(jQuery().mosaicflow) == 'function') {
                if ($('.mosaicflow_load.built_flow').length == 0) {


                    $('.mosaicflow_load').mosaicflow({
                        minItemWidth: $('.mosaicflow_load').data('width'),
                        itemHeightCalculation: 'auto'
                    });

                    $('.mosaicflow_load').addClass('built_flow');
                }
                else {
                    var firstContainer = $('.mosaicflow_load.built_flow').first();
                    $('.mosaicflow_load:not(.built_flow)').each(function () {
                        $(this).find('article.photos_row').each(function () {
                            firstContainer.mosaicflow('add', $(this));
                        });
                        $(this).remove();
                    });
                }
                clearInterval(mLoad);
            }
        }, 500);
    }

    $('.image_deferred:not(.built)').each(function () {
        var t = $(this),
            src = t.data('src'),
            i = new Image();

        t.addClass('built');
        if (!src) {
            t.addClass('no_image');
            return;
        }

        t.addClass('has_image');
        i.onerror = function (e, u) {
            t.replaceWith('');
        };
        i.onload = function (e) {
            t.attr('src', src);
        };
        i.src = src;
    });
    $('.image_load:not(.built)').each(function () {
        var t = $(this),
            src = t.data('src'),
            i = new Image();

        t.addClass('built');
        if (!src) {
            t.addClass('no_image');
            return;
        }

        t.addClass('has_image');
        i.onload = function (e) {
            if (t.hasClass('parent-block')) {
                var parentClass = t.data('apply');
                var main = t.data('main');
                if (parentClass){
                    if (main) {
                        $(main + ' .' + parentClass).css('background-image', 'url("' + src + '")');
                    }
                    else {
                        $('#main .' + parentClass).css('background-image', 'url("' + src + '")');
                    }
              }
            } else {
                t.css('background-image', 'url("' + src + '")');
            }
        };
        i.src = src;
    });

    if ($('.moderate_link').length) {
        $('.moderate_link:not(.built)').each(function () {
            var t = $(this),
                parents,
                html = '',
                obj;
            var location = t.data('id');
            if (location == 'mod') {
                t.html('<i class="fa"></i>');
            }
            t.addClass('built');
            if (t.parents('.table_row:first').length) {
                parents = t.parents('.table_row:first').parent();
            }
            else if (t.parents('.moderation_row:first').length) {
                parents = t.parents('.moderation_row:first');
            }
            else {
                parents = t.parents('article:first');
            }

            obj = $('<div class="_moderator">' + html + '</div>');

            // html += t.clone();

            parents.before(obj);

            // parents.find('.')

            if (t.parent().find('.row_edit_bar_parent').length) {

                t.parent().find('.row_edit_bar_parent').prependTo(obj);
            }
            t.prependTo(obj);
            if (location == 'user') {
                //t.parent().remove();
            }

            if (obj.width() > 0) {
                parents.addClass('has-visible-moderator');
            }
        });
    }


    // Confirm before deleting an item
    $('.sJsConfirm').click(function () {
        var message = ($(this).data('message')) ? $(this).data('message') : oTranslations['are_you_sure'],
            url = $(this).attr('href'),
            form = $(this).closest('form');

        if ($(this).is('a')) {
            if (url == '' || url == '#') {
                url = window.location.href;
            }
            $Core.jsConfirm({message: message}, function () {
                window.location.href = url;
            }, function () {
            });
            return false;
        }
        else if (($(this).is('input[type="submit"]') || $(this).is('button')) && $(this).closest('form').length > 0) {
            $('<input />').attr('type', 'hidden')
                .attr('name', $(this).attr('name'))
                .attr('value', $(this).attr('value'))
                .appendTo(form);

            $Core.jsConfirm({message: message}, function () {
                form.submit();
            }, function () {
            });
            return false;
        }
        return confirm(message);
    });

    $('#select_lang_pack').click(function () {
        tb_show(oTranslations['language_packages'], $.ajaxBox('language.select', 'height=300&amp;width=300'));

        return false;
    });

    if (!oCore['core.is_admincp']) {
        if ($('#country_iso').length > 0 && !empty(oCore['core.country_iso'])) {
            if (empty($('#country_iso').val())) {
                $('#js_country_iso_option_' + oCore['core.country_iso']).attr('selected', true);
            }
        }
    }

    $('.js_item_active').each(function () {
        var t = $(this).find('input'),
            i = t.parents('.item_is_active_holder:first');

        if (t.prop('checked')) {
            if (t.parent().hasClass('item_is_active')) {
                i.addClass('item_selection_active');
            }
            else {
                i.addClass('item_selection_not_active');
            }
        }
        else {

        }
    });

    $('.js_item_active').click(function () {
        var p = $(this).parents('.item_is_active_holder:first');

        p.removeClass('item_selection_active').removeClass('item_selection_not_active');
        $(this).parent().find('.js_item_active input').prop('checked', false);
        if ($(this).hasClass('item_is_active')) {
            $(this).parent().find('.item_is_active input').prop('checked', true);
            p.addClass('item_selection_active');
        }
        else {
            $(this).parent().find('.item_is_not_active input').prop('checked', true);
            p.addClass('item_selection_not_active');
        }

        $(this).parent().find('.item_is_active input').trigger('change');
    });

    if ($('.moderate_link').length > 0) {
        $('.moderation_drop').unbind('click');
        $('.moderation_drop').click(function () {
            if (parseInt($('.js_global_multi_total').html()) === 0) {
                return false;
            }

            if ($(this).hasClass('is_clicked')) {
                $('.moderation_holder ul').hide();
                $(this).removeClass('is_clicked');
            }
            else {
                $('.moderation_holder ul').show();
                $(this).addClass('is_clicked');
            }

            return false;
        });

        var iEmptyModLinks = 0;
        var iSelectItems = 0;
        var sView = (typeof moderationViewString !== 'undefined') ? moderationViewString : '';
        $('.moderate_link').each(function () {
            var sCookieName = 'js_' + (sView != '' ? sView + '_' : '') + 'item_m_' + $(this).attr('rel') + '_' + $(this).attr('href').replace('#', '');
            if (getCookie(sCookieName)) {
                $(this).addClass('moderate_link_active');
                $(this).parent().addClass('moderator_active');
                iSelectItems++;
            }
            else {
                iEmptyModLinks++;
            }
        });

        if (iEmptyModLinks === 0) {
            $('.moderation_action_unselect').show();
            $('.moderation_action_select').hide();
        }

        $('.js_global_multi_total').html(iSelectItems);
        $('.js_global_multi_total').parent().show();
    }

    $('.moderation_clear_all').click(function () {
        $Core.moderationLinkClear();

        return false;
    });

    $('.moderation_action').click(function () {
        var sType = $(this).attr('rel');

        $(this).hide();

        if (sType == 'select') {
            $('.moderation_action_unselect').show();
        }
        else {
            $('.moderation_action_select').show();
        }

        $('.moderate_link').each(function () {
            $Core.moderationLinkClick(this, sType);
        });

        return false;
    });

    $('.moderate_link').unbind('click');
    $('.moderate_link').click(function () {
        return $Core.moderationLinkClick(this);
    });

    $('.page_section_menu ul li a').click(function () {
        // remove error message when click another tab
        $('#core_js_messages').remove();

        var sRel = $(this).attr('rel');
        if (empty(sRel)) {
            return true;
        }
        $('.page_section_menu ul li').removeClass('active');
        $('.page_section_menu_holder').hide();
        $('#' + sRel).show();
        $(this).parent().addClass('active');

        if ($('#page_section_menu_form').length > 0) {
            $('#page_section_menu_form').val(sRel);
        }
        // set current tab
        $('#current_tab').val($(this).attr('href').replace('#', ''));

        return false;
    });

    /**
     * @since 4.5.3
     * <code>
     *     $('.js_date_picker').on('datepicker.before_show',function(){
     *       console.log('beforeshow', $(this))
     *  })
     * </code>
     */
    $('.js_date_picker').each(function () {
        var $this = $(this),
            $holder = $this.closest('.js_datepicker_holder'),
            $year = $('.js_datepicker_year', $holder),
            minYear,
            maxYear,
            sFormat = oParams['sDateFormat'];

        if (typeof pf_select_date_sort_desc !== 'undefined' && pf_select_date_sort_desc) {
            minYear = $('option:last',$year).val() || 0;
            maxYear = $('option:eq(0)',$year).val() || $('option:eq(1)',$year).val() || 0;
        } else {
            minYear = $('option:eq(0)',$year).val() || $('option:eq(1)',$year).val() || 0;
            maxYear = $('option:last',$year).val() || 0;
        }

        sFormat = sFormat.charAt(0) + '/' + sFormat.charAt(1) + '/' + sFormat.charAt(2);
        sFormat = sFormat.replace('D', 'd').replace('M', 'm').replace('Y', 'yy');

        $this.datepicker('destroy');

        if (!minYear) {
            minYear = '-100';
        }
        if (!maxYear) {
            maxYear = '+10';
        }

        $('.js_date_picker', $holder).prop('readonly', true);

        $this.datepicker({
            dateFormat: sFormat,
            changeYear: true,
            yearRange: minYear + ':' + maxYear,
            beforeShow: function () {
                $this.trigger("datepicker.before_show", minYear, maxYear);
            },
            onSelect: function (dateText) {
                var aParts = explode('/', dateText),
                    sMonth,
                    sDay,
                    sYear;

                switch (oParams['sDateFormat']) {
                    case 'YMD':
                        sMonth = ltrim(aParts[1], '0');
                        sDay = ltrim(aParts[2], '0');
                        sYear = aParts[0];
                        break;
                    case 'DMY':
                        sMonth = ltrim(aParts[1], '0');
                        sDay = ltrim(aParts[0], '0');
                        sYear = aParts[2];
                        break;
                    default:
                        sMonth = ltrim(aParts[0], '0');
                        sDay = ltrim(aParts[1], '0');
                        sYear = aParts[2];
                        break;
                }

                $('.js_datepicker_month', $holder).val(sMonth);
                $('.js_datepicker_day', $holder).val(sDay);
                $('.js_datepicker_year', $holder).val(sYear);
            }
        });

        $holder.find('.js_datepicker_image').click(function () {
            $this.datepicker('show');
        });

    });

    $('#js_login_as_page').click(function () {
        $Core.box('pages.login', 500);
        return false;
    });

    $('.mobile_view_options').click(function () {
        $('#js_mobile_form_holder').toggle();

        return false;
    });

    if (typeof $.browser != 'undefined' && $.browser.msie && parseInt($.browser.version, 10) < 8 && !getParam('bJsIsMobile')) {
        $('#js_update_internet_explorer').show();
    }
};

$Core.pageSectionMenuShow = function (sId) {
    $('.page_section_menu_holder').hide();
    $('.page_section_menu ul li').removeClass('active');
    $(sId).show();
    $('.page_section_menu ul li a').each(function () {
        if ($(this).attr('rel') == sId.replace('#', '')) {
            $(this).parent().addClass('active');

            return false;
        }
    });
};

$Core.moderationLinkClear = function () {
    var aCookies = document.cookie.split(';');
    $(aCookies).each(function (sKey, sValue) {
        if (sValue.match(/js_item_m/i)) {
            var aParts = explode('=', sValue);

            deleteCookie(trim(aParts[0].replace(getParam('sJsCookiePrefix'), '')));
        }
    });

    $('.moderate_link').removeClass('moderate_link_active');
    $('#js_global_multi_form_ids').html('');
    $('.js_global_multi_total').html('0');
    $('.moderation_holder').addClass('not_active');
    $('.moderation_holder a.moderation_drop').removeClass('is_clicked');
    $('.moderation_holder ul').hide();
    $('.moderation_action_unselect').hide();
    $('.moderation_action_select').show();
};

$Core.moderationLinkClick = function (oObj, sType) {
    var sView = (typeof moderationViewString !== 'undefined') ? moderationViewString : '';
    var sName = 'js_item_m_' + $(oObj).attr('rel') + '_' + $(oObj).attr('href').replace('#', '');
    var sCookieName = 'js_' + (sView != '' ? sView + '_' : '') + 'item_m_' + $(oObj).attr('rel') + '_' + $(oObj).attr('href').replace('#', '');

    if (($(oObj).hasClass('moderate_link_active') && sType != 'select') || sType == 'unselect') {
        $(oObj).parent().removeClass('moderator_active');
        $(oObj).removeClass('moderate_link_active');
        $('#js_global_multi_form_ids').find('.' + sName).remove();
        deleteCookie(sCookieName);
    }
    else {
        if (!$(oObj).hasClass('moderate_link_active')) {
            $(oObj).parent().addClass('moderator_active');
            $(oObj).addClass('moderate_link_active');
            $('#js_global_multi_form_ids').append('<div class="' + sName + '"><input class="js_global_item_moderate" type="hidden" name="item_moderate[]" value="' + $(oObj).attr('href').replace('#', '') + '" /></div>');
            setCookie(sCookieName, $(oObj).attr('rel') + '_' + $(oObj).attr('href').replace('#', ''), 1);
        }
    }
    var iTotalItems = $('.moderate_link_active').length;
    $('.js_global_multi_total').html(iTotalItems);

    if (iTotalItems) {
        $('.moderation_holder').removeClass('not_active');
    }
    else {
        $('.moderation_holder').addClass('not_active');
    }

    return false;
};

/**
 * Define privacy contents.
 */
(function ($) {
    var _debug = true;

    $(document).on('click','.popup',function(){
        var caption = ($(this).data('caption')) ? $(this).data('caption') : '',
            href =  $(this).attr('href') || document.location.href;

        // fix is_ajax_popup issues.
        $(this).attr('href',  href + (/\?/.test(href)?'&is_ajax_popup=1': '?is_ajax_popup=1'));
        tb_show(caption, $(this).attr('href'), $(this));
        return false;
    }).on('click','a.thickbox',function(){
        var aExtra = $(this).html().match(/userid="([0-9a-z]?)"/i);
        var sHref = this.href;
        if (aExtra != null && isset(aExtra[1])) {
            sHref += 'userid_' + aExtra[1] + '/';
        }

        var bReturn = tb_show('', sHref, this);

        return bReturn === true;
    });

    // On click privacy items
    $(document).on('click', '[data-toggle="privacy_item"]', function () {
        var element = $(this),
            container = element.closest('.open'),
            input = container.find('input:first'),
            button = container.find('[data-toggle="dropdown"]'),
            rel = element.attr('rel');
        // processs data
        input.val(rel);

        container.find('.is_active_image').removeClass('is_active_image');
        element.addClass('is_active_image');

        var $sContent = element.html();

        if ($sContent.toLowerCase().indexOf('<span>') > -1) {
            var $aParts = explode('<span>', $sContent);
            if (!isset($aParts[1])) {
                $aParts = explode('<SPAN>', $sContent);
            }

            $sContent = $aParts[0];
        }

        button.find('span.txt-label').text($sContent);

        container.find('.fa.fa-privacy').replaceWith($('<i/>', {class: 'fa fa-privacy fa-privacy-' + rel}));
    });

    /**
     * click on like toggle
     */
    $(document).on('click', '[data-toggle="like_toggle_cmd"]', function () {
        var element = $(this),
            obj = element.data(),
            liked = !!obj.liked,
            extras = '',
            method = !liked ? 'like.add' : 'like.delete';
        if (!$('body').hasClass('_is_guest_user')) {
            if (element.parents('.comment-mini-content-commands').length) {
                var allElement = $('.comment-mini-content-commands').find('[data-toggle="like_toggle_cmd"][data-feed_id="'+obj.feed_id+'"]');

                allElement.data('liked',!liked?true:false);
                allElement.removeClass('unlike liked').addClass(liked?'unlike':'liked');
                allElement.find('span').text(!liked ? obj.label2 : obj.label1);
            }
            else {
                element.data('liked',!liked?true:false);
                element.removeClass('unlike liked').addClass(liked?'unlike':'liked');
                element.find('span').text(!liked ? obj.label2 : obj.label1);
            }
        }

        var i = element.parents('.comment_mini_content_holder:first');
        if (i.hasClass('_is_app')) {
            extras += 'custom_app_id=' + i.data('app-id') + '&';
        }

        $.ajaxCall(method, extras + 'type_id=' + obj.type_id + '&item_id=' + obj.item_id + '&parent_id=' + obj.feed_id + '&custom_inline=' + obj.is_custom + '&table_prefix=' + obj.table_prefix, 'GET');

        _debug && console.log(obj);
    });

    /**
     *
     * click to view more messages
     */
    $(document).on('click', '.mail_view_more', function () {
        var parent = $(this).parent();
        var offset = $(parent).find('.mail_thread_holder').length;
        $(this).hide();
        parent.find('#mail-pf-loading-message').show();
        $.ajaxCall('mail.viewMoreThreadMail', 'offset=' + offset + '&thread_id=' + $(this).attr('rel'), 'GET');
        return false;
    });

})(jQuery);

var cacheShadownInfo = false;
var shadow = null;
var minHeight = null;
$Core.resizeTextarea = function (oObj) {
    if (cacheShadownInfo === false) {
        var lineHeight = oObj.css('lineHeight');
        minHeight = oObj.height();
        cacheShadownInfo = true;
        shadow = $('<div></div>').css(
            {
                position: 'absolute',
                top: -10000,
                left: -10000,
                width: oObj.width(),
                fontSize: oObj.css('fontSize'),
                fontFamily: oObj.css('fontFamily'),
                lineHeight: oObj.css('lineHeight'),
                resize: 'none'
            }).appendTo(document.body);
    }

    var val = oObj.val().replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/&/g, '&amp;')
        .replace(/\n/g, '<br/>');

    shadow.html(val);
    oObj.css('height', Math.max(shadow.height(), minHeight));
};

$Core.getObjectPosition = function (sId) {
    if ($('#' + sId).length <= 0) {
        return false;
    }

    var curleft = 0;
    var curtop = 0;
    var obj = document.getElementById(sId);
    if (obj.offsetParent) {
        do {
            curleft += obj.offsetLeft;
            curtop += obj.offsetTop;
        } while (obj = obj.offsetParent);
    }

    return {left: curleft, top: curtop};
};

$Core.getFriends = function (aParams) {
    tb_show('', $.ajaxBox('friend.search', 'height=410&width=600&input=' + aParams['input'] + '&type=' + (isset(aParams['type']) ? aParams['type'] : '') + ''));
};

$Core.browseUsers = function (aParams) {
    tb_show('', $.ajaxBox('user.browse', 'height=410&width=600&input=' + aParams['input'] + ''));
};

$Core.composeMessage = function (aParams) {
    if (aParams === undefined) {
        aParams = new Array();
    }

    tb_show('', $.ajaxBox('mail.compose', 'height=300&width=500' + (!isset(aParams['user_id']) ? '' : '&id=' + aParams['user_id']) + '&no_remove_box=true'));
};

$Core.addAsFriend = function (iUserId) {
    tb_show('', $.ajaxBox('friend.request', 'width=420&user_id=' + iUserId + ''));

    return false;
};

$Core.getParams = function (sHref) {
    var aParams = new Array();
    var aUrlParts = explode('/', sHref);
    var iRequest = 0;
    for (count in aUrlParts) {
        if (empty(aUrlParts[count])) {
            continue;
        }

        aUrlParts[count] = aUrlParts[count].replace('#', '');
        if (aUrlParts[count].match(/_/i)) {
            var aUrlParams = explode('_', aUrlParts[count]);

            aParams[aUrlParams[0]] = aUrlParams[1];
        }
        else {
            iRequest++;

            aParams['req' + iRequest] = aUrlParts[count];
        }
    }

    return aParams;
};

$Core.getRequests = function (sHref, bReturnPath) {
    var sParams = '';
    var sUrlString = '';
    var sModuleName = oCore['core.section_module'];

    switch (oCore['core.url_rewrite']) {
        case '1':
            if (getParam('sHostedVersionId') == '') {
                var oReq = new RegExp("" + getParam('sJsHome') + "(.*?)$", "i");
                var aMatches = oReq.exec(sHref + (getParam('sHostedVersionId') == '' ? '' : getParam('sHostedVersionId') + '/'));
                var aParts = explode('/', aMatches[1]);

                sUrlString = '/' + aMatches[1];
            }
            else {
                var aParts = explode('/', ltrim(sHref.pathname, '/'));
                sUrlString = sHref.pathname;
            }
            break;
        case '3':
            if (oCore['profile.is_user_profile']) {
                var aProfileMatches = sHref.match(/http:\/\/(.*?)\.(.*?)/i);
                sModuleName = aProfileMatches[1];
            }

            var oReq = new RegExp("" + oParams['sJsHome'] + "(.*?)$", "i");
            var aMatches = oReq.exec(sHref);

            sUrlString = sModuleName + '/' + (aMatches != null && isset(aMatches[1]) ? aMatches[1] : '');
            break;
        default:
            var oReq = new RegExp("(.*?)=\/(.*?)$", "i");
            var aMatches = oReq.exec(sHref);
            if (aMatches !== null) {
                var aParts = explode('/', aMatches[2]);

                sUrlString = aMatches[2];
            }

            break;
    }

    if (bReturnPath === true) {
        return '/' + ltrim(sUrlString, '/');
    }

    return $Core.parseUrlString(sUrlString);
};

$Core.parseUrlString = function (sUrlString) {
    var sParams = '';
    var aUrlParts = explode('/', sUrlString);
    var iRequest = 0;
    var iLoadCount = 0;

    for (count in aUrlParts) {
        if (empty(aUrlParts[count]) || aUrlParts[count] == '#') {
            continue;
        }

        iLoadCount++;

        if (iLoadCount != 1 && aUrlParts[count].match(/_/i)) {
            var aUrlParams = explode('_', aUrlParts[count]);

            sParams += '&' + aUrlParams[0] + '=' + aUrlParams[1];
        }
        else {
            iRequest++;

            sParams += '&req' + iRequest + '=' + aUrlParts[count];
        }
    }

    return sParams;
};

$Core.reverseUrl = function (sForm, aSkip) {
    var aForms = explode('&', sForm);
    var sUrlParam = '';
    for (count in aForms) {
        var aFormParts = aForms[count].match(/(.*?)=(.*?)$/i);
        if (aFormParts !== null) {
            if (isset(aSkip)) {
                if (in_array(aFormParts[1], aSkip)) {
                    continue;
                }
            }

            sUrlParam += aFormParts[1] + '_' + encodeURIComponent(aFormParts[2]) + '/';
        }
    }

    return sUrlParam;
};

$Core.getHashParam = function (sHref) {
    var sParams = '';
    var aParams = $.getParams(sHref);

    for (var sKey in aParams) {
        sParams += '&' + sKey + '=' + aParams[sKey];
    }
    sParams = ltrim(sParams, '&');

    return sParams;
};

$Core.box = function ($sRequest, $sWidth, $sParams) {
    tb_show('', $.ajaxBox($sRequest, 'width=' + $sWidth + ($sParams ? '&' + $sParams : '')));

    return false;
};

$Core.ajax = function (sCall, $oParams) {
    var sParams = '&' + getParam('sGlobalTokenName') + '[ajax]=true&' + getParam('sGlobalTokenName') + '[call]=' + sCall;

    if (!sParams.match(/\[security_token\]/i)) {
        sParams += '&' + getParam('sGlobalTokenName') + '[security_token]=' + oCore['log.security_token'];
    }

    if (isset($oParams['params'])) {
        if (typeof($oParams['params']) == 'string') {
            sParams += $oParams['params'];
        }
        else {
            $.each($oParams['params'], function ($sKey, $sValue) {
                sParams += '&' + $sKey + '=' + encodeURIComponent($sValue) + '';
            });
        }
    }

    $.ajax(
        {
            type: (isset($oParams['type']) ? $oParams['type'] : 'GET'),
            url: getParam('sJsAjax'),
            dataType: 'html',
            data: sParams,
            success: $oParams['success']
        });
};

$Core.popup = function (sUrl, aParams) {
    oDate = new Date();
    iId = oDate.getTime();
    var sParams = '';
    var iCount = 0;
    var bCenter = false;
    for (count in aParams) {
        if (count == 'center') {
            bCenter = true;
            continue;
        }

        iCount++;
        if (iCount != 1) {
            sParams += ',';
        }

        sParams += count + '=' + aParams[count];
    }

    if (bCenter === true) {
        sParams += ',left=' + (($(window).width() - aParams['width']) / 2) + ',top=' + (($(window).height() - aParams['height']) / 2) + '';
    }

    window.open(sUrl, iId, sParams);
};

$Core.processing = function () {
    $('.ajax_processing').remove();
    $('body').prepend('<div class="ajax_processing"><i class="fa fa-spin fa-circle-o-notch"></i></div>');
};

$Core.processingEnd = function () {
    $('.ajax_processing').fadeOut();
};

$Core.closeAjaxMessage = function () {
    if ($('#table_hover_action_holder').length > 0) {
        $('#table_hover_action_holder i.fa-spin').remove();
    }
    $('#global_ajax_message').hide();
}

$Core.ajaxMessage = function () {
    if ($('#table_hover_action_holder').length > 0 && $('#table_hover_action_holder').is(':visible')) {
        $('#table_hover_action_holder i.fa-spin').remove();
        $('#table_hover_action_holder').prepend('<i class="fa fa-spin fa-circle-o-notch"></i>');
    }
    else {
        $('#global_ajax_message').html('<i class="fa fa-spin fa-circle-o-notch"></i>').show();
    }
};

/**
 * Used for the accordion effect on sections with many categories
 */
$Core.toggleCategory = function (sName, iId) {
    $('.' + sName).toggle();
    $('#show_more_' + iId).toggle();
    $('#show_less_' + iId).toggle();
};

/**
 * Refine $Core.page method then export
 */
(function ($) {
    var _xhr,
        _debug = false,
        _loading = false;

    function abortRequest() {
        try {
            if (_xhr) _xhr.abort();
        } catch (e) {
        }
        _xhr = null;
        _loading = false;
    }

    function reloadPage(url) {

        if (_loading) abortRequest();

        $(document).trigger('click.bs.dropdown.data-api');
        PF.event.trigger('on_page_change_start');

        _xhr = $.ajax({
            timeout: 30000, // max timeout when a page is requested (in milliseconds)
            url: url,
            contentType: 'application/json',
            beforeSend: function () {
                NProgress.start();
                _loading = true;
                _debug && console.log('before send');
            },
            complete: function (e) {
                if (e.responseText.substr(0, 1) != '{') {
                    eval(e.responseText);
                    return;
                }
                e = $.parseJSON(e.responseText);
                $Core.show_page(e);
            }, error: function () {
                // reload page again.
                window.location.href = window.location.href;
            }
        })
            .fail(function () {
                _debug && console.log('_xhr faile');
            })
            .always(function () {
                _loading = false;
                _debug && console.log('_xhr always');
                //Remove back on visitor page when change to other page
                $('#main .row_image:not(.no_change)').css('background-image', 'none');
                NProgress.done();
                $iReloadIteration = 0;
            })
            .done(function (e) {
                setTimeout(function () {
                    var hash = window.location.hash;
                    if (!empty(hash) && $(hash).length) {
                        $('html, body').animate({scrollTop: ($(hash).offset().top)}, 'fast');
                    }
                }, 200);
                _debug && console.log('_xhr done ', e);
            });
        _debug && console.log('done');
    }

    // export to $Core
    $Core.page = reloadPage;
})(jQuery);


if (!isset(page_editor_meta)) {
    var page_editor_meta;
}

var cacheCurrentBody = null;

$Core.refresh_counter = function (iNumberRequest, iNumberNotification, iNumberMessage) {
    if (iNumberRequest != -1 && $('#js_total_new_friend_requests').length > 0) {
        if (iNumberRequest > 0) {
            $("span#js_total_new_friend_requests").html(iNumberRequest).show();
        }
        else {
            $("span#js_total_new_friend_requests").hide();
        }
    }

    if (iNumberNotification != -1 && $('#js_total_new_notifications').length > 0) {
        if (iNumberNotification > 0) {
            $("span#js_total_new_notifications").html(iNumberNotification).show();
        }
        else {
            $("span#js_total_new_notifications").hide();
        }
    }

    if ((typeof pf_im_node_server === 'undefined' || pf_im_node_server == '') && iNumberMessage != -1 && $('#js_total_new_messages').length > 0) {
        if (iNumberMessage > 0) {
            $("span#js_total_new_messages").html(iNumberMessage).show();
        }
        else {
            $("span#js_total_new_messages").hide();
        }
    }
};

$Core.show_page = function ($aParams) {
    $Core.buildPageFromCache = false;
    if (typeof CorePageAjaxBrowsingStart == 'function') {
        CorePageAjaxBrowsingStart($aParams);
    }

    if (isset($aParams['is_sample']) && $aParams['is_sample']) {
        $bIsSample = true;
    }

    if (isset($aParams['phrases'])) {
        for (sKey in $aParams['phrases']) {
            if (!isset(oTranslations[sKey])) {
                oTranslations[sKey] = $aParams['phrases'][sKey];
            }
        }
    }

    $('.js_user_tool_tip_holder').remove();

    $('#js_user_profile_css').remove();


    if (isset($aParams['profilecss'])) {
        $('body').append($aParams['profilecss']);
    }

    if (!empty($aParams['files'])) {
        $Core.loadStaticFiles($aParams['files']);
    }

    if ($aParams['keep_body']) {
        PF.event.trigger('before_cache_current_body');

        if (cacheCurrentBody === null) {
            cacheCurrentBody = {
                contentObject: $('#content').html(),
                main: $('#main').html(),
                mainClass: $('#main').attr('class'),
                scrollTop: $(window).scrollTop(),
                id: $('body').attr('id'),
                title: document.title,
                class: $('body').attr('class'),
                url: window.location.href,
                location_6: $('.location_6').length ? $('.location_6').html() : ''
            };
        }
    }
    else {
        cacheCurrentBody = null;
    }

    var emptyLocation = [],
        main = $('#main'),
        holder = $('#content-holder');

    var blockHtml;

    for (var location in $aParams['blocks']) {
        blockHtml = $aParams['blocks'][location].join('');
        emptyLocation[location] = blockHtml.trim() == '';
        $('._block[data-location="' + location + '"]').html(blockHtml);
        // speed up html
    }

    var emptyLeft = emptyLocation[1] && emptyLocation[9];
    var emptyRight = emptyLocation[3] && emptyLocation[10];

    if (!main.hasClass('force') && !$bIsSample) {
        main[emptyLeft ? 'addClass' : 'removeClass']('empty-left');
        main[emptyRight ? 'addClass' : 'removeClass']('empty-right');
    }

    PF.event.trigger('on_page_column_init_end');

    $('#public_message').remove();
    $('._block_menu_sub').html($aParams['menuSub']);
    $('._block_top').html($aParams['search']);
    $('._block_breadcrumb').html($aParams['breadcrumb']);
    $('._block_h1').html($aParams['h1']);
    $('._block_error').html($aParams['error']);

    // controller_e
    if ($('#page_editor_popup').length) {
        page_editor_meta = $aParams['meta'];
        $('#page_editor_popup').attr('href', $aParams['controller_e']);
    }

    $('body').attr('id', 'page_' + $aParams['id']);
    $('body').attr('class', $aParams['class']);

    if (isset($aParams['customcss'])) {
        var sCustomCss = '';
        $('#js_global_custom_css').remove();
        for (sKey in $aParams['customcss']) {
            sCustomCss += $aParams['customcss'][sKey];
        }
        if (!empty(sCustomCss)) {
            // $('body').append()
        }
    }

    //rebuild main menu
    if ($aParams['selected_menu'] != null) {
        $('.site_menu a.menu_is_selected').removeClass('menu_is_selected');
        $('.site_menu li[rel="menu' + $aParams['selected_menu'] + '"] a').addClass('menu_is_selected');
    }

    var pageTitle = '';
    if ($aParams['title'] != null && typeof $aParams['title'] != 'undefined') {
        pageTitle = $aParams['title'].replace(new RegExp('&#039;', 'g'), "'");
    }
    if (self == top) {
        document.title = pageTitle;
    }
    else {
        window.parent.document.title = pageTitle;
    }

    $('._block_content').html('' + $aParams['content'] + '');

    $Core.refresh_counter($aParams['iNumberRequest'], $aParams['iNumberNotification'], $aParams['iNumberMessage']);

    $('body').css('cursor', 'auto');

    PF.event.trigger('on_page_change_end');

    // $oEventHistory[($Core.hasPushState() ? $Core.getRequests(window.location, true) : window.location.hash.replace('#!', ''))] = $aParams['content'];

    var loadInit = setInterval(function () {
        if ($Core.dynamic_js_files <= 0) {
            $Core.loadInit();
            clearInterval(loadInit);
        }
    }, 500);

    scroll(0, 0);

    $Behavior.loadTinymceEditor = function () {
    };
};

$Behavior.fixLayoutGrid = function () {
    if ($('section#site-header').length) {
        $('#content-holder').css({
            minHeight: Math.max($(window).height() - $('section#site-header').height() - $('footer').height(), $('#left').height())
        });
    }

    PF.event.trigger('on_page_column_init_end');
}

$(document).on('pageChangeStart', function () {
    // remove collapse
    $('.collapse.in').removeClass('in');
})

$Core.updatePageHistory = function () {
    var $sLocation = window.location.hash.replace('#!', '');
    if (empty($sLocation)) {
        $sLocation = '/';
    }

    $oEventHistory[$sLocation] = $('#main_content_holder').html();
};

$Behavior.janRainLoader = function () {
    $('._a_back').click(function () {
        $('.imgareaselect-outer').remove();
        $('.imgareaselect-selection').each(function () {
            $(this).parent().remove();
        });

        if (typeof(cacheCurrentBody.main) == 'string') {
            $Core.buildPageFromCache = true;
            $('#main').html(cacheCurrentBody.main);
            $('#main').attr('class',cacheCurrentBody.mainClass);
            $('body').attr('id', cacheCurrentBody.id);
            $('body').attr('class', cacheCurrentBody.class);
            // breadcrumb
            $('.location_6').html(cacheCurrentBody['location_6']);

            // reset active menu
            $('.site_menu a.menu_is_selected').removeClass('menu_is_selected');
            if (typeof $Core.lastActiveMenu !== 'undefined') {
                $('a', 'li[rel=' + $Core.lastActiveMenu + ']').addClass('menu_is_selected');
            }

            history.pushState(null, null, lastPushState);
            document.title = cacheCurrentBody.title;
            $('html, body').animate({
                scrollTop: cacheCurrentBody.scrollTop
            }, 400);
            $Core.loadInit();
        }
        $('.js_pager_view_more_link').last().removeClass('built');

        $Behavior.globalInit();
        /*if ($('.mosaicflow_load.built_flow').length > 0) {
          var firstContainer = $('.mosaicflow_load.built_flow').first();
          $('.mosaicflow_load').each(function () {
            $(this).find('article.photos_row').each(function () {
              $(this).appendTo(firstContainer);
            });
          });
          $('.mosaicflow_load').addClass('built_flow');
          $('.mosaicflow_load').mosaicflow({
            minItemWidth: $('.mosaicflow_load').data('width'),
            itemHeightCalculation: 'auto'
          });
        }*/
    });
};
var close_warning_checked = false;
var close_warning_enabled = true;
$Behavior.close_warning_event = function () {
    if (!close_warning_checked) {
        $('form .close_warning').on('change keydown', function () {
            var warningValue = $(this).val();
            if (warningValue) {
                close_warning_checked = true;
                close_warning_enabled = true;
                window.onbeforeunload = function () {
                    if (close_warning_enabled) {
                        return false;
                    }
                };
            } else {
                close_warning_enabled = false;
                close_warning_checked = false;
            }
        });
    }
    $('input[type="submit"]').on('submit click', function () {
        close_warning_enabled = false;
        close_warning_checked = false;
    });
    $('button[type="submit"]').on('submit click', function () {
        close_warning_enabled = false;
        close_warning_checked = false;
    });
    $('form').on('submit click', function () {
        close_warning_enabled = false;
        close_warning_checked = false;
    });
};
var bAjaxLinkIsClicked = false;
var bCanByPassClick = false;
var sClickProfileName = '';
var lastPushState;
$Behavior.linkClickAll = function () {
    if (!$Core.hasPushState() || $('#admincp_base').length) {
        return;
    }

    if (oParams.bOffFullAjaxMode) {
        return;
    }
    $('a, ._a').click(function (e) {
        if (close_warning_checked) {
            return;
        }
        // fix issue hold command on click to open new tab instead of ajax
        if (e.metaKey || e.ctrlKey || e.altKey)
            return;

        var $sLink = $(this).attr('href');
        if (!$sLink) {
            $sLink = $(this).data('href');
        }

        if (!$sLink) {
            return;
        }

        if ((substr($sLink, 0, 7) != 'http://' && substr($sLink, 0, 8) != 'https://')
            || substr($sLink, -1) == '#'
            || $sLink == '#'
        ) {
            return;
        }

        if ($(this).hasClass('no_ajax_link')
            || $(this).hasClass('thickbox')
            || $(this).hasClass('popup')
            || $(this).hasClass('ajax')
            || $(this).hasClass('no_ajax')
            || $(this).hasClass('inlinePopup')
            || $(this).hasClass('sJsConfirm')) {
            return;
        }

        if ($(this).hasClass('ajax-paging')) {
            var oParent = $(this).closest('.js_pager_buttons');
            var sAjaxBlock = oParent.data('block');
            var sContainer = oParent.data('content-container');

            if ($(sContainer).length > 0 && sAjaxBlock) {
                if ($(this).data('params').indexOf('type=loadmore') === -1) {
                    $(sContainer).html('<i class="ajax-paging-loading fa fa-spin fa-circle-o-notch"></i>');
                } else {
                    $(sContainer + ' .js_pager_view_more_link').remove();
                    $(sContainer).append('<i class="ajax-paging-loading fa fa-spin fa-circle-o-notch"></i>');
                }
                $.ajaxCall('core.ajaxPaging', $(this).data('params') + '&block=' + sAjaxBlock + '&container=' + sContainer, 'GET');
            }
            return false;
        }

        if ($(this).hasClass('keepPopup') && $(this).closest('.js_box').length > 0) {
            js_box_remove($(this));
            $(this).addClass('popup');
            tb_show('', $(this).attr('href'), $(this));
            return false;
        }
        var $aUrlParts = parse_url($sLink);

        if ($aUrlParts['host'] != getParam('sJsHostname') && (($aUrlParts['host'] + ':' + $aUrlParts['port']) != getParam('sJsHostname'))) {
            return;
        }

        if (!isset($aUrlParts['query'])) {
            var sTempHost = $aUrlParts['scheme'] + '://' + $aUrlParts['host'] + $aUrlParts['path'];
            $aUrlParts['query'] = sTempHost.replace(getParam('sJsHome'), '/');
        }

        if (isset($aUrlParts['query'])) {
            var aUrlParts = explode('/', $aUrlParts['query']);
            if (aUrlParts[1] == 'user' && aUrlParts[2] == 'logout') {
                return;
            }
        }

        if (bCanByPassClick === true && aUrlParts[1] != sClickProfileName) {
            bCanByPassClick = false;
            return;
        }

        if ($('#noteform').length > 0) {
            $('#noteform').hide();
        }

        if ($('#user_profile_photo').length > 0) {
            $('#user_profile_photo').imgAreaSelect({hide: true});
        }

        $('.ajax_link_reset').hide();
        $('div#core_js_messages').html('');

        bAjaxLinkIsClicked = true;

        $('body').css('cursor', 'wait');

        $(document).trigger('pageChangeStart');

        if ($('.site_menu a.menu_is_selected').length) {
          $Core.lastActiveMenu = $('.site_menu a.menu_is_selected').closest('li').attr('rel');
        } else {
          $Core.lastActiveMenu = undefined;
        }

        $('.site_menu a.menu_is_selected').removeClass('menu_is_selected');
        if ($(this).parents('.site_menu:first').length) {
            $('.site_menu a.menu_is_selected').removeClass('menu_is_selected');
            $(this).addClass('menu_is_selected');
            var rel = $(this).parent().attr('rel');
            $('.site_menu li[rel="' + rel + '"] a').addClass('menu_is_selected');
        }
        $Behavior.activeMainMenu = function () {
        };

        $('.js_user_tool_tip_holder').hide();
        $('#js_global_tooltip').hide();

        $(this).addClass('is_built');

        if (typeof BehaviorlinkClickAllAClick == 'function') {
            var bReturn = BehaviorlinkClickAllAClick($aUrlParts);
            if (bReturn == true) {
                return false;
            }
        }

        if (cacheCurrentBody === null) {
            lastPushState = window.location.href;
        }

        if (self == top) {
            history.pushState(null, null, $sLink);
        }
        else {
            window.parent.history.pushState(null, null, $sLink);
        }
        $Core.page($sLink);

        $('.js_box').each(function () {
            if ($(this).children().length > 0) {
                js_box_remove($(this).children(":first"));
            }
        });
        return false;
    });
};

$Core.loadInit = function (forceIt) {

    if ($Core.dynamic_js_files > 0 && forceIt !== true) {
        console.log('loadInit prevent', forceIt);
        setTimeout(function () {
            $Core.loadInit();
        }, 100);

        return false;
    }

    //debug('$Core.loadInit() Loaded');

    $('*:not(.star-rating, .dont-unbind)').each(function () {
        if ($(this).closest('.dont-unbind-children').length == 0) {
            $(this).unbind();
        }
    })

    $.each($Behavior, function () {
        this(this);
    });

    PF.event.trigger('on_page_load_init_end');
};

$(window).load(function () {
    if ($('.nano').length) {
        $('.nano, .nano-content').addClass('dont-unbind');
        $('.nano').css('visibility', 'visible').nanoScroller();
        $('.nano, .nano-content, .nano-pane').addClass('dont-unbind');
    }
});

var popped = ('state' in window.history), initialURL = location.href;

$Core.init = function () {
    if ($Core.hasPushState()) {
        window.addEventListener("popstate", function (e) {
            var h = document.location.href;
            if ((h.substr(h.length - 1)) == '#') {
                return;
            }
            var initialPop = !popped && location.href == initialURL;
            popped = true;
            if (initialPop) return;
            // what should be control
            console.log('pop from state', document.location.href, e);
            $Core.page(document.location.href);

        });
    }

    $bDocumentIsLoaded = true;
    $(document).ready(function () {
        if ($('.nano').length) {
            $('.nano').css('visibility', 'visible').nanoScroller();
            $('.nano, .nano-content, .nano-pane').addClass('dont-unbind');
        }

        $('*:not(.star-rating, .dont-unbind)').each(function () {
            if ($(this).closest('.dont-unbind-children').length == 0) {
                $(this).unbind();
            }
        })

        $.each($Behavior, function () {
            this(this);
        });

        $.each($Events, function () {
            this(this);
        });
    });

    $('script,link').each(function () {
        if (!empty(this.src)) {
            var $sVar = this.src;

            if (this.src.indexOf('f=') !== -1) {
                var $aFiles = explode('f=', $sVar);
                var $aParts = explode('&v=', $aFiles[1]);
                var $aGetFiles = explode(',', $aParts[0]);
                $($aGetFiles).each(function ($sKey, $sFile) {
                    if (substr($sFile, 0, 7) == 'module/') {
                        $oStaticHistory[getParam('sJsHome') + $sFile] = true;
                    }
                    else {
                        $oStaticHistory[getParam('sJsStatic') + 'jscript/' + $sFile] = true;
                    }
                });
                return;
            }
        }
        else if (!empty(this.href)) {
            var $sVar = this.href;

            if (this.href.indexOf('f=') !== -1) {
                var $aFiles = explode('f=', $sVar);
                var $aParts = explode('&v=', $aFiles[1]);
                var $aGetFiles = explode(',', $aParts[0]);
                $($aGetFiles).each(function ($sKey, $sFile) {
                    $oStaticHistory[getParam('sJsHome') + $sFile] = true;
                });
                return;
            }
        }

        if (!empty($sVar)) {
            var $aParts = explode('?', $sVar);
            $oStaticHistory[$aParts[0]] = true;
        }
    });

    if (isset($Cache['post_static_files'])) {
        $($Cache['post_static_files']).each(function ($sKey, $mValue) {
            $Core.loadStaticFiles($mValue);
        });
    }
};

$Core.hasPushState = function () {
    return (typeof(window.history.pushState) == 'function' ? true : false);
};

/**
 * Adds a hash to the URL string, which is used to emulate a AJAX page
 *
 * @param object oObject Is the anchor object (this)
 */
$Core.addUrlPager = function (oObject, bShort) {
    if ($Core.hasPushState()) {
        window.history.pushState('', '', oObject.href);
    }
    else {
        window.location = '#!' + (bShort ? oObject.href : $Core.getRequests(oObject.href, true));
    }
};

$Core.reloadPage = function () {
    /* which is why we have these fallbacks*/
    if (typeof window.location.reload == 'function') window.location.reload();
    else if (typeof history != 'undefined' && history.go == 'function') history.go(0);
};

$Behavior.addExpanderListener = function () {
    $('[data-expand="expander"]').click(function () {
        var $this = $(this),
            target = $($this.data('target'));
        if (target.length) {
            target.toggleClass('close');
        }
    });

    var ft = $('#section-footer'), st = $('#content-stage');

    if (ft.length && st.length && ((ft.offset().top + ft.height()) < $(window).height())) {
        st.css({
            minHeight: $(window).height() - st.offset().top - ft.height()
        });
    }
};

$Core.publicMessageSlideDown = function() {
  $('#public_message').animate({'margin-bottom': '-50px'}, 'fast', function() {
    $('#public_message').html('').hide();
  });
  $('.has-public-message').removeClass('has-public-message');
};

$Behavior.addModerationListener = function() {
  var m = $('#public_message');
  if (m.length && m.html().length && (typeof $Core.buildPageFromCache !== 'undefined' || !$Core.buildPageFromCache)) {
    m.show();
    m.animate({
      'margin-bottom': '0px',
    }, 'fast', function() {
      if (m.data('auto-close') != false) {
        setTimeout(function() {
          $Core.publicMessageSlideDown();
        }, 3000);
      }
    });
  }

  $(window).on('moderation_ended', function() {
    /* Search for moderation rows */
    if ($('.moderation_row:visible').length < 1) {
      if ($('a.pager_previous_link').length > 0 &&
        $('a.pager_previous_link:first').attr('href') != '#') {
        window.location.href = $('a.pager_previous_link:first').attr('href');
        return;
      }

      if (window.location.href.indexOf('page_1') > (-1)) {
        window.location.href = window.location.href.replace('/page_1', '');
        return;
      }

      return $Core.reloadPage();

      /* Check if we have a pager */

      if ($('a.pager_next_link').length > 0) {
        if (isset($Core.Pager) && isset($Core.Pager.count) &&
          ($Core.Pager.count - $Core.Pager.size) > 20) {
          window.location.href = $('a.pager_next_link:first').attr('href');
          return;
        }
        window.location.href = $('a.pager_next_link:first').attr('href');
      }
      else {
        window.location.href = window.location.href;
      }
    }
    else if ($('.moderation_row:first').is(':animated')) {
      setTimeout('$(window).trigger("moderation_ended");', 1000);
    }
    else {
      /* console.log('Moderation_rows still exist and are not being animated');*/
    }
  });
};

/* We use the block core.delayed-block as placeholder */
$Behavior.loadDelayedBlocks = function () {
    if (isset($Core.delayedBlocks) && Object.prototype.toString.call($Core.delayedBlocks).indexOf('Array') > (-1)) {
        // we could issue several ajax calls (one per location)
        $.ajaxCall('core.loadDelayedBlocks', 'locations=' + $Core.delayedBlocks.join(','));
    }

    /* We load the main content (the middle column) here */
    if ($('#delayed_block').length > 0) {
        if ( //oCore['profile.is_user_profile'] == true ||
        (oParams['sController'] == 'core.index-member') ||
        (oCore['sController'] == 'pages.view')) {
            console.log('Behavior.loadDelayedBlock, Dont load the content');
        }
        else {
            var sContent = $('#delayed_block').html();
            // $('#delayed_block').show();
            // Get the params from the url
            // console.log('Behavior.loadDelayedBlock, load ' + sContent);
            var sUrl = $Core.getRequests(window.location.href, true);
            var aUrl = sUrl.split('/');
            var oUrlParams = {};
            var aTemp = [];

            for (var count in aUrl) {
                if (aUrl[count].indexOf('_') > (-1)) {
                    aTemp = aUrl[count].split('_');
                    oUrlParams[aTemp[0]] = aTemp[1];
                }
                oUrlParams['req' + j] = aUrl[count];
            }
            var sParams = $.param({params: oUrlParams});

            //setTimeout(function(){	/* Uncomment to test */
            $.ajaxCall('core.loadDelayedBlocks', 'loadContent=' + sContent + '&' + sParams, 'GET');
            // }, 2000);
        }
    }
    /* Any extra delayed loading is done here, for example with the comments */
    if ($('.load_delayed').length > 0) {
        var oGet = {};
        $('.load_delayed').each(function () {
            if ($(this).attr('id') == undefined || $(this).attr('id').length < 1) {
                $(this).attr('id', 'load_delayed_' + Math.floor(Math.random() * 999));
            }
            oGet[$(this).find('.block_id').html()] = {
                block_id: $(this).find('.block_id').html(),
                block_name: $(this).find('.block_name').html(),
                block_param: $(this).find('.block_param').html()
            };
        });
        var sParams = encodeURIComponent(JSON.stringify(oGet));
        //console.log(sParams);
        //setTimeout(function(){	/* Uncomment to test */
        $.ajaxCall('core.loadDelayedBlocks', 'delayedTemplates=' + sParams, 'GET');
        // }, 2000);
    }
};


/************************ Compatibility Features (Mostly due to IE8) *******************************/
/* Production steps of ECMA-262, Edition 5, 15.4.4.19
   Reference: http://es5.github.com/#x15.4.4.19
   Taken from https://developer.mozilla.org/en-US/docs/JavaScript/Reference/Global_Objects/Array/map
*/
if (!Array.prototype.map) {
    Array.prototype.map = function (callback, thisArg) {

        var T, A, k;

        if (this == null) {
            throw new TypeError(" this is null or not defined");
        }
        var O = Object(this);
        var len = O.length >>> 0;
        if (typeof callback !== "function") {
            throw new TypeError(callback + " is not a function");
        }
        if (thisArg) {
            T = thisArg;
        }
        A = new Array(len);
        k = 0;
        while (k < len) {

            var kValue, mappedValue;
            if (k in O) {

                kValue = O[k];
                mappedValue = callback.call(T, kValue, k, O);
                A[k] = mappedValue;
            }
            k++;
        }
        return A;
    };
}
/* Taken from https://developer.mozilla.org/en-US/docs/JavaScript/Reference/Global_Objects/Array/filter */
if (!Array.prototype.filter) {
    Array.prototype.filter = function (fun /*, thisp */) {
        "use strict";

        if (this == null)
            throw new TypeError();

        var t = Object(this);
        var len = t.length >>> 0;
        if (typeof fun != "function")
            throw new TypeError();

        var res = [];
        var thisp = arguments[1];
        for (var count = 0; count < len; count++) {
            if (count in t) {
                var val = t[count];
                if (fun.call(thisp, val, j, t))
                    res.push(val);
            }
        }

        return res;
    };
}

$Behavior.setIndexJsBox = function () {
    $('.js_box').unbind('click');
    $('.js_box').click(function () {
        var $aAllBoxIndexInner = new Array();
        $('.js_box').each(function () {
            $aAllBoxIndexInner.push(parseInt($(this).css('z-index')));
        });

        $(this).css(
            {
                'z-index': (parseInt(Math.max.apply(Math, $aAllBoxIndexInner)) + 1)
            });
    });
}

$Core.jsConfirm = function (params, doYes, doNo) {
    var title = (params.hasOwnProperty('title') && params.title) ? params.title : oTranslations['confirm'];
    var message = (params.hasOwnProperty('message') && params.message) ? params.message : oTranslations['are_you_sure'];
    var yesBtn = (params.hasOwnProperty('btn_yes') && params.btn_yes) ? params.btn_yes : oTranslations['yes'];
    var noBtn = (params.hasOwnProperty('btn_no') && params.btn_no) ? params.btn_no : oTranslations['no'];
    var buttons = {};
    if (!(params.hasOwnProperty('no_yes') && params.no_yes)) {
        buttons[yesBtn] = {
            'class': 'btn btn-success dont-unbind',
            text: yesBtn,
            click: function () {
                $(this).dialog("close");
                if (doYes && (typeof doYes === "function")) {
                    doYes();
                    return true;
                }
            }
        };
    }
    buttons[noBtn] = {
        'class': 'btn btn-default dont-unbind',
        text: noBtn,
        click: function () {
            $(this).dialog("close");
            if (doNo && (typeof doNo === "function")) {
                doNo();
                return false;
            }
        }
    };
    var $aAllBoxIndex = new Array();
    $('.js_box_holder').each(function() {
        $aAllBoxIndex.push(parseInt($(this).css('z-index')));
    });

    var wrapper = $('<div />', {
        'class': 'js_box_holder',
        id: 'js-confirm-popup-wrapper',
        'css': {
            'z-index': (parseInt(Math.max.apply(Math, $aAllBoxIndex)) + 1),
        },
    });

    if ($('.js_box_holder').length > 0) {
      wrapper.insertAfter($('.js_box_holder').last());
    }
    else {
      wrapper.prependTo('body');
    }
    $('body').css('overflow', 'hidden');

    $(document.createElement('div'))
    .attr({title: title, class: 'confirm'})
    .html(message)
    .dialog({
      classes: {
        "ui-dialog": "js_box",
        "ui-dialog-titlebar": "js_box_title",
        "ui-dialog-content": "js_box_content",
        "ui-dialog-titlebar-close": "js_box_close"
      }
      ,
      buttons: buttons
      ,
      close: function() {
        $(this).closest('.js_box_holder').remove();
        $('body').css('overflow', 'auto');
      },
      draggable: true,
      modal: true,
      resizable: false,
      minWidth: 300,
      appendTo: "#js-confirm-popup-wrapper"
    });
};

$Core.music_playSong = function (obj) {
    var parent = $(obj).closest('.music_row');
    var audio = parent.find('.music_player .audio_player audio');
    if (audio.length > 0) {
        audio = audio[0];
    }
    else {
        return false;
    }

    if (parent.hasClass('active')) {
        audio.pause();
        audio.currentTime = 0;
        parent.removeClass('active');
        parent.find('.music_player').slideToggle();
        return true;
    }

    var block_content = $(obj).closest('._block_content');
    block_content.find('.music_row.active').each(function () {
        $(this).find('.music_player').slideToggle();
        var temp_audio = $(this).find('.music_player .audio_player audio');
        if (temp_audio.length > 0) {
            (temp_audio[0]).pause();
            (temp_audio[0]).currentTime = 0;
        }
        $(this).removeClass('active');
    });
    parent.find('.music_player').slideToggle();
    audio.play();
    parent.addClass('active');

};

$Core.b64DecodeUnicode = function (str) {
    return decodeURIComponent(Array.prototype.map.call(atob(str), function (c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
    }).join(''));
};

$Core.loadRecaptchaApi = function () {
    var ele = $('.g-recaptcha').not('.build');
    if (!ele.length) {
        return;
    }

    if (typeof grecaptcha == 'undefined') {
        $("<script/>", {src: "//www.google.com/recaptcha/api.js"}).appendTo("body");
    } else {
        grecaptcha.render(ele.get(0), {
            'sitekey': ele.data('sitekey')
        });
    }

  $('.g-recaptcha').addClass('build');
};

$Core.searchTable = function (obj, table_id, column_index) {
    var filter, table, tr, td, i,
        index = $('#' + column_index).index();
    filter = obj.value.toUpperCase();
    table = document.getElementById(table_id);
    tr = table.getElementsByTagName("tr");
    // Loop through all table rows, and hide those who don't match the search query
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[index];
        if (td) {
            if ($(td).text().toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
};

$Core.sortTable = function(obj, table_id) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById(table_id);
    var n = $(obj).index();
    switching = true;
    //Set the sorting direction to ascending:
    dir = "asc";
    /*Make a loop that will continue until
    no switching has been done:*/
    while (switching) {
        //start by saying: no switching is done:
        switching = false;
        rows = table.getElementsByTagName("tr");
        /*Loop through all table rows (except the
        first, which contains table headers):*/
        for (i = 1; i < (rows.length - 1); i++) {
            //start by saying there should be no switching:
            shouldSwitch = false;
            /*Get the two elements you want to compare,
            one from current row and one from the next:*/
            x = rows[i].getElementsByTagName("td")[n];
            y = rows[i + 1].getElementsByTagName("td")[n];
            /*check if the two rows should switch place,
            based on the direction, asc or desc:*/
            var xhtml = $(x).text().toLowerCase().trim(),
                yhtml = $(y).text().toLowerCase().trim();
            if (dir == "asc") {
                if (xhtml > yhtml) {
                    //if so, mark as a switch and break the loop:
                    shouldSwitch= true;
                    break;
                }
            } else if (dir == "desc") {
                if (xhtml < yhtml) {
                    //if so, mark as a switch and break the loop:
                    shouldSwitch= true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            /*If a switch has been marked, make the switch
            and mark that a switch has been done:*/
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            //Each time a switch is done, increase this count by 1:
            switchcount ++;
        } else {
            /*If no switching has been done AND the direction is "asc",
            set the direction to "desc" and run the while loop again.*/
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
};

$Core.uploadForm = {
  toggleForm: function(obj) {
    $(obj).closest('.js_upload_form_wrapper').toggleClass('show-current');
  },

  deleteImage: function(obj, type, field) {
    $Core.uploadForm.toggleForm(obj);
    var wrapper = $(obj).closest('.js_upload_form_wrapper');
    if (wrapper.length > 0) {
      wrapper.find('.js_upload_form_current').remove();
      wrapper.find('.js_upload_form .js_hide_upload_form').remove();
      wrapper.append($('<input />', {
        type: "hidden",
        name: "val[" + field + "]",
        value: 1,
        id: "js_upload_remove_file_" + type
      }));
    }
  },

  dismissUpload: function(obj, type) {
    $Core.uploadForm.toggleForm(obj);
    $Core.dropzone.instance[type].removeAllFiles();
  },

  onSuccessUpload: function(ele, file, response) {
    response = JSON.parse(response);


    // process error
    if (typeof response.error !== 'undefined' && typeof response.type !== 'undefined') {
      return $Core.dropzone.setFileError(response.type, file, response.error);
    }

    // upload successfully
    if (typeof response.file !== 'undefined' && typeof response.type !== 'undefined' && typeof response.field_name !== 'undefined') {
        var input = ele.find('.dz-preview #js_upload_form_file_' + response.type);
        file.item_id = response.file;
        if (input.length > 0) {
          input.val(response.file);
          input.attr('name', 'val['+ response.field_name +']');
        }
    }
  }
};

$Core.slideAlert = function(sParent, sMessage, sType, sExtraClass) {
  var sId = 'pf_alert_' + sType + '_' + Math.floor((Math.random() * 1000) + 1);
  var ele = $('<div />', {
    'class': 'alert alert-' + sType + ' slide-down-alert alert-dismissable fade in ' + sExtraClass,
    id: sId,
    text: sMessage
  }).css('display', 'none').append($('<a href="javascript:void(0);" class="close" onclick="$(this).parent().slideUp();event.stopPropagation();" aria-label="' + oTranslations['close'] + '">&times;</a>'));
  var parent = $(sParent);
  if (parent.length > 0) {
    if (parent.find('.pf-alert-wrapper').length == 0) {
      var wrapper = $('<div />', {'class':'pf-alert-wrapper ' + sExtraClass}).appendTo(sParent);
    }
    else {
      var wrapper = parent.find('.pf-alert-wrapper');
    }
    ele.prependTo(wrapper).slideDown();
    setTimeout(function(){ $('#' + sId).slideUp() }, 1500);
  }
};

$Core.submitFriendRequest = function() {
  $('#container_submit_friend_request').fadeOut('fast', function() {
    $('#friend_request_alert').fadeIn();
    setTimeout(function() {
      tb_remove();
    }, 3000)
  });
};

$Core.onSubmitForm = function(form, isAdmin) {
  var ele = $(form).find('input[type=submit], button[type=submit]');
  ele.addClass('disabled');
  if (typeof isAdmin != 'undefined' && isAdmin) {
      ele.prop('disabled', true);
  }
};

/*
 *  Search item button
 */
(function ($) {
    PF.cmd('core.search_items', function (btn, evt) {
        btn.closest('form').submit();
    });
})(jQuery);

Core_drag =
{
    init: function(aParams)
    {
        $(document).ready(function()
        {
            $(aParams['table']).tableDnD(
                {
                    dragHandle: 'drag_handle',
                    onDragClass: 'drag_my_class',
                    onDrop: function (oTable, oRow)
                    {
                        iCnt = 0;
                        sParams = '';
                        $('.drag_handle input').each(function()
                        {
                            iCnt++;

                            sParams += '&' + $(this).attr('name') + '=' + iCnt;
                        });

                        $('.drag_handle_input').each(function()
                        {
                            sParams += '&' + $(this).attr('name') + '=' + $(this).attr('value');
                        });

                        if (aParams['ajax'].substr(0, 7) == 'http://' || aParams['ajax'].substr(0, 8) == 'https://') {
                            $Core.processing();
                            $.ajax({
                                url: aParams['ajax'],
                                type: 'POST',
                                data: sParams,
                                success: function(e) {
                                    $('.ajax_processing').remove();
                                    if (typeof e == 'object' && typeof e.run == 'string') {
                                        eval(e.run);
                                    }
                                }
                            });
                        }
                        else {
                            $Core.ajaxMessage();
                            $.ajaxCall(aParams['ajax'], sParams + '&global_ajax_message=true');
                        }
                    }
                });

            $(aParams['table'] + " tr.checkRow").hover(
                function()
                {
                    $(this.cells[0]).addClass('drag_show_handle');
                },
                function()
                {
                    $(this.cells[0]).removeClass('drag_show_handle');
                }
            );
        });
    }
};

$(document).on('focus', '.activity-feed-status-form', function() {
  $(this).addClass('activity-feed-status-form-active');
});

$(document).on('blur', '.activity-feed-status-form', function() {
  $(this).removeClass('activity-feed-status-form-active');
});

$Core.searchFriend = {
  sPrivacyInputName: null,
  sSearchByValue: null,
  sFriendModuleId: null,
  sFriendItemId: null,
  inputSelector: null,
  initialized: false,
  searching: false,
  searchAction: null,

  init: function (inputSelector, params) {
    this.sPrivacyInputName = params.sPrivacyInputName;
    this.sSearchByValue = params.sSearchByValue;
    this.sFriendModuleId = params.sFriendModuleId;
    this.sFriendItemId = params.sFriendItemId;
    this.inputSelector = inputSelector;
    this.itemWidth = 0;
    this.maxItemsInARow = 0;
    this.itemCount = 0;
    this.showOverflow = false;

    if (!this.initialized) {
      // init input
      $(document).on('focus', inputSelector, function() {
        if (this.value == $Core.searchFriend.sSearchByValue) {
          this.value = '';
          $(this).removeClass('default_value');
        }
      }).on('blur', inputSelector, function() {
        if (this.value == '') {
          this.value = $Core.searchFriend.sSearchByValue;
          $(this).addClass('default_value');
        }
      });
      // selected list
      $(document).on('click', '#selected_friends_list li', function() {
        if (typeof $(this).data('id') === 'undefined') {
          return;
        }
        $('#search-friend-' + $(this).data('id')).trigger('click');

        // pop 1 member in overflow list
        var overflow = $('li[data-overflow="true"]');
        if (overflow.length) {
          $(overflow[0]).removeClass('hide').attr('data-overflow', false);
        } else {
          $('#selected_friend_view_more').addClass('hide');
        }
      });
      $(document).on('click', '#deselect_all_friends', function() {
        $('#selected_friends_list li').trigger('click');
        $(this).addClass('hide');
        $Core.searchFriend.showOverflow = false;
      });
      // view more
      $(document).on('click', '#selected_friend_view_more', function() {
        $Core.searchFriend.showOverflow = true;
        $(this).addClass('hide');
        $('li[data-overflow="true"]', $('#selected_friends_list')).toggleClass('hide');
      });
      this.initialized = true;
    }
  },

  search: function() {
    if ($Core.searchFriend.searching) {
      return;
    }

    clearTimeout($Core.searchFriend.searchAction);
    $Core.searchFriend.searchAction = setTimeout(function() {
      var paramsArray = [
        'friend_module_id=' + $Core.searchFriend.sFriendModuleId,
        'friend_item_id=' + $Core.searchFriend.sFriendItemId,
        'find=' + $($Core.searchFriend.inputSelector).val(),
        'input=' + $Core.searchFriend.sPrivacyInputName,
      ];
      $Core.searchFriend.searching = true;
      $Core.searchFriend.showLoader();
      $.ajaxCall('friend.searchAjax', paramsArray.join('&'));
    }, 500);
  },

  selectFriend: function(ele) {
    var th = $(ele),
        friendList = $('#selected_friends_list');

    if ($('#js_friends_checkbox_' + th.data('id')).prop('checked')) {
      $('#js_friends_checkbox_' + th.data('id')).prop('checked', false);
      th.removeClass('active');
      $('.item-outer', th).removeClass('active');
    }
    else {
      $('#js_friends_checkbox_' + th.data('id')).prop('checked', true);
      th.addClass('active');
      $('.item-outer', th).addClass('active');
    }

    if (th.data('can-message')) {
      this.addFriendToSelectList($('#js_friends_checkbox_' + th.data('id')),
          th.data('id'), $('#js_friends_checkbox_' + th.data('id')).prop('checked'));
    }

    if (this.itemWidth == 0 && $('li', friendList).length > 2) {
      var lastLi = $('li', friendList).last();
      this.itemWidth = lastLi.width() + parseInt(lastLi.css('margin-right'));
      this.maxItemsInARow = parseInt(friendList.width() / this.itemWidth) - 1;
    }
  },

  addFriendToSelectList: function(oObject, sId, bForce) {
    if (oObject.checked || bForce) {
      iCnt = 0;
      $('.js_cached_friend_name').each(function() {
        iCnt++;
      });

      if (function_exists('plugin_addFriendToSelectList')) {
        plugin_addFriendToSelectList(sId);
      }
      $('#js_selected_friends').
          append('<div class="js_cached_friend_name row1 js_cached_friend_id_' +
              sId + '' + (iCnt ? '' : ' row_first') +
              '"><span style="display:none;">' + sId +
              '</span><input type="hidden" name="val[' + this.sPrivacyInputName +
              '][]" value="' + sId +
              '" /><a href="#" onclick="return $Core.searchFriend.removeFromSelectList(sId);"></a> ' +
              $('#js_friend_' + sId + '').html() + '</div>');

      // add to selected list
      var friendItem = oObject.closest('article'),
          selectedFriend = $('#selected_friend_template').clone(),
          selectedFriendList = $('#selected_friends_list'),
          notOverflow = this.showOverflow || this.maxItemsInARow == 0 || this.itemCount < this.maxItemsInARow,
          viewMore = $('#selected_friend_view_more');
      // clone image
      $('.img-wrapper', selectedFriend).prepend($('.user_rows_image', friendItem).html());
      // remove attribute id
      selectedFriend.removeAttr('id');
      selectedFriend.attr('data-id', sId);
      if (notOverflow) {
        selectedFriend.removeClass('hide');
      } else {
        selectedFriend.attr('data-overflow', true);
      }

      $('#selected_friends_list').append(selectedFriend);
      if (!notOverflow) {
        $('li', selectedFriendList).last().after(viewMore);
        viewMore.removeClass('hide');
      }

      $('#deselect_all_friends').removeClass('hide');
      $('#deselect_all_friends span').html($('#selected_friends_list li').length - 2);
      this.itemCount++;
    }
    else {
      if (function_exists('plugin_removeFriendToSelectList')) {
        plugin_removeFriendToSelectList(sId);
      }

      $('.js_cached_friend_id_' + sId).remove();
      $('#js_friend_input_' + sId).remove();
      $('[data-id=' + sId + ']', $('#selected_friends_list')).remove();
      var selectedCount = $('#selected_friends_list li').length - 2;
      $('#deselect_all_friends span').html(selectedCount);
      selectedCount === 0 && $('#deselect_all_friends').addClass('hide');
      $('#selected_friends_list li').length === 1 && $('#deselect_all_friends').addClass('hide');
      this.itemCount--;
    }
  },

  updateFriendsList: function() {
    $Core.searchFriend.searching = false;
    this.updateCheckBoxes();
    if (typeof $.mCustomScrollbar === 'function') {
      $(".friend-search-invite-container", '#js_friend_search_content').mCustomScrollbar({
        theme: "minimal-dark",
      }).addClass('dont-unbind-children');
    }
  },

  removeFromSelectList: function(sId) {
    $('.js_cached_friend_id_' + sId + '').remove();
    $('#js_friends_checkbox_' + sId).attr('checked', false);
    $('#js_friend_input_' + sId).remove();
    $('.js_cached_friend_id_' + sId).remove();
    this.itemCount--;

    return false;
  },

  cancelFriendSelection: function() {
    if (function_exists('plugin_cancelFriendSelection')) {
      plugin_cancelFriendSelection();
    }

    $('#js_selected_friends').html('');
    $Core.loadInit();
    tb_remove();
  },

  updateCheckBoxes: function() {
    $('.js_cached_friend_name').each(function() {
      $('#js_friends_checkbox_' + $(this).find('span').html()).prop('checked', true).closest('article').addClass('active');
    });
  },

  showLoader: function() {
    $('#js_friend_search_content').
        html($.ajaxProcess(oTranslations['loading'], 'large'));
  },

  checkForEnter: function(event) {
    if ($Core.searchFriend.searching) {
      return;
    }

    clearTimeout($Core.searchFriend.searchAction);
    $Core.searchFriend.searchAction = setTimeout(function() {
      var paramsArray = [
        'friend_module_id=' + $Core.searchFriend.sFriendModuleId,
        'friend_item_id=' + $Core.searchFriend.sFriendItemId,
        'find=' + $($Core.searchFriend.inputSelector).val(),
        'input=' + $Core.searchFriend.sPrivacyInputName,
      ];
      $Core.searchFriend.searching = true;
      $Core.searchFriend.showLoader();
      $.ajaxCall('friend.searchAjax', paramsArray.join('&'));
    }, 500);
  },

  selectSearchFriends: function() {
    if (function_exists('plugin_selectSearchFriends')) {
      plugin_selectSearchFriends();
    }
    else {
      $Core.loadInit();
      tb_remove();
    }
  },

  cancelSearchFriends: function() {
    if (function_exists('plugin_cancelSearchFriends')) {
      plugin_cancelSearchFriends();
    }
    else {
      this.cancelFriendSelection();
    }
  },
};

$Behavior.searchFriendBlock = function() {
  $Core.searchFriend.sSearchByValue = $('.js_is_enter').val();

  if ($.browser.mozilla) {
    $(document).on('keypress', '.js_is_enter', $Core.searchFriend.checkForEnter);
  }
  else {
    $(document).on('keyup', '.js_is_enter', $Core.searchFriend.checkForEnter);
  }
};

$Behavior.friend_block_search_init = function() {
  $Core.searchFriend.updateCheckBoxes();
};

$Core.tokenfield = {
  isAjax: false,

  init: function(ele) {
    var params = {
      delimiter: [','],
    };

    // search friends
    if (ele.data('search') === 'friends') {
      params.autocomplete = {
        source: $Cache.friends.map(function(a) {
          return {
            id: a.user_id,
            value: a.user_id,
            label: a.full_name,
            user_image: a.user_image,
          };
        }),
        delay: 100,
      };
      params.showAutocompleteOnFocus = true;
      params.allowEditing = false;
    }

    if (ele.data('allow-editing') == false) {
      params.allowEditing = false;
    }

    if (typeof ele.data('type') !== 'undefined') {
      params.inputType = ele.data('type');
    }

    ele.addClass('built')
    .on('tokenfield:removetoken', function(event) {
      if (ele.data('multiple-input') == true) {
        $('#tokenfield-' + ele.data('input-name') + '-' + event.attrs.value).remove();
      }
    }).on('tokenfield:createtoken', function(event) {
      var duplicated = false;
      if (ele.data('check-duplicate') == true) {
        var existingTokens = ele.tokenfield('getTokens');
        $.each(existingTokens, function(index, token) {
          if (typeof token.value === 'undefined' ||
              typeof event.attrs.value === 'undefined' ||
              token.value === event.attrs.value) {
            event.preventDefault();
            duplicated = true;
          }
        });
      }

      if (ele.data('multiple-input') == true && !duplicated) {
        var inputId = 'tokenfield-' + ele.data('input-name') + '-' + event.attrs.value;
        if ($('#' + inputId).length) {
          return;
        }

        $('<input type="hidden" name="' + ele.data('input-name') +
            '[]" value="' + event.attrs.value + '" id="' + inputId + '">').
            insertBefore(ele);
      }
    }).on('tokenfield:initialize', function() {
      // don't unbind all children
      ele.closest('.tokenfield').addClass('dont-unbind-children');
      if (typeof ele.data('bs.tokenfield').$input.autocomplete('instance') ===
          'undefined') {
        return;
      }

      var input = ele.data('bs.tokenfield').$input;
      // custom render autocomplete result list
      input.autocomplete('instance')._renderItem = function(ul, item) {
        var userImage = item.user_image;
        if (item.user_image.indexOf('no_image_user') === -1) {
          userImage = '<img src=' + item.user_image + '>';
        }

        return $('<li class="search-friend-item"></li>').
            data('item.autocomplete', item).
            append('<div class="friend-image">' + userImage + '</div>').
            append('<span class="friend-name">' + item.label + '</span>').
            appendTo(ul);
      };
      // add class to ul
      input.autocomplete('widget').addClass('search-friend-list');
    }).tokenfield(params);

    if (ele.data('type') === 'email') {
      ele.on('tokenfield:createdtoken', function(e) {
        // Über-simplistic e-mail validation
        var re = /\S+@\S+\.\S+/;
        var valid = re.test(e.attrs.value);
        if (!valid) {
          $(e.relatedTarget).addClass('invalid');
        }
      });
    }
    // init values
    if (typeof $Core.tokenfieldValues !== 'undefined' && typeof $Core.tokenfieldValues[ele.data('input-name')] !== 'undefined') {
      ele.tokenfield('setTokens', $Core.tokenfieldValues[ele.data('input-name')]);
    }

    ele.tokenfield('update');
  },

  globalInit: function() {
    $('[data-component="tokenfield"]').not('.built').each(function() {
      var th = $(this);

      if (th.data('search') === 'friends' && typeof $Cache.friends === 'undefined') {
        $Core.searchFriendsInput.buildFriends();
        PF.waitUntil(function() {
          return (typeof $Cache.friends !== 'undefined');
        }, function() {
          $Core.tokenfield.init(th);
        });

        return;
      }

      $Core.tokenfield.init(th);
    });
  }
};

$Behavior.generateTokenfield = function() {
  if ($Core.tokenfield.isAjax == false) {
    return;
  }
  $Core.tokenfield.globalInit();
};

PF.event.on('on_page_column_init_end', function() {
  $Core.tokenfield.isAjax = true;
  $Core.tokenfield.globalInit();
});

$Behavior.buildBLockToggle = function() {
  $('.layout-left .block:not(.built-toggle):not(.no-toggle), .layout-right .block:not(.built-toggle):not(.no-toggle)').each(function() {
    var toggle = $(this).data('toggle'),
      bodyWidth = $('body').width();
    if (toggle == false || bodyWidth >= 992) {
      return;
    } else if (typeof toggle == 'number' && toggle) {
      if ((bodyWidth < 992 && bodyWidth >= 767 && toggle != 992) ||
        (bodyWidth < 767 && bodyWidth >= 480 && toggle != 767) ||
        (bodyWidth < 480 && toggle != 480)
      ) {
        return;
      }
    } else if (typeof toggle == 'string' && toggle) {
      var toggleWidths = toggle.split(',');
      if (toggleWidths.length) {
        if ((bodyWidth < 992 && bodyWidth >= 767 && toggleWidths.indexOf('992') === -1) ||
          (bodyWidth < 767 && bodyWidth >= 480 && toggleWidths.indexOf('767') === -1) ||
          (bodyWidth < 480 && toggleWidths.indexOf('480') === -1)
        ) {
          return;
        }
      }
    }

    $(this).addClass('built-toggle');
    if ($(this).height() >= 120) {
        $('<div class="toggle-button shown dont-unbind"><span class="ico ico-angle-down"></span></div>').on('click', function() {
            $(this).parent().toggleClass('full');
        }).appendTo($(this));
        $(this).addClass('has-toggle');
    }
  });
}

$Behavior.searchFriendsComponent = function() {
  if ($Core.searchFriendsParams) {
    $Core.searchFriends($Core.searchFriendsParams);
  }
};

$Core.ProfilePhoto = {
  hadPhoto: false,
  firstZoom: true,
  firstOffset: true,
  modified: false,

  cropProfilePhoto: function(profileImage) {
    $Core.customizeImageContainer = function(bgContainer) {
      bgContainer.append('<img class="cropit-preview-image-bg" src="' + getParam('sJsHome') + 'module/user/static/image/crop-area.png' + '">');

      return bgContainer;
    };

    $('.cropit-preview-background-container', '.image-editor').remove();
    $('.cropit-preview-image-container', '.image-editor').remove();
    $('.image-editor').cropit('destroy');
    $('.image-editor').cropit({
      imageState: {
        src: profileImage,
      },
      smallImage: 'allow',
      maxZoom: 2,
      imageBackground: true,
      onZoomChange: function() {
        if (!$Core.ProfilePhoto.firstZoom) {
          $Core.ProfilePhoto.modified = true;
        } else {
          $Core.ProfilePhoto.firstZoom = false;
        }
      },
      onOffsetChange: function() {
        if (!$Core.ProfilePhoto.firstOffset) {
          $Core.ProfilePhoto.modified = true;
        } else {
          $Core.ProfilePhoto.firstOffset = false;
        }
      }
    });

    $('.export').click(function() {
      var imageData = $('.image-editor').cropit('export');
      window.open(imageData);
    });
  },

  onSuccessUpload: function(ele, file, response) {
    if (typeof response === 'string') {
      $('#profile_crop_me').removeClass('profile-image-uploading profile-image-error');
      this.cropProfilePhoto(response);
    } else if(typeof response.run !== 'undefined') {
      eval(response.run);
    }
    this.enableCropFormButtons();
  },

  onAddedFile: function() {
    $('#profile_crop_me').addClass('profile-image-uploading').removeClass('profile-image-error');
    this.showHiddenBox();
    this.disableCropFormButtons();
  },

  onError: function() {
    $('#profile_crop_me').removeClass('profile-image-uploading');
    $('#profile_crop_me').addClass('profile-image-error');
    this.enableCropFormButtons();
  },

  update: function(photo) {
    if (photo === false) {
      $('#profile_crop_me').removeClass('profile-image-error');
      $('.dropzone-button', '#user-dropzone').trigger('click');
    } else {
      this.showHiddenBox();
      this.cropProfilePhoto(photo);
    }
  },

  showHiddenBox: function() {
    $Core.ProfilePhoto.modified = false;
    $Core.ProfilePhoto.firstZoom = $Core.ProfilePhoto.firstOffset = true;
    $('#profile_photo_form').show();
    $('body').css('overflow', 'hidden');
  },

  closeHiddenBox: function(ele) {
    $('#profile_photo_form .js_box_close a').closest('.js_box_holder').hide();
    $('body').css('overflow', 'auto');
  },

  save: function() {
    $('#update-profile-image-form').submit();
  },

  showError: function(message) {
    // error message
    $('[data-dz-errormessage]', '#user-dropzone').text(message);
    $('.dz-error-message', '#user-dropzone').insertAfter($('#user-dropzone'));

    $('#user-dropzone').addClass('dz-error');
    $('.dz-preview', '#user-dropzone').addClass('dz-error');
    $('#profile_crop_me').removeClass('profile-image-uploading').addClass('profile-image-error');
  },

  disableCropFormButtons: function() {
    $('#profile_crop_me .rotate_button input').prop('disabled', true);
    $('#profile_crop_me .rotate_button a').addClass('disabled');
  },

  enableCropFormButtons: function() {
    $('#profile_crop_me .rotate_button input').prop('disabled', false);
    $('#profile_crop_me .rotate_button a').removeClass('disabled');
  }
};

$(document).on('click', '.rotate-cw', function() {
  $('.image-editor').cropit('rotateCW');
  $Core.ProfilePhoto.modified = true;
});

$(document).on('click', '.rotate-ccw', function() {
  $('.image-editor').cropit('rotateCCW');
  $Core.ProfilePhoto.modified = true;
});

$Behavior.loadProfilePhotoForm = function() {
  if ($('#page_profile_index').length > 0 && $('#profile_photo_form').length === 0) {
    tb_show(oTranslations['update_profile_picture'], $.ajaxBox('profile.updateProfilePhoto', 'width=650'), null, null, null, null, true, 'profile_photo_form');

    $(document).on('click', '#profile-image-upload-again', function() {
      $('#profile_crop_me').removeClass('profile-image-error');
      $('.dropzone-button', '#user-dropzone').trigger('click');
    });

    $(document).on('click', '#profile_photo_form .js_box_close a', function() {
      if ($Core.ProfilePhoto.modified) {
        $Core.jsConfirm({
          'title': oTranslations['close'],
          'message': oTranslations['close_without_save_your_changes'],
          'btn_yes': oTranslations['close']
        }, function() {
          $Core.ProfilePhoto.closeHiddenBox();
        }, function() {});
      } else {
        $Core.ProfilePhoto.closeHiddenBox();
      }
    });
  }
};

$Core.checkAttachmentHolder = function() {
  $('.attachment_list').each(function() {
    var list = $(this);
    if (list.find('.attachment-row').length === 0) {
      list.closest('.attachment_holder_view').remove();
      $('.no-attachment', list).removeClass('hide');
    }
  });
}

//custom input range
$(document).on('input', '.cropit-btn-edit input', function() {
  $('.cropit-btn-edit input').css( 'background', 'linear-gradient(to right, #2681d5 0%, #2681d5 ' + this.value*100 + '%, #dcdcdc ' + this.value*100 + '%, #dcdcdc 100%)' );
  console.log(this.value);
});

var isFF = true;
var addRule = (function (style) {
  var sheet = document.head.appendChild(style).sheet;
  return function (selector, css) {
    if ( isFF ) {
      if ( sheet.cssRules.length > 0 ) {
        sheet.deleteRule( 0 );
      }

      try {
        sheet.insertRule(selector + "{" + css + "}", 0);
      } catch ( ex ) {
        isFF = false;
      }
    }
  };
})(document.createElement("style"));

addRule( '.cropit-btn-edit input::-moz-range-track', 'background: #dcdcdc' );

$(document).on('input', '.cropit-btn-edit input', function() {
  addRule( '.cropit-btn-edit input::-moz-range-track', 'background: linear-gradient(to right, #2681d5 0%, #2681d5 ' + this.value*100 + '%, #dcdcdc ' + this.value*100 + '%, #dcdcdc 100%)' );
} );
//end custom

//check ie fix input range
$Core.getIEVersion = function() {
  var sAgent = window.navigator.userAgent;
  var Idx = sAgent.indexOf("MSIE");

  // If IE, return version number.
  if (Idx > 0)
    return parseInt(sAgent.substring(Idx+ 5, sAgent.indexOf(".", Idx)));

  // If IE 11 then look for Updated user agent string.
  else if (!!navigator.userAgent.match(/Trident\/7\./))
    return 11;
  else
    return 0; //It is not IE
}
//end check

$Core.FriendRequest = {
  panel: {
    accept: function(requestId, message) {
      var requestRow = $('#drop_down_' + requestId, '#request-panel-body');

      $('.panel_rows_time', requestRow).text(message);
      $('.panel_action', requestRow).remove();
      requestRow.addClass('friend-request-accepted');

      // update counter
      $Core.FriendRequest.panel.descreaseCounter();

      setTimeout(function() {
        $('.panel_row', requestRow).slideUp(200, function() {
          requestRow.remove();
          $Core.FriendRequest.panel.checkAndClosePanel();
        });
      }, 2e3);
    },

    deny: function(requestId) {
      var requestRow = $('#drop_down_' + requestId, '#request-panel-body');

      // update counter
      $Core.FriendRequest.panel.descreaseCounter();

      $('.panel_row', requestRow).fadeOut(400, function() {
        requestRow.remove();
        $Core.FriendRequest.panel.checkAndClosePanel();
      });
    },

    descreaseCounter: function() {},

    checkAndClosePanel: function() {
      if ($('li', '#request-panel-body').length === 0) {
        $('#hd-request').trigger('click');
      }
    }
  },

  manageAll: {
    accept: function(requestId, message) {
      var requestRow = $('#request-' + requestId);

      $('.moderation_row', requestRow).css({
        visibility: 'hidden'
      });
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

$Core.dropdownTooltip = {
  tooltipElement: null,
  registeredClosing: null,
  timeout: 1e3,
  debug: false, // enable this when debugging

  openTooltip: function(tooltip) {
    this.debug('openTooltip');
    this.tooltipElement = tooltip;
    tooltip.addClass('open');
    $('a', tooltip).attr('aria-expanded', true);
    this.dismissClose();
  },

  closeTooltip: function() {
    this.debug('closeTooltip');
    this.tooltipElement.removeClass('open');
    $('a', this.tooltipElement).attr('aria-expanded', false);
  },

  registerClose: function(tooltip) {
    this.debug('registerClose');
    this.tooltipElement = tooltip;
    this.registeredClosing = setTimeout(function() {
      $Core.dropdownTooltip.closeTooltip();
    }, this.timeout);
  },

  dismissClose: function() {
    this.debug('dismissClose');
    clearTimeout(this.registeredClosing);
  },

  debug: function(msg) {
    if (!this.debug) {
      return;
    }
    console.log('$Core.dropdownTooltip:', msg);
  }
}

$(document).on('mouseover', '[data-component="dropdown-tooltip"] >a, [data-component="dropdown-tooltip"] >ul', function() {
  $Core.dropdownTooltip.openTooltip($(this).closest('[data-component="dropdown-tooltip"]'));
}).on('mouseout', '[data-component="dropdown-tooltip"] >a, [data-component="dropdown-tooltip"] >ul', function() {
  $Core.dropdownTooltip.registerClose($(this).closest('[data-component="dropdown-tooltip"]'));
});