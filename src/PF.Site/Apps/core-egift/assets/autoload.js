var core_egifts_onchangeDeleteCategoryType = function (type) {
    if(parseInt(type) === 3)
        $('#category_select').show();
    else
        $('#category_select').hide();
};

var core_egift_clear_preview = function () {
    $('#js_core_egift_preview').html('');
    $('#js_core_egift_id').val('');
};

$Ready(function() {
    $('.textarea-has-egift').on('click', function(){
        $(this).closest('.activity_feed_form_holder ').addClass('egift-focus');
    })
});