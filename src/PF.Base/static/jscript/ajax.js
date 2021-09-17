/**
 * Creates an AJAX call using jQuery.load()
 * Data is inserted into DOM
 *
 * @param {String} sCall Name of the Component
 * @param {String} sExtra Extra params we plan to pass
 */
$.ajaxBox = function (sCall, sExtra) {
    var sParams = getParam('sJsAjax') + '?' + getParam('sGlobalTokenName') + '[ajax]=true&' + getParam('sGlobalTokenName') + '[call]=' + sCall;
    if (sExtra) {
        sParams += '&' + sExtra;
    }

    if (!sParams.match(/\[security_token\]/i)) {
        sParams += '&' + getParam('sGlobalTokenName') + '[security_token]=' + oCore['log.security_token'];
    }

    return sParams;
};

var oCacheAjaxRequest = null;

window.onbeforeunload = function () {
    if (oCacheAjaxRequest !== null) {
        oCacheAjaxRequest.abort();
    }
};

/**
 * Create AJAX Call
 *
 * @param    {string}    sCall    Name of the function we plan to use
 * @param    {string}    sExtra
 * @param    {string}    sType
 * @param    {boolean}   bNoForm
 * @param    {object}    callback
 */
$.fn.ajaxCall = function (sCall, sExtra, bNoForm, sType, callback) {
    if (typeof sCall == 'undefined') {
        return;
    }

    if (empty(sType)) {
        sType = 'POST';
    }

    switch (sCall) {
        case 'share.friend':
        case 'share.email':
        case 'share.post':
            sType = 'POST';
            break;
        default:
            break;
    }

    var sUrl = getParam('sJsAjax'),
        sParams;
    if (sCall.substr(0, 7) == 'http://' || sCall.substr(0, 8) == 'https://') {
        sUrl = sCall;
        sParams = this.getForm();
    }
    else {
        sParams = '&' + getParam('sGlobalTokenName') + '[ajax]=true&' + getParam('sGlobalTokenName') + '[call]=' + sCall + '' + (bNoForm ? '' : this.getForm());
        if (sExtra) {
            sParams += '&' + ltrim(sExtra, '&');
        }

        if (!sParams.match(/\[security_token\]/i)) {
            sParams += '&' + getParam('sGlobalTokenName') + '[security_token]=' + oCore['log.security_token'];
        }

        sParams += '&' + getParam('sGlobalTokenName') + '[is_admincp]=' + (oCore['core.is_admincp'] ? '1' : '0');
        sParams += '&' + getParam('sGlobalTokenName') + '[is_user_profile]=' + (oCore['profile.is_user_profile'] ? '1' : '0');
        sParams += '&' + getParam('sGlobalTokenName') + '[profile_user_id]=' + (oCore['profile.user_id'] ? oCore['profile.user_id'] : '0');
    }

    var params = {
        type: sType,
        url: sUrl,
        dataType: "script",
        data: sParams
    };
    var self = this;
    if (typeof(callback) == 'function') {
        params.success = function (e) {
            callback(e, self);
        };
    }
    oCacheAjaxRequest = $.ajax(params);
    return oCacheAjaxRequest;
};

$.ajaxCall = function (sCall, sExtra, sType) {
    if ($('body').hasClass('page-loading')) return false;
    return $.fn.ajaxCall(sCall, sExtra, true, sType);
};

$.ajaxCallOne = function (obj, sCall, sExtra, sType) {
    if ($('body').hasClass('page-loading')) return false;
    var processing = $(obj).data('processing');
    if (processing == true)
        return false;
    $(obj).data('processing', true);
    return $.fn.ajaxCall(sCall, sExtra, true, sType, function () {
        $(obj).data('processing', false)
    });
};

/**
 * Get form details
 * @param    string    frm    Form ID or Element ID
 * @return    string    Return parsed URL string
 */
$.fn.getForm = function () {
    var objForm = this.get(0);
    var prefix = "";
    var submitDisabledElements = false;

    if (arguments.length > 1 && arguments[1] == true) {
        submitDisabledElements = true;
    }

    if (arguments.length > 2) {
        prefix = arguments[2];
    }

    var sXml = '';
    if (objForm && objForm.tagName == 'FORM') {
        var formElements = objForm.elements;
        for (var i = 0; i < formElements.length; i++) {
            if (!formElements[i].name) {
                continue;
            }

            if (formElements[i].name.substring(0, prefix.length) != prefix) {
                continue;
            }

            if (formElements[i].type && (formElements[i].type == 'radio' || formElements[i].type == 'checkbox') && formElements[i].checked == false) {
                continue;
            }

            if (formElements[i].disabled && formElements[i].disabled == true && submitDisabledElements == false) {
                continue;
            }

            var name = formElements[i].name;
            if (name) {
                sXml += '&';
                if (formElements[i].type == 'select-multiple') {
                    for (var j = 0; j < formElements[i].length; j++) {
                        if (formElements[i].options[j].selected == true) {
                            sXml += name + "=" + encodeURIComponent(formElements[i].options[j].value) + "&";
                        }
                    }
                }
                else {
                    sXml += name + "=" + encodeURIComponent(formElements[i].value);
                }
            }
        }
    }

    if (!sXml && objForm) {
        sXml += "&" + objForm.name + "=" + encodeURIComponent(objForm.value);
    }

    return sXml;
};

$Core.processPostForm = function (e, obj) {
    if (typeof(e.append) == 'object') {
        $(e.append.to).append(e.append.with);
        $Core.loadInit();
    }

    if (typeof(e.prepend) == 'object') {
        $(e.prepend.to).prepend(e.prepend.with);
        $Core.loadInit();
    }

    if (typeof(e.html) == 'object') {
        $(e.html.to).html(e.html.with);
        $Core.loadInit();
    }

    if (typeof(e.error) == 'string') {
        obj.prepend(e.error);
        obj.find('.btn-primary').attr('disabled', false);
    }

    if (obj instanceof jQuery) {
        if (obj.data('callback')) {
            eval('' + obj.data('callback') + '(e, obj);');
        }
    }

    if (typeof(e.redirect) == 'string') {
        window.location.href = e.redirect;
    }

    if (typeof(e.push) == 'string') {
        history.pushState(null, null, e.redirect);
    }

    if (typeof(e.run) == 'string') {
        eval(e.run);
    }

    if (typeof(e.ace) == 'string') {
        $AceEditor.set(e.ace);
    }
};

$Behavior.onAjaxSubmit = function () {
    $('.moxi9:not(.built)').each(function () {
        var t = $(this);

        t.addClass('built');
        $.ajax({
            url: getParam('sJsHome'),
            data: 'm9callback=' + t.data('call') + '&current=' + encodeURIComponent(window.location.href),
            success: function (e) {
                if (typeof(e.error) == 'string') {

                    t.html('<div class="error_message">' + e.error + '</div>');

                    return;
                }

                t.data('json', e).addClass('success');
                $Core.loadInit();
            }
        });
    });

    $('div.ajax:not(.built)').each(function () {
        var t = $(this);
        t.html('<i class="fa fa-spin fa-circle-o-notch"></i>');
        t.addClass('built');
        $.ajax({
            url: t.data('url'),
            data: 'is_ajax_get=1',
            success: function (e) {
                // $Core.processPostForm(e, t);
                t.html(e);
                t.fadeIn();
                $Core.loadInit();
            }
        });
    });

    $('a.ajax').click(function () {
        var t = $(this),
            url = (t.data('url') ? t.data('url') : t.attr('href'));

        if (t.data('add-class')) {
            t.addClass(t.data('add-class'));
        }

        if (t.data('add-spin')) {
            t.parent().prepend('<i class="fa fa-spin fa-circle-o-notch"></i>');
        }

        if (t.data('add-process')) {
            $Core.processing();
        }

        $.ajax({
            url: url,
            contentType: 'application/json',
            data: 'is_ajax_get=1',
            success: function (e) {
                if (t.data('add-process')) {
                    $Core.processingEnd();
                }
                if (t.hasClass('reload')) {
                    $Core.reloadPage();
                }
                $Core.processPostForm(e, t);
            }
        });

        return false;
    });

    $('.button,.btn').click(function () {
        $('.last_clicked_button').removeClass('last_clicked_button');
        $(this).addClass('last_clicked_button');
    });
    $('.ajax_post').off('submit').submit(function () {
        var t = $(this),
            callback = t.data('callback'),
            callbackStart = t.data('callback-start'),
            includeButton = t.data('include-button');

        t.find('.form-spin-it').remove();
        var b = t.find('.button');
        if (t.data('add-spin')) {
            if (!b.length) {
                b = t.find('input.btn');
            }

            b.before('<span class="form-spin-it"><i class="fa fa-spin fa-circle-o-notch"></i></span>');
            b.hide();
        }

        if (callbackStart) {
            window[callbackStart](t);
        }

        var data = t.serialize();
        if (includeButton) {
            data += '&' + $('.last_clicked_button').attr('name') + '=1';
        }

        t.find('.error_message').remove();

        $.ajax({
            url: t.attr('action'),
            type: 'POST',
            data: data + '&is_ajax_post=1',
            success: function (e) {
                $('.button.last_clicked_button').removeClass('last_clicked_button');
                b.show();
                t.find('.form-spin-it').remove();
                // t.find('.button').attr('disabled', false);
                if (t.data('fade-out-error')) {
                    setTimeout(function () {
                        t.find('.error_message').fadeOut();
                    }, 1000);
                }

                $Core.processPostForm(e, t);
                if (callback) {
                    // window[callback](e, t, t.serializeArray());
                }
            }
        });

        return false;
    });

    $('.on_enter_submit').off('keydown').keydown(function (e) {
        if (e.which == 13) {
            var $this = $(this),
                $form =  $this.closest('form'),
                value = $(this).val();

            e.preventDefault();
            value = $.trim(value);

            if (value == '' && $('.js_attachment_list_holder').children('.attachment_holder').length == 0) {
                var holder = $(this).parents('#js_main_mail_thread_holder:first');
                if (holder.length > 0) {
                    holder.find('.mail_messages:first').prepend('<div id="js_ajax_compose_error_message"><div class="error_message">' + getPhrase('can_not_send_empty_message') + '</div></div>');
                    setTimeout(function () {
                        holder.find('#js_ajax_compose_error_message').fadeOut();
                    }, 2000);
                }
                else {
                    var boxContent = $(this).parents('.js_box_content:first');
                    if (boxContent.length > 0) {
                        var errorMessage = boxContent.find('#js_ajax_compose_error_message:first');
                        if (errorMessage) errorMessage.html('<div class="error_message">' + getPhrase('can_not_send_empty_message') + '</div>');
                    }
                }
                return false;
            }
            // user must enter captcha for send message
            if ($('.captcha_holder',$form).length > 0) {
                if ($('#image_verification').val() == '') {
                    return false;
                }
            }
            $form.trigger('submit');
            $this.val('');
            $('.js_attachment',$form).val('');
            return false;
        }
    });
};

var $AceEditor = {
    obj: null,
    set: function (value) {
        $AceEditor.obj.getSession().setValue(value);
    },
    mode: function (mode) {
        $AceEditor.obj.getSession().setMode('ace/mode/' + mode);
    }
};


$Core.upload = {
    listen: function (obj) {

        var f_input = obj.get(0);

        f_input.addEventListener("dragover", $Core.upload._prevent, false);
        f_input.addEventListener("dragleave", $Core.upload._prevent, false);
        f_input.addEventListener("drop", function () {
            $Core.upload._upload(obj, this);
        }, false);

        f_input.addEventListener("change", function () {
            $Core.processing();
            $Core.upload._upload(obj, this);
        }, false);
    },

    file: function (obj, file) {
        var data = new FormData();
        data.append('ajax_upload', file);

        if (obj.data('onstart')) {
            var thisFunction = window[obj.data('onstart')];
            thisFunction();
        }

        var url = obj.data('url');
        if (obj.hasClass('ajax_upload_actions')) {
            obj = obj.parents('form:first');
            obj.find('.error_message').remove();
        }

        if (obj.data('custom')) {
          var custom = obj.data('custom').split('&');
          for (var i = 0; i < custom.length; i++) {
            var customData = custom[i].split('=');
            data.append(customData[0], customData[1]);
          }
        }

        $.ajax({
            url: url,
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            headers: {
                'X-File-Name': encodeURI(file.name),
                'X-File-Size': file.size,
                'X-File-Type': file.type
            },
            type: 'POST',
            success: function (data) {
                $Core.processingEnd();
                $Core.processPostForm(data, obj);
            },
            error: function() {
                if (url.match('user/photo') != null) {
                    window.parent.sCustomMessageString = oTranslations['upload_failed_please_try_uploading_valid_or_smaller_image'];
                    changingProfilePhoto();
                    tb_show('Error', $.ajaxBox('core.message', 'height=150&width=300'));
                }
            }
        });
    },

    _upload: function (obj, _this) {
        if (typeof _this.files !== "undefined") {
            for (var i = 0, l = _this.files.length; i < l; i++) {
                $Core.upload.file(obj, _this.files[i]);
            }
        }
    },

    _prevent: function (e) {
        e.stopPropagation();
        e.preventDefault();
    }
};

$Ready(function () {
    $('.ajax_upload:not(.built)').each(function () {
        $(this).addClass('built');

        $Core.upload.listen($(this));
    });
});

$Ready(function () {
    $('.ace_editor:not(.built)').each(function () {
        var t = $('.ace_editor');

		t.submit(function() {
			t = $(this);
			$(this).find('.error_message').remove();
			if ($(this).attr('action') == '#') {
				return true;
			}
			$Core.processing();
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				data: $(this).serialize(),
				success: function(e) {
					if (typeof(e.error) == 'string') {
						t.prepend('<div class="error_message">' + e.error + '</div>');
					}
					if (e.updated && e.message) {
						if ($('#public_message').length == 0) {
							$('#main').prepend('<div class="public_message" id="public_message"></div>');
						}
						$('#public_message').html(e.message);
						$Behavior.addModerationListener();
					}
					$('.ajax_processing').fadeOut();
				}
			});
			return false;
		});

	});

	if ($('.ajax_upload').length) {

		$('.ajax_upload:not(.built)').each(function() {
			$(this).addClass('built');

			$Core.upload.listen($(this));
		});
	}
});

$Ready(function() {
    if($('#page_editor').length){
        $('#page_editor').height($(window).height()-20);
    }
	$('.ace_editor:not(.built)').each(function() {
		var t = $('.ace_editor');

		t.addClass('built');
    $.getScript('//cdn.jsdelivr.net/ace/1.1.8/min/ace.js', function() {
      var text = t.html();
      text = $('<div/>').html(text).text();
      $AceEditor.obj = ace.edit(t.get(0));
      $AceEditor.obj.getSession().setValue(text);
      $AceEditor.obj.getSession().setUseWorker(false);
      $AceEditor.obj.setTheme('ace/theme/github');
      $AceEditor.obj.getSession().setMode('ace/mode/' + t.data('ace-mode'));

			if (t.data('ace-save')) {
				$AceEditor.obj.commands.addCommand({
					name: 'saveFile',
					bindKey: {
						win: 'Ctrl-S',
						mac: 'Command-S',
						sender: 'editor|cli'
					},
					exec: function(env, args, request) {
						var data = '';
						if (t.data('form-data')) {
							data = $(t.data('form-data')).serialize() + '&';
						}

						if (t.data('onstart')) {
							var thisFunction = window[t.data('onstart')];
							thisFunction();
						}
						$.ajax({
							url: t.data('ace-save'),
							type: 'POST',
							data: data + 'is_ajax_post=1&content=' + encodeURIComponent($AceEditor.obj.getSession().getValue()),
							success: function(e) {
								if (t.data('onend')) {
									var thisFunction = window[t.data('onend')];
									thisFunction();
								}

								if (typeof(e.run) == 'string') {
									eval(e.run);
								}
							}
						});
					}
				});
			}
		}).done(function () {
            $('input[type=submit]').attr('disabled', false);
        });
	});
});