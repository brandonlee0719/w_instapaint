
function flavor_start() {
	$('.fm_save .fa-check').hide();
	$('.fm_save .fa-spin').show();
	$('.fm_save').show();
	$('.fm_sub_menu_title > button').hide();
}

function flavor_end(show_button) {
	Theme_Manager.success(show_button);
	var s = document.getElementById('fm_iframe').src;
	document.getElementById('fm_iframe').src = s;
}

function flavor_alert(msg){
	alert(atob(msg));
	flavor_end();
}

var Theme_Manager = {
	design: function() {
		this.success();
	},

	success: function(show_button) {
		$('.fm_save .fa-spin').hide();
		// $('.fm_save .fa-check').fadeIn();
		if (show_button === true) {
			$('.fm_sub_menu_title > button').show();
		}

		$('.fm_save').fadeOut();
	},

	icon: function(icon) {
		$('.theme_icon').data('src', icon + '?v=' + new Date().getMilliseconds()).removeClass('built').removeClass('has_image');

		this.success();
		$Core.loadInit();
	},

	logo: function(params) {
		var logo = params.logo;
		if (params.type == 'favicons') {
			logo = params.favicon;
		}
		$('.fm_' + params.type).data('src', logo).removeClass('built').removeClass('has_image');

		this.reload();
		this.success();
	},

	default_photo: function(params) {
		var file = params.file;
		if (file == ''){
			$('.fm_' + params.type).css('background-image', 'none');
			$('.fm_' + params.type).find('a.remove-btn').hide();
		}
		else {
			$('.fm_' + params.type).data('src', file).removeClass('built').removeClass('has_image');
			$('.fm_' + params.type).find('a.remove-btn').show();
		}

		this.reload();
		this.success();
	},

	banner: function(params) {
		document.getElementById('fm_iframe').src = $('.fm_content').data('url') + '&preview=1&image=' + params.banner;
		$('.fm_banners').prepend('<div class="image_load" data-src="' + decodeURIComponent(params.banner) + '"><span><i class="fa fa-remove"></i></span></div>');
		this.success();
		$Core.loadInit();
	},

	reload: function() {
		var s = document.getElementById('fm_iframe').src;
		document.getElementById('fm_iframe').src = s;
		$Core.loadInit();
	}
};

$Ready(function() {
	var c = $('.fm_content'), l = $('.fm_loader');
	if (!c.length) {
		return;
	}

	if (!c.hasClass('built')) {
		c.addClass('built');
		c.append('<iframe src="' + c.data('url') + '" id="fm_iframe" name="fm_iframe"></iframe>');
	}

	$('.fm_banners i').click(function() {
		var t = $(this).parents('div:first'), src = t.data('src');

		flavor_start();
		t.remove();
		$.ajax({
			url: PF.url.make('/flavors/manage', {id: $('.fm_banners').data('flavor-id'), type: 'delete_banner'}),
			data: 'banner=' + src,
			type: 'POST',
			success: function() {
				flavor_end();
			}
		});
	});

	$('.fm_default_photo a.remove-btn').click(function() {
		var url = $(this).data('url');
		flavor_start();
		$.ajax({
			url: url,
			type: 'POST',
			success: function(e) {
				flavor_end();
				if (typeof(e.run) == 'string') {
					eval(e.run);
				}
			}
		});
	});

	$('#fm_iframe').bind('load', function() {
		l.fadeOut();
	});

	$('.fm_submit').click(function() {
		flavor_start();
		$('.fm_submit').hide();
		$(this).parents('form:first').trigger('submit');
	});

	$('.fm_content_textarea').keyup(function() {
	  if ($(this).val()) {
      $('#fm_iframe').contents().find('#welcome_message').removeClass('hide');
      $('#fm_iframe').contents().find('.custom_flavor_content').html($(this).val());
    } else {
      $('#fm_iframe').contents().find('#welcome_message').addClass('hide');
    }
	});

	$('.fm_responsive > span').click(function() {
		var t = $('.fm_responsive');
		if (t.hasClass('built')) {
			t.removeClass('built');
			t.height('40px');
			$(this).find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
		} else {
			t.addClass('built');
			t.height('auto');
			$(this).find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
		}
	});

	$('.fm_content_textarea').blur(function() {
		flavor_start();
		$.ajax({
			url: $(this).data('url'),
			type: 'POST',
			data: 'content=' + $(this).val(),
			success: function(e) {
				Theme_Manager.success();
			}
		});
	});

	$('.fm_setting ._colorpicker:not(.c_built)').each(function() {
		var t = $(this),
			h = t.parent().find('._colorpicker_holder');

		t.addClass('c_built');
		h.css('background-color', t.val());

		h.colpick({
			layout: 'hex',
			submit: false,
			onChange: function(hsb,hex,rgb,el,bySetColor) {
				t.val('#' + hex);
				h.css('background-color', '#' + hex);
				t.trigger('change');
			},
			onHide: function() {
				t.trigger('change');
			}
		});
	});

	$('.fm_setting input').change(function() {
		var t = $(this), v = t.val(), r = t.data('rules');

		$('.fm_submit').show().find('span').addClass('active');
		$('head', window.frames['fm_iframe'].document).append('<style>' + r.rule.replace('[VALUE]', t.val()) + '</style>');
	});

	$('.fm_setting input').click(function() {
		$(this).select();
	});

	$('.fm_responsive > a').click(function() {
		var t = $(this);

		$('.fm_responsive a.active').removeClass('active');
		t.addClass('active');
		$('body').removeClass('fm_responsive_mobile').removeClass('fm_responsive_tablet');
		if (t.data('type') == 'desktop') {
			return false;
		}
		$('body').addClass('fm_responsive_' + t.data('type'));
		// reload iframe
        $('#fm_iframe')[0].contentWindow.location.reload(true);

        return false;
	});

	$('.fm_sub_menu .fa-chevron-left').click(function() {
		$('.fm_sub_menu').animate({
			left: 270
		}, 'fast');

		$('.fm_menu').animate({
			'margin-left': 0
		}, 'fast');

		$('.ace_editor_holder').hide();
		$('.fm_submit').hide();
	});

	$('.fm_sub_menu_title > button').click(function() {
		var t = $('.ace_editor');

		flavor_start();
		$.ajax({
			url: t.data('ace-save'),
			type: 'POST',
			data: 'is_ajax_post=1&content=' + encodeURIComponent($AceEditor.obj.getSession().getValue()),
			success: function(e) {
				flavor_end(true);
				if (typeof(e.run) == 'string') {
					eval(e.run);
				}
			}
		});
	});

	$('.fm_menu a, a.edit_for_theme').click(function() {
		var t = $(this);

		if (t.hasClass('skip')) {
			return true;
		}

		l.fadeIn();
		t.find('.fa-chevron-right').removeClass('fa-chevron-right').addClass('fa-spin').addClass('fa-circle-o-notch');
		$.ajax({
			url: t.data('url'),
			success: function(e) {
				if (e.type == 'homepage') {
					document.getElementById('fm_iframe').src = $('.fm_content').data('url') + '&preview=1';
				} else {
					l.fadeOut();
				}

				$('.fm_sub_menu').animate({
					left: 0
				}, 'fast');

				$('.fm_menu').animate({
					'margin-left': '-' + 270
				}, 'fast');

				t.find('.fa-spin').removeClass('fa-spin').removeClass('fa-circle-o-notch').addClass('fa-chevron-right');

				$('.fm_sub_menu_content').html(e.html);
				$('.fm_sub_menu_title > span').html(e.title);

				if (typeof(e.ace) == 'string') {
					$('.ace_editor').data('ace-mode', e.mode);
					$('.ace_editor').data('ace-save', e.save);
					$('.ace_editor_holder').show();
					$AceEditor.set(e.ace);
					$('.fm_sub_menu_title > button').show();
				}
				else {
					$('.ace_editor_holder').hide();
					$('.fm_sub_menu_title > button').hide();
				}

				$Core.loadInit();
			}
		});

		return false;
	});
});