
$Behavior.announcement_admin_manage_init = function()
{
	$('#age_from').change(function()
	{
		if (!empty($(this).val()) && $('#age_to option:selected').val() != '' && parseInt($(this).val()) > parseInt($('#age_to option:selected').val()))
		{
            window.parent.sCustomMessageString = oTranslations['min_age_cannot_be_higher_than_max_age'];
            tb_show(oTranslations['notice'], $.ajaxBox('core.message', 'height=200&width=300'));
            $(this).val('');
		}
	});

	$('#age_to').change(function(){
		if (!empty($(this).val()) && $('#age_from option:selected').val() && parseInt($(this).val()) < parseInt($('#age_from option:selected').val()))
		{
            window.parent.sCustomMessageString = oTranslations['max_age_cannot_be_lower_than_the_min_age'];
            tb_show(oTranslations['notice'], $.ajaxBox('core.message', 'height=200&width=300'));
            $(this).val('');
		}
	});

	$('#js_is_user_group').change(function()
	{
		if ($(this).val() == 1)
		{
			$('#js_user_group').hide();
		}
		else if ($(this).val() == 2)
		{
			$('#js_user_group').show();
		}
	});

	if ($('#js_is_user_group').val() == 2)
	{
		$('#js_user_group').show();
	}

};