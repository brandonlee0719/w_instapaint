
$Behavior.countryIsoChange = function()
{
	$('#country_iso').change(function()
	{
		var sChildValue = $('#js_country_child_id_value').val(),
			sExtra = '';

		$('#js_country_child_id').html('');
		$('#country_iso').after('<span id="js_cache_country_iso">' + $.ajaxProcess('no_message') + '</span>');
		if ($('#js_country_child_is_search').length > 0)
		{
			sExtra += '&country_child_filter=true';
		}

		if($('#js_admincp_search_options').length){
            sExtra += '&admin_search=true';
		}

		$.ajaxCall('core.getChildren', 'country_iso=' + this.value + '&country_child_id=' + sChildValue + sExtra, 'GET');
	});	
}