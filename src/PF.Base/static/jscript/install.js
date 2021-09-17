(function ($) {
    // this variable to run automation, do not remove
    window.currentStep =  '';

    $(document).on('submit', 'form', function (evt) {
        var t = $(this),
            step = t.attr('action').replace('#', '')
            ;

        if(t.hasClass('no_ajax')){
            return false;
        }
        if (t.hasClass('built')) {
            evt.preventDefault();
            return false;
        }
        t.addClass('built');
        showIndicator('Waiting...');

        runStep(step, 'POST', 1, t.serialize());

        return false;
    });

    $(document).on('click', '#license_selector a', function (evt) {
        var t = $(this);

        if (t.hasClass('premium')) {
            // $('#license_selector').hide();
            $('#license_selector').html('<div class="process"><i class="fa fa-spin fa-circle-o-notch"></i></div>');
            setTimeout(function () {
                $('#license_selector').hide();
                $('#client_details').fadeIn();
            }, 800);
        }
        else {
            $('#license_id, #license_key').val('techie');
            if (t.hasClass('trial')) {
                $('#license_trial').val(1);
            }
            $('#js_form').trigger('submit');
        }

        evt.preventDefault();
        return false;
    });

    var showIndicator =  function(msg){
        var form =  $('form'),
            indicator = $('<div class="process">'+msg+' <i class="fa fa-spin fa-circle-o-notch"></i></div>');
        form.hide();
        indicator.insertBefore(form);
    };

    var runStep = function (step, type, timeout, data) {
        $('.error, .alert-danger').remove();
        setTimeout(function () {
            var isUpgrade = '';
            if ($('#is-upgrade').length) {
                isUpgrade = '&phpfox-upgrade=1';
            }
            $.ajax({
                url: BasePath + '?step=' + step + isUpgrade,
                type: (type ? type : 'GET'),
                data: data,
                timeout: 600e3,
                error: function (e) {
                    // $('html').html(e.responseText);
                    document.open();
                    document.write(e.responseText);
                    document.close();
                },
                success: function (e) {
                    $('form').show();
                    $('.process').remove();
                    if (typeof(e.next) == 'string') {
                        if (typeof(e.message) == 'string') {
                            showIndicator(e.message);
                        }else{
                            showIndicator('Waiting...');
                        }
                        runStep(e.next, 'GET', timeout, (typeof(e.extra) == 'string' ? e.extra : ''));
                    }
                    else if (typeof(e.content) == 'string') {
                        $('#installer').html(e.content);
                    }
                    else if (typeof(e.errors) == 'object') {
                        $('form').removeClass('built');
                        $('.alert-danger').remove();
                        $('#installer .panel-body').prepend('<div class="alert alert-danger">' + e.errors.join('<br/>') + '</div>');
                    } else {
                        location.href = BasePath + 'PF.Base/install/500.php?t=0' + isUpgrade;
                    }
                }
            }).always(function(){
                window.currentStep = step;
            });
        }, (timeout ? timeout : 0));
    };

    $(document).ready(function () {
        if (!$('.process').length) {
            console.log('Requirements did not pass...');
            return;
        }
        runStep('key','GET');
    });
})(jQuery);