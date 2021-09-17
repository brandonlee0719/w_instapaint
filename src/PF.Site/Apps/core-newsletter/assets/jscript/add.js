$Core.Newsletter = {
    showPlain: function () {
        var sText = Editor.getContent();
        $.ajaxCall('newsletter.showPlain', 'sText=' + $("<p />").html(sText).text());
    },
    checkText: function () {
        if ($('textarea#text').val().length < 1 && $('textarea#txtPlain').val().length > 0) {
            $('textarea#text').val($('textarea#txtPlain').val());
        }
        close_warning_enabled = false;
        close_warning_checked = false;
        $('#frmNewsletter').submit();
    }
};

$Behavior.newsletter_add_init = function () {
    $('#age_from').change(function () {
        if (!empty(this.value) && $('#age_to option:selected').val() != '' && this.value > $('#age_to option:selected').val()) {
            $(this).val('');
            window.parent.sCustomMessageString = oTranslations['min_age_cannot_be_higher_than_max_age'];
            tb_show(oTranslations['notice'], $.ajaxBox('core.message', 'height=200&width=300'));
        }
    });

    $('#age_to').change(function () {
        if (!empty(this.value) && $('#age_from option:selected').val() && $(this).val() < $('#age_from option:selected').val()) {
            $(this).val('');
            window.parent.sCustomMessageString = oTranslations['max_age_cannot_be_lower_than_the_min_age'];
            tb_show(oTranslations['notice'], $.ajaxBox('core.message', 'height=200&width=300'));
        }
    });


    $('#js_is_user_group').change(function () {
        if (this.value == 1) {
            $('#js_user_group').hide();
        }
        else if (this.value == 2) {
            $('#js_user_group').show();
        }
    });

    if ($('#js_is_user_group').val() == 2) {
        $('#js_user_group').show();
    }
};
