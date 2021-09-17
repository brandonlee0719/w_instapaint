
var bAutoSaveSettings = false,
	aAdminCPSearchValues = new Array();

// disable ajax mode in admincp
oParams.bOffFullAjaxMode = true;

PF.cmd('admincp.open_sub_menu', function (ele, evt) {
    var li = ele.closest('li').toggleClass('open'),
		id = li.attr('id'),
		key =  'admin_open_sub_menu';
    li.hasClass('open')? setCookie(key, id):deleteCookie(key);
    evt.preventDefault();
	return false;
}).cmd('admincp.site_setting_remove_input', function (obj) {
    $Core.jsConfirm({}, function () {
        obj.closest('.p_4').remove();
    }, function () {
    	obj.closest('form').trigger('submit');
    });
}).cmd('admincp.site_setting_add_input', function (btn) {
    var holder = btn.closest('.js_array_holder'),
        sVarName = btn.data('rel'),
        sValue = $('.js_add_to_array', holder).val(),
        iCnt = (parseInt($('#js_array_count', holder).html()) + 1),
		form = btn.closest('form') ;

    $('.js_array_data', holder).append('<div class="p_4" id="js_array' + iCnt + '"><div class="input-group"><input class="form-control" value="' + sValue + '" type="text" name="' + sVarName + '" placeholder="Add a New Value..." size="30" /><span class="input-group-btn"><a role="button" class="btn btn-info" data-cmd="admincp.site_setting_remove_input"><i class="fa fa-remove"></a></span></div></div>');
    $('.js_array_count', holder).html(iCnt);
    $('.js_add_to_array', holder).val('').focus();

    if (form.attr('action') == '#') {
        form.trigger('submit');
    }
    else {
        $Core.processing();
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(e) {
                $('.ajax_processing').fadeOut();
            }
        });
    }
}).cmd('admincp.remove_user_image', function (btn) {
    $Core.jsConfirm('', function () {
        $.ajaxCall('user.deleteProfilePicture', 'id=' + btn.data('user-id'));
    }, function () {});
});
PF.cmd('admincp.ajax_menu',function(btn,evt){
    var href = btn.attr('href');
    btn.parent().children().removeClass('active');
    btn.addClass('active');
    evt.preventDefault();
    $.ajax({
        url: href,
        contentType: 'application/json',
        success: function(e) {
            $('#site_content').html(e.content).show();
            $('.breadcrumbs a:last').text(btn.text());
            $Core.loadInit();
        }
    });
});

function _admincp_load_content(customContent, contentIsLoaded, extraParams, appUrl){
    if (contentIsLoaded) {
        return true;
    }
    contentIsLoaded = true;
    // $('.apps_menu a[href="#"]').addClass('active');
    if (extraParams == 1) {
        // $('.apps_menu a[href="#"]').attr('href', appUrl).addClass('no_ajax');
    }

    $.ajax({
        url: customContent,
        contentType: 'application/json',
        success: function(e) {
            $('#app-content-holder').hide();
            $('#app-custom-holder').html(e.content).show();
            if(typeof e.title === 'string'){$('title').text(e.title);}
            if(typeof e.breadcrumb_menu === 'string'){
				$(e.breadcrumb_menu).appendTo('.breadcrumbs');
			}
            $Core.loadInit();
        }
    });
    return true;
}
$Behavior.tableHover = function()
{
	if ($('#_sort tbody').hasClass('ui-sortable')) {
		$('#_sort tbody').sortable('destroy');
	}
	$('#_sort tbody').sortable({
		handle: '.fa-sort',
		helper: 'clone',
		axis: 'y',
		stop: function(event, ui) {
			var ids = '';
			$('#_sort tr').removeClass('tr');
			$('#_sort tr').each(function(i, el) {
				var t = $(this);
				if (!t.data('sort-id')) {
					return;
				}

				if (i % 2 === 0) {

				}
				else {
					t.addClass('tr');
				}

				ids += t.data('sort-id') + ',';
			});

			// console.log(ui.item.find('> td:first-of-type').html());
			var t = ui.item.find('> td:first-of-type');
			t.find('i').hide();
			t.prepend('<i class="fa fa-spin fa-circle-o-notch"></i>');

			$('#public_message').remove();
			$.ajax({
				url: $('#_sort').data('sort-url'),
				type: 'POST',
				data: 'is_ajax_post=1&ids=' + ids,
				success: function() {
					t.find('.fa-spin').remove();
					t.find('i').show();

					$('body').prepend('<div id="public_message" class="public_message" style="display:block;">Order updated</div>');
					$Core.loadInit();
				}
			});
		}
	});

	$('table tr td:last-of-type .goJump').each(function() {
		var t = $(this), html = '';

		html = '<ul class="table_actions">';
		t.find('option').each(function() {
			var o = $(this);
			if (o.val().length > 2) {
				html += '<li><a href="' + o.val() + '">' + o.html() + '</a></li>';
			}
		});
		html += '</ul>';

		t.hide();
		t.parent().html(html);
	});

	if ($Core.exists('.table_hover_action')){
		$('#table_hover_action_holder').remove();
		$('body').append('<div id="table_hover_action_holder" style="display:none;"></div>');
		// $('#table_hover_action_holder').css("left", (($(window).width() - $('#table_hover_action_holder').outerWidth()) / 2) + $(window).scrollLeft() + "px");
		$('#table_hover_action_holder').html($('.table_hover_action').html());

		if (bAutoSaveSettings) {
			$('#table_hover_action_holder').addClass('hidden');
		}

		if (!isScrolledIntoView('.table_hover_action')){
			$('#table_hover_action_holder').show();
		}

		$(window).scroll(function(){
			if (isScrolledIntoView('.table_hover_action')){
				$('#table_hover_action_holder').hide();
			}
			else{
				$('#table_hover_action_holder').show();
			}
		});

		$('#table_hover_action_holder input').click(function(){
			$('.table_hover_action').append('<div><input type="hidden" name="' + $(this).attr('name') + '" value="' + $(this).attr('value') + '" /></div>')
			if ($('.table_hover_action').hasClass('table_hover_action_custom')){
				$Core.ajaxMessage();
				$($('.table_hover_action').parents('form:first')).ajaxCall('user.updateSettings');
				return false;
			}
			else{
				$('.table_hover_action').parents('form:first').submit();
			}
		});
	}

	$('#admincp_search_input').focus(function(){
		if (empty(aAdminCPSearchValues)){
			$.ajaxCall('admincp.buildSearchValues', '', 'GET');
		}

		if ($(this).val() == $('#admincp_search_input_default_value').html()){
			$(this).val('').addClass('admincp_search_input_focus');
		}
	});

	$('#admincp_search_input').blur(function(){
		if (empty($(this).val())){
			$(this).val($('#admincp_search_input_default_value').html()).removeClass('admincp_search_input_focus');
		}
	});

	$('#admincp_search_input').keyup(function(){
		if (!empty(aAdminCPSearchValues)){

			var iFound = 0;
			var oParent = $(this);
			var sHtml = '';

			if (empty(oParent.val())){
				$('#admincp_search_input_results').hide();
				return;
			}

			$(aAdminCPSearchValues).each(function(sKey, aResult){
				var mRegSearch = new RegExp(oParent.val(), 'i');

				if (aResult['title'].match(mRegSearch))
				{
					sHtml += '<li><a href="' + aResult['link'] + '">' + aResult['title'] + '<div class="extra_info">' + aResult['type'] + '</div></a></li>';
					iFound++;
				}

				if (iFound > 10){
					return false;
				}
			});

			if (iFound > 0){
				$('#admincp_search_input_results').html('<ul>' + sHtml + '</ul>');
				$('#admincp_search_input_results').show();
			}
			else{
				$('#admincp_search_input_results').hide();
			}
		}
	});

	$("#js_check_box_all").click(function()
  	{
   		var bStatus = this.checked;

   		if (bStatus)
   		{
   			$('.checkRow').addClass('is_checked');
   			$('.sJsCheckBoxButton').removeClass('disabled');
   			$('.sJsCheckBoxButton').prop('disabled', false);
   		}
   		else
   		{
   			$('.checkRow').removeClass('is_checked');
   			$('.sJsCheckBoxButton').addClass('disabled');
   			$('.sJsCheckBoxButton').prop('disabled', true);
   		}

   		$("input.checkbox").each(function()
   		{
    		this.checked = bStatus;
   		});
  	});

	$('th').click(function()
	{
		if (typeof($(this).find('a').get(0)) != 'undefined')
		{
			window.location.href = $(this).find('a').get(0).href;
		}
	});


	$('.text').click(function()
	{
		return false;
	});

    $('.checkbox').click(function()
    {
    	var sIdName = '#js_row' + $(this).get(0).id.replace('js_id_row', '');
    	if ($(sIdName).hasClass('is_checked'))
    	{
    		$(sIdName).removeClass('is_checked');
    	}
    	else
    	{
    		$(sIdName).addClass('is_checked');
    	}

    	var iCnt = 0;
   		$("input:checkbox").each(function()
   		{
    		if (this.checked)
    		{
   				iCnt++;
    		}
   		});

   		if (iCnt > 0)
   		{
   			$('.sJsCheckBoxButton').removeClass('disabled');
   			$('.sJsCheckBoxButton').attr('disabled', false);
   		}
   		else
   		{
   			$('.sJsCheckBoxButton').addClass('disabled');
   			$('.sJsCheckBoxButton').attr('disabled', true);
   		}
    });

    $('table tr td:last-of-type .goJump').each(function () {
        var t = $(this), html = '';

        html = '<ul class="table_actions">';
        t.find('option').each(function () {
            var o = $(this);
            if (o.val().length > 2) {
                html += '<li><a href="' + o.val() + '">' + o.html() + '</a></li>';
            }
        });
        html += '</ul>';

        t.hide();
        t.parent().html(html);
    });

    $('.checkbox').click(function()
    {
    	var sIdName = '#js_user_' + $(this).get(0).id.replace('js_id_row', '');
    	if ($(sIdName).hasClass('is_checked'))
    	{
    		$(sIdName).removeClass('is_checked');
    	}
    	else
    	{
    		$(sIdName).addClass('is_checked');
    	}

    	var iCnt = 0;
   		$("input:checkbox").each(function()
   		{
    		if (this.checked)
    		{
   				iCnt++;
    		}
   		});

   		if (iCnt > 0)
   		{
   			$('.sJsCheckBoxButton').removeClass('disabled');
   			$('.sJsCheckBoxButton').attr('disabled', false);
   		}
   		else
   		{
   			$('.sJsCheckBoxButton').addClass('disabled');
   			$('.sJsCheckBoxButton').attr('disabled', true);
   		}
    });

    $('.js_drop_down_link').click(function () {
        var eleOffset = $(this).offset();

        $('#js_drop_down_cache_menu').remove();

        $(this).parent().find('ul').addClass('dropdown-menu');
        $('body').prepend('<div id="js_drop_down_cache_menu" style="position:absolute; left:' + eleOffset.left + 'px; top:' + (eleOffset.top + 15) + 'px; z-index:9999;"><div class="link_menu dropdown open">' + $(this).parent().find('.link_menu:first').html() + '</div></div>');
        $Core.loadInit();

        $('#js_drop_down_cache_menu .link_menu').hover(function () {

            },
            function () {
                $('#js_drop_down_cache_menu').remove();
        });

        return false;
    });

	$('.link_menu a').click(function() {
		if ($(this).hasClass('popup')) {
			$('#js_drop_down_cache_menu').fadeOut();
		}
	});



    $('.form_select_active').hover(
        function () {
            $(this).addClass('form_select_is_active');
        },
        function () {
            if (!$(this).hasClass('is_selected_and_active')) {
                $(this).removeClass('form_select_is_active');
            }
        });

    $('.form_select_active').click(function () {
        $('.form_select').hide();
        $('.form_select_active').removeClass('is_selected_and_active').removeClass('form_select_is_active');
        $(this).addClass('form_select_is_active');
        $(this).parent().find('.form_select:first').width($(this).innerWidth()).show();
        $(this).addClass('is_selected_and_active');

        return false;
    });

    $('.form_select li a').click(function () {
        $(this).parents('.form_select:first').hide();
        $('.form_select_active').removeClass('is_selected_and_active').removeClass('form_select_is_active');
        $(this).parents('.form_select:first').parent().find('.form_select_active:first').html($(this).html());

        aParams = $.getParams(this.href);
        var sParams = '';
        for (sVar in aParams) {
            sParams += '&' + sVar + '=' + aParams[sVar] + '';
        }
        sParams = sParams.substr(1, sParams.length);

        $Core.ajaxMessage();
        $.ajaxCall(aParams['call'], sParams + '&global_ajax_message=true');

        return false;
    });

    $(document).click(function () {
        $('.form_select').hide();
        $('.form_select_active').removeClass('is_selected_and_active').removeClass('form_select_is_active');
    });
};

if (!oCore['core.enabled_edit_area']) {
    var editAreaLoader = {
        openFile: function (sId, oOptions) {
            $('#' + sId).val(oOptions['text']);
        }, getValue: function (sId) {
            return $('#' + sId).val();
        }, setFileEditedMode: function () {
            //
        }, closeFile: function () {
            //
        }
    };
}